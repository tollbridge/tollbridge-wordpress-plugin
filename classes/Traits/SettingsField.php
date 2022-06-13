<?php

namespace Tollbridge\Paywall\Traits;

trait SettingsField {

    public function render_settings_field( $args ) {
        /* EXAMPLE INPUT
                  'type'      => 'input',
                  'subtype'   => '',
                  'id'    => $this->plugin_name.'_example_setting',
                  'name'      => $this->plugin_name.'_example_setting',
                  'required' => true,
                  'get_option_list' => "",
                    'value_type' = serialized OR normal,
        'wp_data'=>(option or post_meta),
        'post_id' =>
        */
        if ( $args['wp_data'] == 'option' ) {
            $wp_data_value = get_option( $args['name'] );
        } elseif ( $args['wp_data'] == 'post_meta' ) {
            $wp_data_value = get_post_meta( $args['post_id'], $args['name'], true );
        }

        switch ( $args['type'] ) {

        case 'input':
            $value = ( $args['value_type'] == 'serialized' ) ? serialize( $wp_data_value ) : $wp_data_value;

            if ( $args['subtype'] != 'checkbox' ) {
                $prependStart = ( isset( $args['prepend_value'] ) ) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
                $prependEnd   = ( isset( $args['prepend_value'] ) ) ? '</div>' : '';
                $step         = ( isset( $args['step'] ) ) ? 'step="' . $args['step'] . '"' : '';
                $min          = ( isset( $args['min'] ) ) ? 'min="' . $args['min'] . '"' : '';
                $max          = ( isset( $args['max'] ) ) ? 'max="' . $args['max'] . '"' : '';
                $placeholder  = ( isset( $args['placeholder'] ) ) ? 'placeholder="' . $args['placeholder'] . '"' : '';

                if ( isset( $args['disabled'] ) ) {
                    echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr( $value ) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' ' . $placeholder . ' name="' . $args['name'] . '" size="40" value="' . esc_attr( $value ) . '" />' . $prependEnd;
                } else {
                    echo $prependStart . '<input type="' . $args['subtype'] . '" ' .
                        'id="' . $args['id'] . '" ' .
                        ( $args['required'] ? 'required="required"' : '' ) . ' ' .
                        $step . ' ' . $max . ' ' . $min . ' ' . $placeholder .
                        ' name="' . $args['name'] . '" size="40" value="' . esc_attr( $value ) . '" />' . $prependEnd;
                }
            } else {
                $checked = ( $value ) ? 'checked' : '';
                echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
            }

            break;
        default:
            // code...
            break;
        }
    }
}
