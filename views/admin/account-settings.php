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
    <p>Enter the settings from your Tollbrige.co "Integrations" panel.</p>
    <p>The "<i>Callback URL</i>"" you need to enter is <strong><?php echo $manager->getCallbackUrl(); ?></strong>.</p>

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

        submit_button();
        ?>
    </form>
</div>
