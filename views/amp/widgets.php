<?php

$manager = new \Tollbridge\Paywall\Manager();

if ( $manager->allAccountSettingsAreEntered() ) {
    global $post;

    $views = $this->manager->getAmpViews();

    foreach ( $views as $key => $view ) {
        if ( $key === 'css' || $key === 'inline' ) {
            continue;
        }
        echo str_replace( '{{ homepage_url }}', get_home_url(), $view );
    }
}
