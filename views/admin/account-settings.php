<?php

/**
 * Provide a admin settings area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */
$manager = new \Tollbridge\Paywall\Manager();
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php _e( 'Account Settings', 'tollbridge' ); ?></h2>
    <p><?php _e( 'Enter the settings from your Tollbrige "Integrations" panel.', 'tollbridge' ); ?></p>

    <?php
    if ( $manager->allAccountSettingsAreEntered() && !$manager->accountSettingsCanBeAuthenticated() ) {
        ?>
        <div class="error notice">
            <p><?php _e( 'It looks like the Account Settings you\'ve entered are not active. Please re-check that you can log into your Tollbridge account, and that you have correctly copied over the settings from the Integration panel.', 'tollbridge' ); ?></p>
        </div>
        <?php
    }

    settings_errors();
    ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields( 'tollbridge_paywall_account_settings' );
        do_settings_sections( 'tollbridge_paywall_account_settings' );

        if (!get_option('tollbridge_advanced_mode')) :
        ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><?php _e( 'Callback URL', 'tollbridge' ); ?></th>
                    <td>
                        <input type="text" size="40" readonly value="<?php echo $manager->getCallbackUrl(); ?>" onClick="this.select();">
                        <br />
                        <small><i><?php _e( 'Copy this value to the "Callback URL" field in your Tollbridge "Integrations" panel.', 'tollbridge' ); ?></i></small>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        endif;
        submit_button();
        ?>
    </form>
</div>
