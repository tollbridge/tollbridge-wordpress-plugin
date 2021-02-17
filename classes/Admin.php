<?php

namespace Tollbridge\Paywall;

use Tollbridge\Paywall\Settings\Account;
use Tollbridge\Paywall\Settings\Article;
use Tollbridge\Paywall\Settings\Config;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package Tollbridge\Paywall
 */
class Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('admin_menu', [$this, 'addPluginAdminMenu'], 9);
        add_action('admin_init', [$this, 'registerAndBuildSettingsFields']);
    }


    public function addPluginAdminMenu()
    {
        add_menu_page(
            'Tollbridge',
            'Tollbridge',
            'edit_posts',
            $this->plugin_name,
            [$this, 'displayAccountSettings'],
            "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' id='Layer_1' data-name='Layer 1' viewBox='1.5317970514297485 1.3330256938934326 89.9981918334961 89.98518371582031'%3E%3Cdefs%3E%3Cstyle%3E.cls%7Bfill:white;%7D%3C/style%3E%3C/defs%3E%3Cpath class='cls' d='M68.12,33.74a.87.87,0,0,0-.87.87v16a.86.86,0,0,0,.59.81,67.17,67.17,0,0,1,7.67,3.2.86.86,0,0,0,1.25-.77V34.61a.86.86,0,0,0-.86-.87Z'/%3E%3Cpath class='cls' d='M84.26,34.61V59.38a.87.87,0,0,0,.36.7q1.91,1.37,3.72,2.89a45.07,45.07,0,0,0,1.4-29.23H85.13A.87.87,0,0,0,84.26,34.61Z'/%3E%3Cpath class='cls' d='M51.11,33.74a.86.86,0,0,0-.86.87V47.14a.86.86,0,0,0,.8.86,66.21,66.21,0,0,1,7.67,1,.86.86,0,0,0,1-.85V34.61a.86.86,0,0,0-.86-.87Z'/%3E%3Cpath class='cls' d='M86.8,26.24a45,45,0,0,0-80.53,0Z'/%3E%3Cpath class='cls' d='M17.09,33.74a.86.86,0,0,0-.86.87V53.83a.86.86,0,0,0,1.25.77,67.17,67.17,0,0,1,7.67-3.2.86.86,0,0,0,.59-.81v-16a.87.87,0,0,0-.87-.87Z'/%3E%3Cpath class='cls' d='M7.71,69.07a45,45,0,0,0,77.62.05A58.82,58.82,0,0,0,7.71,69.07Z'/%3E%3Cpath class='cls' d='M8.73,59.38V34.61a.87.87,0,0,0-.87-.87H3.33A45,45,0,0,0,4.7,62.92c1.2-1,2.42-1.94,3.67-2.84A.87.87,0,0,0,8.73,59.38Z'/%3E%3Cpath class='cls' d='M34.1,33.74a.86.86,0,0,0-.86.87V48.14a.86.86,0,0,0,1,.85,66.21,66.21,0,0,1,7.67-1,.86.86,0,0,0,.81-.86V34.61a.87.87,0,0,0-.87-.87Z'/%3E%3C/svg%3E",
            20
        );

        add_submenu_page(
            $this->plugin_name,
            'Paywall Configuration',
            'Paywall Configuration',
            'administrator',
            $this->plugin_name.'-paywall-config',
            [$this, 'displayPaywallConfig']
        );

        add_submenu_page(
            $this->plugin_name,
            'Account Settings',
            'Account Settings',
            'administrator',
            $this->plugin_name.'-account-settings',
            [$this, 'displayAccountSettings']
        );


        // Stop the main menu from duplicating in the submenu.
        // @see https://wordpress.stackexchange.com/questions/16401/remove-duplicate-main-submenu-in-admin
        remove_submenu_page($this->plugin_name, $this->plugin_name);
    }


    public function displayPaywallConfig()
    {
        $config = new Config();
        $config->displaySettingsPage();
    }


    public function displayAccountSettings()
    {
        $settings = new Account();
        $settings->displaySettingsPage();
    }


    public function registerAndBuildSettingsFields()
    {
        $config = new Config();
        $config->registerFields();


        $settings = new Account();
        $settings->registerFields();
    }


    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, TOLLBRIDGE_BASE_URL . 'css/admin.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, TOLLBRIDGE_BASE_URL . 'js/admin.js', [], $this->version, 'all');
    }
}
