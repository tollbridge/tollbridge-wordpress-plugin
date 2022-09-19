<?php

namespace Tollbridge\Paywall\Settings;

use Exception;
use Tollbridge\Paywall\Manager;

class Config {

    const ACCESS_CHANGE_FREE_TO_PAID = 'to_paid';

    const ACCESS_CHANGE_PAID_TO_FREE = 'to_free';

    public function displaySettingsPage() {
        require_once plugin_dir_path( __DIR__ ) . '/../views/admin/paywall-config.php';
    }

    public function getApplicablePostTypes() {
        $saved_post_types = get_option( 'tollbridge_applicable_post_types' );

        if ( !is_array( $saved_post_types ) ) {
            $saved_post_types = [];
        }

        return $saved_post_types;
    }

    public function getGlobalPlansWithAccess() {
        $existing_plans = get_option( 'tollbridge_plans_with_access' );

        if ( !is_array( $existing_plans ) ) {
            $existing_plans = [];
        }

        return $existing_plans;
    }

    public function getGlobalUserTypesWithByPass() {
        $bypass_users = get_option( 'tollbridge_user_types_with_bypass' );

        if ( !is_array( $bypass_users ) ) {
            $bypass_users = [];
        }

        return $bypass_users;
    }

    public function getGlobalTimeAccessChange() {
        return get_option( 'tollbridge_time_access_change', false );
    }

    public function getGlobalTimeAccessDelay() {
        return get_option( 'tollbridge_time_access_delay', 0 );
    }

    public function getGlobalTimeAccessChangeDirection() {
        return get_option( 'tollbridge_time_access_change_direction', self::ACCESS_CHANGE_PAID_TO_FREE );
    }

    public function registerFields() {
        /*
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
            __( 'Apply to these post types', 'tollbridge' ),
            [$this, 'renderPostTypeOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_applicable_post_types',
	        [
				'type' => 'array',
		        'sanitize_callback' => [$this, 'sanitizePostTypeOptions'],
	        ]
        );

        add_settings_field(
            'tollbridge_user_types_with_bypass',
            __( 'Allow these user types to bypass paywall', 'tollbridge' ),
            [$this, 'renderUserBypassOptions'],
            'tollbridge_paywall_paywall_config',
            'tollbridge_paywall_config_global'
        );
        register_setting(
            'tollbridge_paywall_paywall_config',
            'tollbridge_user_types_with_bypass'
        );

        add_settings_field(
            'tollbridge_is_using_global_rules',
            __( 'Apply settings globally', 'tollbridge' ),
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
            __( 'Only grant access to these plans', 'tollbridge' ),
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
            __( 'Change paywall access over time', 'tollbridge' ),
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
            __( 'Change paywall access over time', 'tollbridge' ),
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

    public function renderPostTypeOptions() {
        $saved_post_types = $this->getApplicablePostTypes();
        $content          = '<fieldset>';
        $content          .= '<a style="margin-right: 1rem" href="javascript:void(0)" onClick="selectAllCheckbox(\'.tollbridge-applicable-post-types\')">Select all</a>';
        $content          .= '<a href="javascript:void(0)" onClick="unselectAllCheckbox(\'.tollbridge-applicable-post-types\')">Unselect all</a><br>';

        foreach ( get_post_types( ['public' => true] ) as $post_type ) {
            $readable_post_type = get_post_type_object( $post_type );
            $checked            = '';

            if ( in_array( $post_type, $saved_post_types ) ) {
                $checked = ' checked="checked"';
            }
            $content .= '<label>
                <input type="checkbox" class="tollbridge-applicable-post-types" name="tollbridge_applicable_post_types[]" '
                . 'value="' . $post_type . '" ' . $checked . '> '
                . '<span>' . $readable_post_type->labels->singular_name . '</span>'
                . '</label><br />';
        }

        $content .= '</fieldset>';

        echo $content;
    }

	public function sanitizePostTypeOptions($data) {
		if (empty($data)) {
			add_settings_error('tollbridge_applicable_post_types', 'tollbridge_applicable_post_type', __('At least one post type must be chosen.'));

			$data = get_option('tollbridge_applicable_post_types');
		}

		return $data;
	}

    public function renderGlobalRadioOptions() {
        $global = get_option( 'tollbridge_is_using_global_rules' );
        echo '<fieldset>
            <label>
                <input type="radio" class="tollbridge_global_radio" name="tollbridge_is_using_global_rules" value="1" ' . ( $global ? 'checked="checked"' : '' ) . '> '
                . '<span>' . __( 'Manage settings globally', 'tollbridge' ) . '</span>
            </label>
            <br />
            <label>
                <input type="radio" class="tollbridge_global_radio" name="tollbridge_is_using_global_rules" value="0" ' . ( !$global ? 'checked="checked"' : '' ) . '> '
                . '<span>' . __( 'Manage settings per-article', 'tollbridge' ) . '</span>
            </label>
            <p class="description">' . __( 'Global settings can be overridden on a per-article basis if required.', 'tollbridge' ) . '</p>
        </fieldset>';
    }

    public function renderPlanOptions() {
        $existing_plans = $this->getGlobalPlansWithAccess();

        $manager = new Manager();

        try {
            $plans = $manager->getActivePlans();
        } catch ( Exception $e ) {
            echo '<div class="error">' . __( 'Error retrieving plans', 'tollbridge' ) . ':' . $e->getMessage() . '</div>';
            echo '<fieldset class="tollbridge_global_option"><strong>' . $e->getMessage() . '</strong></fieldset>';

            return;
        }

        $content = '<fieldset class="tollbridge_global_option">';
	    $content .= '<a style="margin-right: 1rem" href="javascript:void(0)" onClick="selectAllCheckbox(\'.tollbridge-plans-access\')">Select all</a>';
	    $content .= '<a href="javascript:void(0)" onClick="unselectAllCheckbox(\'.tollbridge-plans-access\')">Unselect all</a><br>';

        foreach ( $plans as $id => $plan ) {
            $checked = '';

            if ( in_array( $id, $existing_plans ) ) {
                $checked = ' checked="checked"';
            }
            $content .= '<label>
                <input type="checkbox" class="tollbridge-plans-access" name="tollbridge_plans_with_access[]" '
                . 'value="' . $id . '" ' . $checked . '> '
                . '<span>' . $plan . '</span>'
                . '</label><br />';
        }

        $content .= '</fieldset>';

        echo $content;
    }

    public function renderUserBypassOptions() {
        $existing_users = $this->getGlobalUserTypesWithByPass();
        $roles          = get_editable_roles();

        $content = '<fieldset>';

        foreach ( $roles as $slug => $role ) {
            $checked = '';

            if ( in_array( $slug, $existing_users ) ) {
                $checked = ' checked="checked"';
            }
            $content .= '<label>
                <input type="checkbox" name="tollbridge_user_types_with_bypass[]" '
                . 'value="' . $slug . '" ' . $checked . '> '
                . '<span>' . $role['name'] . '</span>'
                . '</label><br />';
        }

        $content .= '</fieldset>';

        echo $content;
    }

    public function renderTimeAccessChangeOptions() {
        $change = $this->getGlobalTimeAccessChange();
        echo '<fieldset class="tollbridge_global_option">
            <label>
                <input type="radio" name="tollbridge_time_access_change" value="1" ' . ( $change ? 'checked="checked"' : '' ) . '> '
                . '<span>' . __( 'Yes', 'tollbridge' ) . '</span>
            </label>
            <br />
            <label>
                <input type="radio" name="tollbridge_time_access_change" value="0" ' . ( !$change ? 'checked="checked"' : '' ) . '> '
                . '<span>' . __( 'No', 'tollbridge' ) . '</span>
            </label>
        </fieldset>
        <br />
        <fieldset class="tollbridge_time_access_dependent ' . ( !$change ? 'hidden' : '' ) . '">
            <label>
                ' . __( 'After', 'tollbridge' ) . ' <input type="number" name="tollbridge_time_access_delay" '
                . 'value="' . $this->getGlobalTimeAccessDelay() . '" min="0"> '
                . __( 'days, change articles from', 'tollbridge' ) . ':
            </label>
        </fieldset>
        <br />';

        $direction = $this->getGlobalTimeAccessChangeDirection();
        echo '<fieldset class="tollbridge_time_access_dependent ' . ( !$change ? 'hidden' : '' ) . '">
            <label>
                <input type="radio" name="tollbridge_time_access_change_direction" '
                . 'value="' . self::ACCESS_CHANGE_PAID_TO_FREE . '" '
                . ( $direction == self::ACCESS_CHANGE_PAID_TO_FREE ? 'checked="checked"' : '' ) . '> '
                . '<span>' . __( 'Paywalled to free', 'tollbridge' ) . '</span>
            </label>
            <br />
            <label>
                <input type="radio" name="tollbridge_time_access_change_direction" '
                . 'value="' . self::ACCESS_CHANGE_FREE_TO_PAID . '" '
                . ( $direction == self::ACCESS_CHANGE_FREE_TO_PAID ? 'checked="checked"' : '' ) . '> '
                . '<span>' . __( 'Free to paywalled', 'tollbridge' ) . '</span>
            </label>
        </fieldset>';
    }
}
