<?php

$manager = new \Tollbridge\Paywall\Manager();

if ($manager->allAccountSettingsAreEntered()) {
    global $post;

    $plans = $this->manager->getApplicablePlans($post);

    $accessRule = $this->manager->getAccessRules($post);

    $requirements = $this->manager->getRequirementsText($post);

    $views = $this->manager->getAmpViews();

    foreach ($views as $key => $view) {
        if ($key == 'inline') {
            echo str_replace('%amp-access-rule%', $accessRule,
                str_replace('{{ widget.requirements }}', $requirements, $view)
            );
        }
    }

    require_once plugin_dir_path(dirname(__FILE__)) . '/../views/amp/container.php';
} else {
    echo $content;
}
