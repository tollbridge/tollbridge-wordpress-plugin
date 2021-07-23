<?php if ($plans) { ?>
    <div amp-access="(views >= 0 AND type = 'leaky') OR <?= implode(' OR ', array_map(function ($plan) {
        return 'plan = ' . $plan;
    }, array_column($plans, 'id'))); ?>">
        <?= $content ?>
    </div>
<?php } else {
    echo $content;
}
