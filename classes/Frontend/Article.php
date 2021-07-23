<?php

namespace Tollbridge\Paywall\Frontend;

use Tollbridge\Paywall\Manager;
use Tollbridge\Paywall\Settings\Config;

/**
 * Handle drawing of the article meta box, and saving settings submitted from there.
 */
class Article
{
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

    public function __construct()
    {
        $this->manager = new Manager();
        $this->config = new Config();

        add_action('wp_head', [$this, 'addArticleMetaHeader']);
        // Adding the js via body_open is the cleanest way, but that hook does not
        // have widespread dev support yet, so add a fallback.
        add_action('wp_body_open', [$this, 'addArticleBodyOpenCode']);
        add_filter('the_content', [$this, 'addArticleBodyContentCode'], 10, 1);
    }

    public function setId(int $id)
    {
        $this->id = $id;
        $this->meta = $this->getArticleMeta($id);
    }


    public function getArticleMeta(int $id)
    {
        $meta = get_post_meta($id);
        $serialised = [
            'tollbridge_plans_with_access',
            'tollbridge_user_types_with_bypass',
        ];
        $stored = [];
        foreach ($meta as $key => $value) {
            if (!preg_match('#^tollbridge_#', $key)) {
                continue;
            }
            $stored[$key] = $value[0];
            if (in_array($key, $serialised)) {
                $stored[$key] = @unserialize($stored[$key]);
                if (!is_array($stored[$key])) {
                    $stored[$key] = [];
                }
            }
        }

        return $stored;
    }



    public function hasMetaOverride() : bool
    {
        return $this->meta['tollbridge_override_global_rules'] ?? false;
    }


    public function getPlansWithAccess() : array
    {
        if (!isset($this->meta['tollbridge_plans_with_access'])
            || !is_array($this->meta['tollbridge_plans_with_access'])) {
            $this->meta['tollbridge_plans_with_access'] = [];
        }

        return $this->meta['tollbridge_plans_with_access'];
    }


    public function getTimeAccessChange() : bool
    {
        if (!isset($this->meta['tollbridge_time_access_change'])) {
            $this->meta['tollbridge_time_access_change'] = false;
        }

        return (bool)$this->meta['tollbridge_time_access_change'];
    }


    public function getTimeAccessDelay() : int
    {
        if (!isset($this->meta['tollbridge_time_access_delay'])) {
            $this->meta['tollbridge_time_access_delay'] = 0;
        }

        return (int)$this->meta['tollbridge_time_access_delay'];
    }


    public function getTimeAccessChangeDirection() : string
    {
        if (!isset($this->meta['tollbridge_time_access_change_direction'])) {
            $this->meta['tollbridge_time_access_change_direction'] = Config::ACCESS_CHANGE_PAID_TO_FREE;
        }

        return $this->meta['tollbridge_time_access_change_direction'];
    }


    private function userCanBypassPaywall() : bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $meta = get_userdata(get_current_user_id());
        if (empty($meta->roles)) {
            return false;
        }

        return (count(array_intersect($meta->roles, $this->manager->getUserTypesWithBypass())) > 0);
    }

    private function isEligibleToShowPaywall() : bool
    {
        if (!is_single()) {
            return false;
        }
        global $post;

        if (!in_array($post->post_type, $this->config->getApplicablePostTypes())) {
            return false;
        }

        if (!$this->manager->allAccountSettingsAreEntered()) {
            return false;
        }

        return true;
    }


    public function addArticleMetaHeader()
    {
        if (!$this->isEligibleToShowPaywall()) {
            return;
        }

        if ($this->userCanBypassPaywall()) {
            return;
        }

        global $post;

        $plans = $this->manager->getApplicablePlans($post);

        if (empty($plans)) {
            return;
        }

        if (amp_is_request()) {
            require_once plugin_dir_path(dirname(__FILE__)).'/../views/amp/config.php';
        } else {
            echo '<meta name="tollbridge" content="'.implode(', ', array_column($plans, 'id')).'"/>';
        }
    }


    public function addArticleBodyOpenCode()
    {
        $this->bodyOpenWasTriggered = true;

        if (!$this->isEligibleToShowPaywall()) {
            return;
        }
        global $post;

        $plans = $this->manager->getApplicablePlans($post);

        if (empty($plans)) {
            return;
        }

        require_once plugin_dir_path(dirname(__FILE__)).'/../views/frontend/js-payload.php';
    }


    public function addArticleBodyContentCode($content)
    {
        // The wp_open_body hook is supported in this theme, and we've fired it already, no
        // more work required!
        if ($this->bodyOpenWasTriggered) {
            return $content;
        }

        if (amp_is_request()) {
            require_once plugin_dir_path(dirname(__FILE__)).'/../views/amp/view.php';
        } else {
            ob_start();
            require_once plugin_dir_path(dirname(__FILE__)).'/../views/frontend/js-payload.php';
            $payload = ob_get_clean();

            return $payload.$content;
        }
    }
}
