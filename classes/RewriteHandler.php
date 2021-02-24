<?php

namespace Tollbridge\Paywall;

/**
 * Handle rewrites in place to provide custom url redirection.
 */
class RewriteHandler
{
    private $query_var = 'tollbridge_redirect';

    public function __construct()
    {
        add_action('init', [$this, 'registerRewriteRules']);
        add_filter('query_vars', [$this, 'registerRewriteRuleVar']);
        add_filter('template_include', [$this, 'loadCustomRegistrationRedirectTemplate'], 1, 1);
    }


    public function registerRewriteRules()
    {
        add_rewrite_rule(
            Manager::AUTHENTICATION_CALLBACK_SLUG.'/?$',
            'index.php?'.$this->query_var.'=true',
            'top'
        );

        // With OPCache turned on, rewrites may already be cached.
        $rules = get_option( 'rewrite_rules' );
        if (!isset($rules[Manager::AUTHENTICATION_CALLBACK_SLUG.'/?$'])) {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }
    }

    public function registerRewriteRuleVar($vars)
    {
        // Register the vars so WP treats ?tollbridge_redirect and ?tollbridge_redirect=true the same
        $vars[] = $this->query_var;
        return $vars;
    }


    public function loadCustomRegistrationRedirectTemplate($template)
    {
        global $wp_query;
        $page_value = $wp_query->query_vars[$this->query_var];

        if ($page_value && $page_value == "true") {
            return plugin_dir_path(dirname(__FILE__)).'views/frontend/callback.php';
        }

        return $template;
    }
}
