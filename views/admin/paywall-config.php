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
    <h1><?php _e('Paywall Configuration', 'tollbridge'); ?></h1>

    <?php
    if (!$manager->allAccountSettingsAreEntered()) {
        ?>
        <div class="error notice">
            <p><?php _e('It looks like you\'ve not finished setting up your configuration yet! Please go to', 'tollbridge'); ?> <a href="<?php echo $settings_url; ?>"><?php _e('Account Settings', 'tollbridge'); ?></a> <?php _e('to enter your Tollbridge account details.', 'tollbridge'); ?></p>
        </div>
        <?php
    } elseif (!$manager->accountSettingsCanBeAuthenticated()) {
        ?>
        <div class="error notice">
            <p><?php _e('It looks like the', 'tollbridge'); ?> <a href="<?php echo $settings_url; ?>"><?php _e('Account Settings', 'tollbridge'); ?></a> <?php _e('you\'ve entered are not active. Please re-check that you can log into your Tollbridge account, and that you have correctly copied over the settings from the Integration panel.', 'tollbridge'); ?></p>
        </div>
        <?php

    } else {
        settings_errors(); ?>
        <p><?php _e('Configure how you want the paywall to behave on your site.', 'tollbridge'); ?></p>
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
