<?php

namespace Tollbridge\Paywall\Settings;

use Tollbridge\Paywall\Traits\SettingsField;

class Account
{
    use SettingsField;

    public function displaySettingsPage()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'/../views/admin/account-settings.php';
    }

    public function registerFields()
    {
        /**
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
            __('App ID', 'tollbridge'),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'      => 'input',
                'subtype'   => 'text',
                'id'        => 'tollbridge_app_id',
                'name'      => 'tollbridge_app_id',
                'required'  => true,
                'get_options_list' => '',
                'value_type' =>'normal',
                'wp_data' => 'option',
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_app_id'
        );

        add_settings_field(
            'tollbridge_client_id',
            __('Client ID', 'tollbridge'),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'      => 'input',
                'subtype'   => 'text',
                'id'        => 'tollbridge_client_id',
                'name'      => 'tollbridge_client_id',
                'required'  => true,
                'get_options_list' => '',
                'value_type' =>'normal',
                'wp_data' => 'option',
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_client_id'
        );

        add_settings_field(
            'tollbridge_client_secret',
            __('Client Secret', 'tollbridge'),
            [$this, 'render_settings_field'],
            'tollbridge_paywall_account_settings',
            'tollbridge_account_settings_general_section',
            [
                'type'      => 'input',
                'subtype'   => 'password',
                'id'        => 'tollbridge_client_secret',
                'name'      => 'tollbridge_client_secret',
                'required'  => true,
                'get_options_list' => '',
                'value_type' =>'normal',
                'wp_data' => 'option',
            ]
        );
        register_setting(
            'tollbridge_paywall_account_settings',
            'tollbridge_client_secret'
        );
    }
}
