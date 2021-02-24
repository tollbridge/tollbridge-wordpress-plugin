<?php

/**
 * Article configuration settings.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package Tollbridge\Paywall
 */

$manager = new \Tollbridge\Paywall\Manager();
$settings_url = admin_url('admin.php?page=tollbridge-paywall-account-settings');
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h1>Paywall Configuration</h1>

    <?php
    if (!$manager->allAccountSettingsAreEntered()) {
        ?>
        <div class="error notice">
            <p>It looks like you've not finished setting up your configuration yet! Please go to <a href="<?php echo $settings_url; ?>">Account Settings</a> to enter your Tollbridge account details.</p>
        </div>
        <?php
    } elseif (!$manager->accountSettingsCanBeAuthenticated()) {
        ?>
        <div class="error notice">
            <p>It looks like the <a href="<?php echo $settings_url; ?>">Account Settings</a> you've entered are not active. Please re-check that you can log into your Tollbridge account, and that you have correctly copied over the settings from the Integration panel.</p>
        </div>
        <?php

    } else {
        settings_errors(); ?>
        <p>Configure how you want the paywall to behave on your site.</p>
        <form method="POST" action="options.php">
            <?php
            settings_fields('tollbridge_paywall_paywall_config');
            do_settings_sections('tollbridge_paywall_paywall_config');
            submit_button();
            ?>
            </fieldset>
        </form>
        <?php
    }
    ?>
</div>
