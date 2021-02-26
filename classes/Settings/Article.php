<?php

namespace Tollbridge\Paywall\Settings;

use Tollbridge\Paywall\Manager;
use Tollbridge\Paywall\Settings\Config;

/**
 * Handle drawing of the article meta box, and saving settings submitted from there.
 */
class Article
{
    private $bodyOpenWasTriggered = false;

    public function __construct()
    {
        $this->manager = new Manager();
        $this->config = new Config();

        add_action('add_meta_boxes', [$this, 'addArticleMetaBoxHook']);
        add_action('save_post', [$this, 'saveCustomMetaBox']);
    }


    public function addArticleMetaBoxHook()
    {
        add_meta_box(
            'tollbridge-metabox',
            'Tollbridge Paywall Settings',
            [$this, 'displayArticleMetaBox'],
            $this->config->getApplicablePostTypes(),
            'side',
            'low'
        );

        // Check for a case where a user has already saved a metabox ordering.
        // If they have an order saved and we add a new box, our box won't appear.
        $user_id = get_current_user_id();
        $meta_key = 'meta-box-order_post';
        $meta_value = get_user_meta($user_id, $meta_key, true );
        if (!$meta_value || !isset($meta_value['side'])) {
            return;
        }
        $side = array_filter(explode(',', $meta_value['side']));
        if (in_array('tollbridge-metabox', $side)) {
            return;
        }

        $side[] = 'tollbridge-metabox';
        $meta_value['side'] = implode(',', $side);
        update_user_meta($user_id, $meta_key, $meta_value);

    }


    public function displayArticleMetaBox()
    {
        global $post;
        $applicable_types = $this->config->getApplicablePostTypes();

        if (!in_array($post->post_type, $applicable_types)) {
            return;
        }

        require_once plugin_dir_path(dirname(__FILE__)).'/../views/admin/article-meta.php';
    }


    public function saveCustomMetaBox()
    {
        global $post;
        foreach ($_POST as $key => $value) {
            if (!preg_match('/^tollbridge_/', $key)) {
                continue;
            }
            update_post_meta($post->ID, $key, $value);
        }
    }
}
