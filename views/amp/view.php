<?php

$manager = new \Tollbridge\Paywall\Manager();

if ($manager->allAccountSettingsAreEntered()) {
    global $post;

    $plans = $this->manager->getApplicablePlans($post);

    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/widgets/leaky.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/widgets/fullscreen.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/widgets/slideup.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/widgets/inline.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/container.php';
} else {
    echo $content;
}
