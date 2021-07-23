<?php

$manager = new \Tollbridge\Paywall\Manager();

if ($manager->allAccountSettingsAreEntered()) {
    global $post;

    $plans = $this->manager->getApplicablePlans($post);

    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/leaky.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/fullscreen.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/slideup.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/inline.php';
    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/container.php';
} else {
    echo $content;
}
