<?php

$manager = new \Tollbridge\Paywall\Manager();

if ( $manager->allAccountSettingsAreEntered() ) {
    global $post;

    $views = $this->manager->getAmpViews();

    foreach ( $views as $key => $view ) {
        if ( $key === 'inline' ) {
            echo $view;
        }
    }

    require_once plugin_dir_path( __DIR__ ) . '/../views/amp/container.php';
} else {
    echo $content;
}
