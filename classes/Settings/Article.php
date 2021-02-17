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
            'post'
        );
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
