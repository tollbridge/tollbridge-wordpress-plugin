<?php
/**
 * JS Payload, to be output near the top of the <body> tag.
 */
$manager = new \Tollbridge\Paywall\Manager();
global $post;

if ($manager->allAccountSettingsAreEntered()) {
    ?>
    <script type="text/javascript" src="https://<?php echo $manager->getAppId(); ?>/js/tollbridge.js"></script>
    <tollbridge-config
        app-id="<?php echo $manager->getAppId(); ?>"
        client-id="<?php echo $manager->getClientId(); ?>"
        config-base="<?php echo $manager->getConfigBase(); ?>"
        callback-url="<?php echo $manager->getCallbackUrl(); ?>"
        <?php if ($manager->disableLeakyPaywall($post)): ?>
        type="hard"
        <?php endif; ?>
    ></tollbridge-config>
    <?php
}
?>
