<?php

namespace Tollbridge\Paywall;

use DateTime;
use Exception;
use Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException;
use Tollbridge\Paywall\Exceptions\NoPlansExistException;
use Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException;
use Tollbridge\Paywall\Frontend\Article;
use Tollbridge\Paywall\Settings\Config;
use WP_Post;

/**
 * Class to interface with underlying data, and make presentation / calculation logic simpler.
 */
class Manager {

    public const AUTHENTICATION_CALLBACK_SLUG = 'tollbridge-callback';
    public const PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS = 2;
    public const PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ALL = 1;
    public const PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ONLY_LOGGED_IN_USERS = 0;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $applicable_plans_cache = null;

    /**
     * @var array
     */
    private $user_types_with_bypass_cache = null;

    public function __construct() {
        $this->client = Client::getInstance();
    }

    public function getAppId(): string {
        return trim( get_option( 'tollbridge_app_id' ) );
    }

    public function getClientId(): string {
        return trim( get_option( 'tollbridge_client_id' ) );
    }

    public function getClientSecret(): string {
        return trim( get_option( 'tollbridge_client_secret' ) );
    }

    public function allAccountSettingsAreEntered(): bool {
        // Ensure that all 3 key fields are entered and non-empty.
        $fields = [
            $this->getAppId(),
            $this->getClientId(),
            $this->getClientSecret(),
        ];

        return count( array_filter( $fields ) ) === 3;
    }

    public function getDefaultConfigBase(): string {
        return 'config.tollbridge.co';
    }

    public function getConfigBase(): string {
        return trim( get_option( 'tollbridge_config_base' ) ) ?: 'config.tollbridge.co';
    }

    public function getDefaultCallbackUrl(): string {
        return get_home_url() . '/' . self::AUTHENTICATION_CALLBACK_SLUG;
    }

    public function getCallbackUrl(): string {
        return trim( get_option( 'tollbridge_callback_url' ) ) ?: ( get_home_url() . '/' . self::AUTHENTICATION_CALLBACK_SLUG );
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     */
    public function getConfig( $key, $default = null ) {
        $config = $this->client->getConfig();

        return array_key_exists( $key, $config ) ? $config[ $key ] : $default;
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     * @throws NoPlansExistException
     */
    public function getActivePlans() {
        return $this->client->getPlans();
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     */
    public function isTrendingArticleActive(): bool {
        $config = $this->client->getConfig();

        return array_key_exists( 'trending_article', $config ) && $config['trending_article'] == '1';
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     */
    public function getPaywallTemplate() {
        $config = $this->client->getConfig();

        return $config['paywall_widget_style'] ?? null;
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     */
    public function getAmpViews() {
        return $this->client->getViews();
    }

    public function accountSettingsCanBeAuthenticated(): bool {
        try {
            $this->client->getAccessToken();
        } catch ( Exception $e ) {
            return false;
        }

        return true;
    }

    /**
     * Get the list of plans which are applicable to the given post.
     *
     * @throws Exception
     */
    public function getApplicablePlans( WP_Post $post ): array {
        if ( ! is_null( $this->applicable_plans_cache ) ) {
            return $this->applicable_plans_cache;
        }

        $article = new Article();
        $article->setId( $post->ID );

        if ( $this->globalSettingsAreActive() && ! $article->hasMetaOverride() ) {
            $config              = new Config();
            $plans               = $config->getGlobalPlansWithAccess();
            $hasTimeAccessChange = $config->getGlobalTimeAccessChange();

            if ( $hasTimeAccessChange ) {
                $timeAccessDays      = $config->getGlobalTimeAccessDelay();
                $timeAccessDirection = $config->getGlobalTimeAccessChangeDirection();
            }
        } else {
            // Get plans from article
            $plans               = $article->getPlansWithAccess();
            $hasTimeAccessChange = $article->getTimeAccessChange();

            if ( $hasTimeAccessChange ) {
                $timeAccessDays      = $article->getTimeAccessDelay();
                $timeAccessDirection = $article->getTimeAccessChangeDirection();
            }
        }

        // We have plan ids - need to hydrate them with plan names!
        $planList      = $this->client->getPlans();
        $hydratedPlans = [];

        foreach ( $plans as $id ) {
            if ( ! empty( $planList[ $id ] ) ) {
                $hydratedPlans[] = [
                    'id'   => $id,
                    'plan' => $planList[ $id ],
                ];
            }
        }

        if ( empty( $hydratedPlans ) ) {
            $this->applicable_plans_cache = [];

            return [];
        }

        $this->applicable_plans_cache = $hydratedPlans;

        // No time filters applied? Return now.
        if ( ! $hasTimeAccessChange ) {
            return $this->applicable_plans_cache;
        }

        $now       = new DateTime();
        $published = new DateTime( $post->post_date );
        $diff      = $now->diff( $published );

        // Enough days passed to trigger time logic
        if ( $diff->days >= $timeAccessDays ) {
            // Are we going from paid to free?
            if ( $timeAccessDirection === Config::ACCESS_CHANGE_PAID_TO_FREE ) {
                // No restriction!
                $this->applicable_plans_cache = [];
            }
        } else {
            // Starting free before going paid later
            if ( $timeAccessDirection === Config::ACCESS_CHANGE_FREE_TO_PAID ) {
                $this->applicable_plans_cache = [];
            }
        }

        return $this->applicable_plans_cache;
    }

    /**
     * Get the list of user type slugs which are permitted to bypass the paywall.
     */
    public function getUserTypesWithBypass(): array {
        if ( ! is_null( $this->user_types_with_bypass_cache ) ) {
            return $this->user_types_with_bypass_cache;
        }

        $config                             = new Config();
        $this->user_types_with_bypass_cache = $config->getGlobalUserTypesWithByPass();

        return $this->user_types_with_bypass_cache;
    }

    public function globalSettingsAreActive() {
        return get_option( 'tollbridge_is_using_global_rules', false );
    }

    public function disableLeakyPaywall( WP_Post $post ): bool {
        $article = new Article();
        $article->setId( $post->ID );

        return $article->hasMetaOverride() && $article->isDisableLeakyPaywall();
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     */
    public function getPaywallTitle( WP_Post $post = null ): string {
        if ( $post ) {
            $article = new Article();
            $article->setId( $post->ID );

            return $article->getPaywallTitle();
        }

        return $this->getConfig( 'paywall_widget_title' );
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     */
    public function getPaywallBody( WP_Post $post = null ): string {
        if ( $post ) {
            $article = new Article();
            $article->setId( $post->ID );

            return $article->getPaywallBody();
        }

        return $this->getConfig( 'paywall_widget_body' );
    }

    public function getPaywallEligibilityCheckBehavior(): int {
        return (int) get_option(
            'tollbridge_paywall_eligibility_check_behaviour',
            self::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS );
    }

    public function allowAllLoggedInUsers( WP_Post $post ): bool {
        $article = new Article();
        $article->setId( $post->ID );

        if ( $this->globalSettingsAreActive() && ! $article->hasMetaOverride() ) {
            return $this->getPaywallEligibilityCheckBehavior() === self::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ONLY_LOGGED_IN_USERS;
        }

        return $article->getPaywallEligibilityCheckBehavior() === self::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ONLY_LOGGED_IN_USERS;
    }
}
