<?php

namespace Tollbridge\Paywall\Settings;

use Tollbridge\Paywall\Manager;

class Config
{
    const ACCESS_CHANGE_FREE_TO_PAID = 'to_paid';
    const ACCESS_CHANGE_PAID_TO_FREE = 'to_free';

    public function displaySettingsPage()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'/../views/admin/paywall-config.php';
    }


    public function getApplicablePostTypes()
    {
        $saved_post_types = get_option('tollbridge_applicable_post_types');
        if (!is_array($saved_post_types)) {
            $saved_post_types = [];
        }

        return $saved_post_types;
    }


    public function getGlobalPlansWithAccess()
    {
        $existing_plans = get_option('tollbridge_plans_with_access');
        if (!is_array($existing_plans)) {
            $existing_plans = [];
        }

        return $existing_plans;
    }

    public function getGlobalTimeAccessChange()
    {
        return get_option('tollbridge_time_access_change', false);
    }

    public function getGlobalTimeAccessDelay()
    {
        return get_option('tollbridge_time_access_delay', 0);
    }

    public function getGlobalTimeAccessChangeDirection()
    {
        return get_option('tollbridge_time_access_change_direction', self::ACCESS_CHANGE_PAID_TO_FREE);
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
            'tollbridge_paywall_config_global',
            // Title to be displayed on the administration page
            '',
            // Callback used to render the description of the section
            [],
            // Page on which to add this section of options
            'tollbridge_paywall_paywall_config'
        );

        add_settings_field(
            'tollbridge_applicable_post_type',
            'Apply to these post types',
            [$this, 'renderPostTypeOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_applicable_post_types'
        );



        add_settings_field(
            'tollbridge_is_using_global_rules',
            'Apply settings globally',
            [$this, 'renderGlobalRadioOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_is_using_global_rules'
        );



        add_settings_field(
            'tollbridge_plans_with_access',
            'Only grant access to these plans',
            [$this, 'renderPlanOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_plans_with_access'
        );



        add_settings_field(
            'tollbridge_time_access_change',
            'Change paywall access over time',
            [$this, 'renderTimeChangeOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_time_access_change'
        );

        add_settings_field(
            'tollbridge_time_access_change',
            'Change paywall access over time',
            [$this, 'renderTimeAccessChangeOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_time_access_change'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_time_access_delay'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_time_access_change_direction'
        );
    }


    public function renderPostTypeOptions()
    {
        $saved_post_types = $this->getApplicablePostTypes();
        $content = '<fieldset>';

        foreach (get_post_types() as $post_type) {
            $readable_post_type = get_post_type_object($post_type);
            $checked = '';
            if (in_array($post_type, $saved_post_types)) {
                $checked = ' checked="checked"';
            }
            $content .= '<label>
                <input type="checkbox" name="tollbridge_applicable_post_types[]" '
                .'value="'.$post_type.'" '.$checked.'> '
                .'<span>'.$readable_post_type->labels->singular_name.'</span>'
                .'</label><br />';
        }

        $content .= '</fieldset>';

        echo $content;
    }

    public function renderGlobalRadioOptions()
    {
        $global = get_option('tollbridge_is_using_global_rules');
        echo '<fieldset>
            <label>
                <input type="radio" class="tollbridge_global_radio" name="tollbridge_is_using_global_rules" value="1" '.($global ? 'checked="checked"' : '').'> '
                .'<span>Manage settings globally</span>
            </label>
            <br />
            <label>
                <input type="radio" class="tollbridge_global_radio" name="tollbridge_is_using_global_rules" value="0" '.(!$global ? 'checked="checked"' : '').'> '
                .'<span>Manage settings per-article</span>
            </label>
            <p class="description">Global settings can be overridden on a per-article basis if required.</p>
        </fieldset>';
    }


    public function renderPlanOptions()
    {
        $existing_plans = $this->getGlobalPlansWithAccess();

        $manager = new Manager();
        $plans = $manager->getActivePlans();
        $content = '<fieldset class="tollbridge_global_option">';
        foreach ($plans as $plan) {
            $checked = '';
            if (in_array($plan, $existing_plans)) {
                $checked = ' checked="checked"';
            }
            $content .= '<label>
                <input type="checkbox" name="tollbridge_plans_with_access[]" '
                .'value="'.$plan.'" '.$checked.'> '
                .'<span>'.$plan.'</span>'
                .'</label><br />';
        }

        $content .= '</fieldset>';

        echo $content;
    }

    public function renderTimeAccessChangeOptions()
    {
        $change = $this->getGlobalTimeAccessChange();
        echo '<fieldset class="tollbridge_global_option">
            <label>
                <input type="radio" name="tollbridge_time_access_change" value="1" '.($change ? 'checked="checked"' : '').'> '
                .'<span>Yes</span>
            </label>
            <br />
            <label>
                <input type="radio" name="tollbridge_time_access_change" value="0" '.(!$change ? 'checked="checked"' : '').'> '
                .'<span>No</span>
            </label>
        </fieldset>
        <br />
        <fieldset class="tollbridge_time_access_dependent '.(!$change ? 'hidden' : '').'">
            <label>
                After <input type="number" name="tollbridge_time_access_delay" '
                .'value="'.$this->getGlobalTimeAccessDelay().'" min="0"> '
                .'days, change articles from:
            </label>
        </fieldset>
        <br />';

        $direction = $this->getGlobalTimeAccessChangeDirection();
        echo '<fieldset class="tollbridge_time_access_dependent '.(!$change ? 'hidden' : '').'">
            <label>
                <input type="radio" name="tollbridge_time_access_change_direction" '
                .'value="'.self::ACCESS_CHANGE_PAID_TO_FREE.'" '
                .($direction == self::ACCESS_CHANGE_PAID_TO_FREE ? 'checked="checked"' : '').'> '
                .'<span>Paywalled to free</span>
            </label>
            <br />
            <label>
                <input type="radio" name="tollbridge_time_access_change_direction" '
                .'value="'.self::ACCESS_CHANGE_FREE_TO_PAID.'" '
                .($direction == self::ACCESS_CHANGE_FREE_TO_PAID ? 'checked="checked"' : '').'> '
                .'<span>Free to paywalled</span>
            </label>
        </fieldset>';
    }
}
