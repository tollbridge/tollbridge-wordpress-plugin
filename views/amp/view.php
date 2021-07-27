<?php

$manager = new \Tollbridge\Paywall\Manager();

if ($manager->allAccountSettingsAreEntered()) {
    global $post;

    $plans = $this->manager->getApplicablePlans($post);

    $accessRule = implode(' AND ', array_map(function ($plan) {
        return 'plan != ' . $plan;
    }, array_column($plans, 'id')));

    $views = $this->manager->getAmpViews();

    foreach ($views as $key => $view) {
        if ($key == 'css') {
            continue;
        }
        echo str_replace('%amp-access-rule%', $accessRule, $view);
    }

    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/container.php';
} else {
    echo $content;
}
