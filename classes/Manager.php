<?php

namespace Tollbridge\Paywall;

use DateTime;
use WP_Post;
use Tollbridge\Paywall\Frontend\Article;
use Tollbridge\Paywall\Settings\Config;

/**
 * Class to interface with underlying data, and make presentation / calculation logic simpler.
 */
class Manager
{
    const AUTHENTICATION_CALLBACK_SLUG = 'tollbridge-callback';

    /**
     * @var \Tollbridge\Paywall\Client
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


    public function __construct()
    {
        $this->client = new Client();
    }


    public function getAppId()
    {
        return trim(get_option('tollbridge_app_id'));
    }

    public function getClientId()
    {
        return trim(get_option('tollbridge_client_id'));
    }

    public function getClientSecret()
    {
        return trim(get_option('tollbridge_client_secret'));
    }

    public function allAccountSettingsAreEntered() : bool
    {
        // Ensure that all 3 key fields are entered and non-empty.
        $fields = [
            $this->getAppId(),
            $this->getClientId(),
            $this->getClientSecret(),
        ];

        return count(array_filter($fields)) == 3;
    }


    public function getCallbackUrl() : string
    {
        return get_home_url().'/'.self::AUTHENTICATION_CALLBACK_SLUG;
    }

    public function getActivePlans()
    {
        return $this->client->getPlans();
    }


    public function accountSettingsCanBeAuthenticated() : bool
    {
        try {
            $this->client->getAccessToken();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


    /**
     * Get the list of plans which are applicable to the given post.
     */
    public function getApplicablePlans(WP_Post $post) : array
    {
        if (!is_null($this->applicable_plans_cache)) {
            return $this->applicable_plans_cache;
        }

        $article = new Article();
        $article->setId($post->ID);

        if ($this->globalSettingsAreActive() && !$article->hasMetaOverride()) {
            $config = new Config();
            $plans = $config->getGlobalPlansWithAccess();
            $hasTimeAccessChange = $config->getGlobalTimeAccessChange();
            if ($hasTimeAccessChange) {
                $timeAccessDays = $config->getGlobalTimeAccessDelay();
                $timeAccessDirection = $config->getGlobalTimeAccessChangeDirection();
            }
        } else {
            // Get plans from article
            $plans = $article->getPlansWithAccess();
            $hasTimeAccessChange = $article->getTimeAccessChange();
            if ($hasTimeAccessChange) {
                $timeAccessDays = $article->getTimeAccessDelay();
                $timeAccessDirection = $article->getTimeAccessChangeDirection();
            }
        }

        // We have plan ids - need to hydrate them with plan names!
        $planList = $this->client->getPlans();
        $hydratedPlans = [];
        foreach ($plans as $id) {
            if (!empty($planList[$id])) {
                $hydratedPlans[] = [
                    'id' => $id,
                    'plan' => $planList[$id],
                ];
            }
        }
        if (empty($hydratedPlans)) {
            $this->applicable_plans_cache = [];
            return [];
        }

        $this->applicable_plans_cache = $hydratedPlans;

        // No time filters applied? Return now.
        if (!$hasTimeAccessChange) {
            return $this->applicable_plans_cache;
        }

        $now = new DateTime();
        $published = new DateTime($post->post_date);
        $diff = $now->diff($published);

        // Enough days passed to trigger time logic
        if ($diff->days >= $timeAccessDays) {
            // Are we going from paid to free?
            if ($timeAccessDirection == Config::ACCESS_CHANGE_PAID_TO_FREE) {
                // No restriction!
                $this->applicable_plans_cache = [];
            }
        } else {
            // Starting free before going paid later
            if ($timeAccessDirection == Config::ACCESS_CHANGE_FREE_TO_PAID) {
                $this->applicable_plans_cache = [];
            }
        }

        return $this->applicable_plans_cache;
    }


    /**
     * Get the list of user type slugs which are permitted to bypass the paywall.
     */
    public function getUserTypesWithBypass() : array
    {
        if (!is_null($this->user_types_with_bypass_cache)) {
            return $this->user_types_with_bypass_cache;
        }

        $config = new Config();
        $this->user_types_with_bypass_cache = $config->getGlobalUserTypesWithByPass();

        return $this->user_types_with_bypass_cache;
    }


    public function globalSettingsAreActive()
    {
        return get_option('tollbridge_is_using_global_rules', false);
    }
}
