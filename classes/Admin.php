<?php

namespace Tollbridge\Paywall;

use Tollbridge\Paywall\Settings\Account;
use Tollbridge\Paywall\Settings\Config;

/**
 * The admin-specific functionality of the plugin.
 */
class Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the ID of this plugin
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the current version of this plugin
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name the name of this plugin
     * @param string $version     the version of this plugin
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        add_action( 'admin_menu', [$this, 'addPluginAdminMenu'], 9 );
        add_action( 'admin_init', [$this, 'registerAndBuildSettingsFields'] );
    }

    public function addPluginAdminMenu() {
        $svg = file_get_contents( __DIR__ . '/../images/tollbridge.svg' );

        add_menu_page(
            __( 'Tollbridge', 'tollbridge' ),
            __( 'Tollbridge', 'tollbridge' ),
            'edit_posts',
            $this->plugin_name,
            [$this, 'displayAccountSettings'],
            'data:image/svg+xml;base64,' . base64_encode( $svg ),
            20
        );

        add_submenu_page(
            $this->plugin_name,
            __( 'Paywall Config', 'tollbridge' ),
            __( 'Paywall Config', 'tollbridge' ),
            'administrator',
            $this->plugin_name . '-paywall-config',
            [$this, 'displayPaywallConfig']
        );

        add_submenu_page(
            $this->plugin_name,
            __( 'Account Settings', 'tollbridge' ),
            __( 'Account Settings', 'tollbridge' ),
            'administrator',
            $this->plugin_name . '-account-settings',
            [$this, 'displayAccountSettings']
        );

        // Stop the main menu from duplicating in the submenu.
        // @see https://wordpress.stackexchange.com/questions/16401/remove-duplicate-main-submenu-in-admin
        remove_submenu_page( $this->plugin_name, $this->plugin_name );
    }

    public function displayPaywallConfig() {
        $config = new Config();
        $config->displaySettingsPage();
    }

    public function displayAccountSettings() {
        $settings = new Account();
        $settings->displaySettingsPage();
    }

    public function registerAndBuildSettingsFields() {
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
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, TOLLBRIDGE_BASE_URL . 'css/admin.css', [], $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, TOLLBRIDGE_BASE_URL . 'js/admin.js', [], $this->version, 'all' );
    }
}
