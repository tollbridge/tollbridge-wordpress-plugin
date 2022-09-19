<?php

namespace Tollbridge\Paywall\Settings;

use Tollbridge\Paywall\Manager;
use Tollbridge\Paywall\Traits\SettingsField;

class Account {

    use SettingsField;

    public function displaySettingsPage() {
        require_once plugin_dir_path( __DIR__ ) . '/../views/admin/account-settings.php';
    }

    public function registerFields() {
        /*
         * First, we add_settings_section. This is necessary since all future settings must belong to one.
         * Second, add_settings_field
         * Third, register_setting
         */
        add_settings_section(
            // ID used to identify this section and with which to register options
            'tollbridge_account_settings_general_section',
            // Title to be displayed on the administration page
            '',
            // Callback used to render the description of the section
            [],
            // Page on which to add this section of options
            'tollbridge_paywall_account_settings'
        );

        add_settings_field(
            'tollbridge_app_id',
            __( 'App ID', 'tollbridge' ),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'             => 'input',
                'subtype'          => 'text',
                'id'               => 'tollbridge_app_id',
                'name'             => 'tollbridge_app_id',
                'required'         => true,
                'get_options_list' => '',
                'value_type'       => 'normal',
                'wp_data'          => 'option',
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_app_id'
        );

        add_settings_field(
            'tollbridge_client_id',
            __( 'Client ID', 'tollbridge' ),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'             => 'input',
                'subtype'          => 'text',
                'id'               => 'tollbridge_client_id',
                'name'             => 'tollbridge_client_id',
                'required'         => true,
                'get_options_list' => '',
                'value_type'       => 'normal',
                'wp_data'          => 'option',
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_client_id'
        );

        add_settings_field(
            'tollbridge_client_secret',
            __( 'Client Secret', 'tollbridge' ),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'             => 'input',
                'subtype'          => 'password',
                'id'               => 'tollbridge_client_secret',
                'name'             => 'tollbridge_client_secret',
                'required'         => true,
                'get_options_list' => '',
                'value_type'       => 'normal',
                'wp_data'          => 'option',
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_client_secret'
        );

        add_settings_field(
            'tollbridge_advanced_mode',
            __( 'Enable Advanced Mode?', 'tollbridge' ),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'             => 'input',
                'subtype'          => 'checkbox',
                'id'               => 'tollbridge_advanced_mode',
                'name'             => 'tollbridge_advanced_mode',
                'required'         => false,
                'get_options_list' => '',
                'value_type'       => 'normal',
                'wp_data'          => 'option',
                'description'      => __('Yes'),
                'sub-description'      => __('Enabling this will trigger advanced options and Tollbridge functionality such as change config base url or change callback url.'),
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_advanced_mode'
        );

        if ( get_option( 'tollbridge_advanced_mode' ) ) {
            $manager = new Manager();

	        add_settings_field(
		        'tollbridge_callback_url',
		        __( 'Callback Url', 'tollbridge' ),
		        [ $this, 'render_settings_field' ],
		        'tollbridge_paywall_account_settings',
		        'tollbridge_account_settings_general_section',
		        [
			        'type'             => 'input',
			        'subtype'          => 'text',
			        'id'               => 'tollbridge_callback_url',
			        'name'             => 'tollbridge_callback_url',
			        'required'         => false,
			        'get_options_list' => '',
			        'value_type'       => 'normal',
			        'wp_data'          => 'option',
			        'placeholder'      => $manager->getDefaultCallbackUrl(),
			        'set-default'      => $manager->getDefaultCallbackUrl(),
			        'description'      => __( 'Copy this value to the "Callback URL" field in your Tollbridge "Integrations" panel.', 'tollbridge' ),
		        ]
	        );
	        register_setting(
		        'tollbridge_paywall_account_settings',
		        'tollbridge_callback_url'
	        );

            add_settings_field(
                'tollbridge_config_base',
                __( 'Config Base Url', 'tollbridge' ),
                [ $this, 'render_settings_field' ],
                'tollbridge_paywall_account_settings',
                'tollbridge_account_settings_general_section',
                [
                    'type'             => 'input',
                    'subtype'          => 'text',
                    'id'               => 'tollbridge_config_base',
                    'name'             => 'tollbridge_config_base',
                    'required'         => false,
                    'get_options_list' => '',
                    'value_type'       => 'normal',
                    'wp_data'          => 'option',
                    'placeholder'      => $manager->getDefaultConfigBase(),
                    'set-default'      => $manager->getDefaultConfigBase(),
                ]
            );
            register_setting(
                'tollbridge_paywall_account_settings',
                'tollbridge_config_base'
            );
        }
    }
}
