<?php

/**
 * Provide a admin settings area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package Tollbridge\Paywall
 */
$manager = new \Tollbridge\Paywall\Manager();
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2>Account Settings</h2>
    <p>Enter the settings from your Tollbrige "Integrations" panel.</p>

    <?php
    if ($manager->allAccountSettingsAreEntered() && !$manager->accountSettingsCanBeAuthenticated()) {
        ?>
        <div class="error notice">
            <p>It looks like the Account Settings you've entered are not active. Please re-check that you can log into your Tollbridge account, and that you have correctly copied over the settings from the Integration panel.</p>
        </div>
        <?php
    }

    settings_errors();
    ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields('tollbridge_paywall_account_settings');
        do_settings_sections('tollbridge_paywall_account_settings');
        ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">Callback URL</th>
                    <td>
                        <input type="text" size="40" disabled value="<?php echo $manager->getCallbackUrl(); ?>">
                        <br />
                        <small><i>Copy this value to the "Callback URL" field in your Tollbridge "Integrations" panel.</i></small>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        submit_button();
        ?>
    </form>
</div>
