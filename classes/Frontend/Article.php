<?php

namespace Tollbridge\Paywall\Frontend;

use Exception;
use Tollbridge\Paywall\Exceptions\MissingConnectionSettingsException;
use Tollbridge\Paywall\Exceptions\ResponseErrorReceivedException;
use Tollbridge\Paywall\Manager;
use Tollbridge\Paywall\Settings\Config;

/**
 * Handle drawing of the article meta box, and saving settings submitted from there.
 */
class Article {

    /**
     * @var bool
     */
    private $bodyOpenWasTriggered = false;

    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $meta = [];

    public function __construct() {
        $this->manager = new Manager();
        $this->config  = new Config();

        add_action( 'wp_head', [ $this, 'addArticleMetaHeader' ] );
        // Adding the js via body_open is the cleanest way, but that hook does not
        // have widespread dev support yet, so add a fallback.
        add_action( 'wp_body_open', [ $this, 'addArticleBodyOpenCode' ] );
        add_filter( 'the_content', [ $this, 'addArticleBodyContentCode' ], 10, 1 );
    }

    public function setId( int $id ): void {
        $this->id   = $id;
        $this->meta = $this->getArticleMeta( $id );
    }

    public function getArticleMeta( int $id ): array {
        $meta       = get_post_meta( $id );
        $serialised = [
            'tollbridge_plans_with_access',
            'tollbridge_user_types_with_bypass',
        ];
        $stored     = [];

        foreach ( $meta as $key => $value ) {
            if ( 0 !== strpos( $key, "tollbridge_" ) ) {
                continue;
            }
            $stored[ $key ] = $value[0];

            if ( in_array( $key, $serialised, true ) ) {
                $stored[ $key ] = @unserialize( $stored[ $key ] );

                if ( ! is_array( $stored[ $key ] ) ) {
                    $stored[ $key ] = [];
                }
            }
        }

        return $stored;
    }

    public function hasMetaOverride(): bool {
        return $this->meta['tollbridge_override_global_rules'] ?? false;
    }

    public function getPlansWithAccess(): array {
        if (
            ! isset( $this->meta['tollbridge_plans_with_access'] )
            || ! is_array( $this->meta['tollbridge_plans_with_access'] )
        ) {
            $this->meta['tollbridge_plans_with_access'] = [];
        }

        return $this->meta['tollbridge_plans_with_access'];
    }

    public function getTimeAccessChange(): bool {
        if ( ! isset( $this->meta['tollbridge_time_access_change'] ) ) {
            $this->meta['tollbridge_time_access_change'] = false;
        }

        return (bool) $this->meta['tollbridge_time_access_change'];
    }

    public function getTimeAccessDelay(): int {
        if ( ! isset( $this->meta['tollbridge_time_access_delay'] ) ) {
            $this->meta['tollbridge_time_access_delay'] = 0;
        }

        return (int) $this->meta['tollbridge_time_access_delay'];
    }

    public function getTimeAccessChangeDirection(): string {
        if ( ! isset( $this->meta['tollbridge_time_access_change_direction'] ) ) {
            $this->meta['tollbridge_time_access_change_direction'] = Config::ACCESS_CHANGE_PAID_TO_FREE;
        }

        return $this->meta['tollbridge_time_access_change_direction'];
    }

    public function isDisableLeakyPaywall(): bool {
        if ( ! isset( $this->meta['tollbridge_disable_leaky_paywall'] ) ) {
            $this->meta['tollbridge_disable_leaky_paywall'] = false;
        }

        return (bool) $this->meta['tollbridge_disable_leaky_paywall'];
    }

    public function isChangeMessagePaywall(): bool {
        if ( ! isset( $this->meta['tollbridge_change_message_paywall'] ) ) {
            $this->meta['tollbridge_change_message_paywall'] = false;
        }

        return (bool) $this->meta['tollbridge_change_message_paywall'];
    }

    public function getPaywallTitle(): string {
        if ( ! isset( $this->meta['tollbridge_message_paywall_title'] ) ) {
            $this->meta['tollbridge_message_paywall_title'] = '';
        }

        return $this->meta['tollbridge_message_paywall_title'];
    }

    public function getPaywallBody(): string {
        if ( ! isset( $this->meta['tollbridge_message_paywall_body'] ) ) {
            $this->meta['tollbridge_message_paywall_body'] = '';
        }

        return $this->meta['tollbridge_message_paywall_body'];
    }

    private function userCanBypassPaywall(): bool {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        $meta = get_userdata( get_current_user_id() );

        if ( empty( $meta->roles ) ) {
            return false;
        }

        return count( array_intersect( $meta->roles, $this->manager->getUserTypesWithBypass() ) ) > 0;
    }

    private function isEligibleToShowPaywall(): bool {
        if ( ! is_single() && ! is_page() ) {
            return false;
        }
        global $post;

        $this->setId( $post->ID );

        if ( ! in_array( $post->post_type, $this->config->getApplicablePostTypes(), true ) ) {
            return false;
        }

        if ( ! $this->manager->allAccountSettingsAreEntered() ) {
            return false;
        }

        if ( $this->articleIsSetToOpenToAll() ) {
            return false;
        }

        return true;
    }

    private function articleIsSetToOpenToAll(): bool {
        if ( $this->manager->globalSettingsAreActive() && ! $this->hasMetaOverride() && $this->manager->getPaywallEligibilityCheckBehavior() === Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ALL ) {
            return true;
        }

        if ( $this->hasMetaOverride() && $this->getPaywallEligibilityCheckBehavior() === Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ALL ) {
            return true;
        }

        return false;
    }

    /**
     * @throws ResponseErrorReceivedException
     * @throws MissingConnectionSettingsException
     * @throws Exception
     */
    public function addArticleMetaHeader(): void {
        if ( $this->manager->isTrendingArticleActive() ) {
            echo '<meta name="tollbridge:track" content="true" />';
        }

        if ( ! $this->isEligibleToShowPaywall() ) {
            return;
        }

        if ( $this->userCanBypassPaywall() ) {
            return;
        }

        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            require_once plugin_dir_path( __DIR__ ) . '/../views/amp/config.php';
        } else {
            global $post;

            $this->setId( $post->ID );

            if ( $this->manager->allowAllLoggedInUsers( $post ) ) {
                echo '<meta name="tollbridge:allow" content="loggedIn"/>';
            }
            $plans = $this->manager->getApplicablePlans( $post );

            if ( empty( $plans ) ) {
                return;
            }

            echo '<meta name="tollbridge:plan" content="' . implode( ',', array_column( $plans, 'id' ) ) . '"/>';
        }
    }

    /**
     * @throws Exception
     */
    public function addArticleBodyOpenCode(): void {
        $this->bodyOpenWasTriggered = true;

        if ( ! $this->isEligibleToShowPaywall() ) {
            return;
        }

        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            if ( $this->manager->getPaywallTemplate() === 'inline' ) {
                $this->bodyOpenWasTriggered = false;
            }

            require_once plugin_dir_path( __DIR__ ) . '/../views/amp/widgets.php';
        } else {
            global $post;

            $plans = $this->manager->getApplicablePlans( $post );

            if ( empty( $plans ) ) {
                return;
            }

            require_once plugin_dir_path( __DIR__ ) . '/../views/frontend/js-payload.php';
        }
    }

    public function addArticleBodyContentCode( $content ) {
        // The wp_open_body hook is supported in this theme, and we've fired it already, no
        // more work required!
        if ( $this->bodyOpenWasTriggered ) {
            return $content;
        }

        ob_start();

        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            require_once plugin_dir_path( __DIR__ ) . '/../views/amp/view.php';

            return '';
        }

        require_once plugin_dir_path( __DIR__ ) . '/../views/frontend/js-payload.php';
        $payload = ob_get_clean();

        return $payload . $content;
    }

    public function getPaywallEligibilityCheckBehavior(): int {
        return (int) ( trim(
                           $this->meta['tollbridge_paywall_eligibility_check_behaviour'] ??
                           Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ALL
                       ) ??
                       Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS );
    }
}
