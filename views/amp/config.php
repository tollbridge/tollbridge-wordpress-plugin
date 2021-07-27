<?php
/**
 * AMP config, to be output near the top of the <body> tag.
 */
$manager = new \Tollbridge\Paywall\Manager();

if ($manager->allAccountSettingsAreEntered()) {
    $views = $this->manager->getAmpViews();
    $appId = $this->manager->getAppId(); ?>
    <style amp-custom><?=$views['css']?></style>
    <script id="amp-access" type="application/json">
        {
            "type": "client",
            "authorization": "https://<?=$appId?>/amp/authorization?rid=READER_ID&url=CANONICAL_URL&ref=DOCUMENT_REFERRER&_=RANDOM",
            "pingback": "https://<?=$appId?>/amp/ping-back?rid=READER_ID&ref=DOCUMENT_REFERRER&url=CANONICAL_URL&_=RANDOM",
            "login": "https://<?=$appId?>/plans?rid=READER_ID&url=CANONICAL_URL&redirect=RETURN_URL",
            "authorizationFallbackResponse": {
                "plan": 0,
                "subscriber": false,
                "type": "leaky",
                "views": 0
            }
        }
    </script>
    <script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
    <script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
    <script async custom-element="amp-access" src="https://cdn.ampproject.org/v0/amp-access-0.1.js"></script>
    <?php
}
