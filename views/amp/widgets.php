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
        if ($key == 'css' || $key == 'inline') {
            continue;
        }
        echo str_replace('%amp-access-rule%', $accessRule, $view);
    }
}
