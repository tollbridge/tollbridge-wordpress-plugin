<?php
global $post;
$manager          = new \Tollbridge\Paywall\Manager();
$global_rules_set = $manager->globalSettingsAreActive();
$article          = new \Tollbridge\Paywall\Frontend\Article();
$article->setId($post->ID);
$override = $article->hasMetaOverride();

if ($global_rules_set) {
?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php _e('Override Global Settings?', 'tollbridge'); ?></th>
            </tr>
            <tr>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="tollbridge_override_global_rules" value="0" <?php
                                                                                                    if (!$override) {
                                                                                                        echo 'checked="checked" ';
                                                                                                    } ?>> <span><?php _e('Use global settings', 'tollbridge'); ?></span>
                        </label>
                        <br />
                        <label>
                            <input type="radio" name="tollbridge_override_global_rules" value="1" <?php
                                                                                                    if ($override) {
                                                                                                        echo 'checked="checked" ';
                                                                                                    } ?>> <span><?php _e('Custom article settings', 'tollbridge'); ?></span>
                        </label>
                        <br>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
<?php
}

$class = ($global_rules_set && !$override) ? 'hidden' : '';

try {
    $existing_plans = $article->getPlansWithAccess();
    $plans          = $manager->getActivePlans();
    $tollbridge_paywall_eligibility_check_behaviour = $article->getPaywallEligibilityCheckBehavior();
?>

    <table class="form-table tollbridge-override-settings <?php echo $class; ?>" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php _e('Paywall Eligibility Check Behavior', 'tollbridge'); ?></th>
            </tr>
            <tr class="tollbridge_global_option">
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" class="tollbridge_paywall_eligibility_check_behaviour" name="tollbridge_paywall_eligibility_check_behaviour" value="<?php echo \Tollbridge\Paywall\Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS ?>" <?php echo $tollbridge_paywall_eligibility_check_behaviour === \Tollbridge\Paywall\Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS  ? ' checked="checked"' : ''  ?>>
                            <span> <?php echo __('Only users with selected plans can see article', 'tollbridge') ?> </span>
                        </label>
                        <br />
                        <label>
                            <input type="radio" class="tollbridge_paywall_eligibility_check_behaviour" name="tollbridge_paywall_eligibility_check_behaviour" value="<?php echo \Tollbridge\Paywall\Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ALL ?>" <?php echo $tollbridge_paywall_eligibility_check_behaviour === \Tollbridge\Paywall\Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ALL ?  'checked="checked"' : ''  ?>>
                            <span><?php echo __('Anyone can see article', 'tollbridge') ?></span>
                        </label>
                        <br />
                        <label>
                            <input type="radio" class="tollbridge_paywall_eligibility_check_behaviour" name="tollbridge_paywall_eligibility_check_behaviour" value="<?php echo \Tollbridge\Paywall\Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ONLY_LOGGED_IN_USERS ?>" <?php echo ($tollbridge_paywall_eligibility_check_behaviour === \Tollbridge\Paywall\Manager::PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_ONLY_LOGGED_IN_USERS ? ' checked="checked"' : '') ?>>
                            <span> <?php echo __('Only logged in users can see article', 'tollbridge') ?></span>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <tr class="tollbridge_global_option tollbridge_article_eligibilty_check_behavior_dependent">
                <th scope="row"><?php _e('Only grant access to these plans', 'tollbridge'); ?></th>
            </tr>
            <tr class="tollbridge_global_option tollbridge_article_eligibilty_check_behavior_dependent">
                <td>
                    <fieldset>
                        <label>
                            <?php
                            $content = '';

                            foreach ($plans as $id => $plan) {
                                $checked = '';

                                if (in_array($id, $existing_plans)) {
                                    $checked = ' checked="checked"';
                                }
                                $content .= '<label>
                                    <input type="checkbox" name="tollbridge_plans_with_access[]" '
                                    . 'value="' . $id . '" ' . $checked . '> '
                                    . '<span>' . $plan . '</span>'
                                    . '</label><br />';
                            }
                            echo $content; ?>
                        </label>
                        <br>
                    </fieldset>
                </td>
            </tr>

            <?php
            $time_access_change = $article->getTimeAccessChange(); ?>
            <tr class="tollbridge_article_eligibilty_check_behavior_dependent">
                <th scope="row"><?php _e('Change paywall access over time', 'tollbridge'); ?></th>
            </tr>
            <tr class="tollbridge_article_eligibilty_check_behavior_dependent">
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change" value="1" <?php
                                                                                                echo $time_access_change ? 'checked="checked"' : ''; ?>> <span><?php _e('Yes', 'tollbridge'); ?></span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change" value="0" <?php
                                                                                                echo !$time_access_change ? 'checked="checked"' : ''; ?>> <span><?php _e('No', 'tollbridge'); ?></span>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <?php
            $class = '';

            if (!$time_access_change) {
                $class = 'hidden';
            } ?>
            <tr class="tollbridge_article_eligibilty_check_behavior_dependent">
                <td>
                    <label class="tollbridge_time_access_dependent <?php echo $class; ?>">
                        <?php _e('After', 'tollbridge'); ?> <input type="number" name="tollbridge_time_access_delay" value="<?php echo $article->getTimeAccessDelay(); ?>" min="0" size="2"> <?php _e('days, change articles from:', 'tollbridge'); ?>
                    </label>
                </td>
            </tr>

            <?php
            $direction = $article->getTimeAccessChangeDirection(); ?>
            <tr class="tollbridge_article_eligibilty_check_behavior_dependent">
                <td>
                    <fieldset class="tollbridge_time_access_dependent <?php echo $class; ?>">
                        <label>
                            <input type="radio" name="tollbridge_time_access_change_direction" value="to_free" <?php
                                                                                                                echo $direction == 'to_free' ? 'checked="checked"' : ''; ?>> <span><?php _e('Paywalled to free', 'tollbridge'); ?></span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change_direction" value="to_paid" <?php
                                                                                                                echo $direction == 'to_paid' ? 'checked="checked"' : ''; ?>> <span><?php _e('Free to paywalled', 'tollbridge'); ?></span>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
<?php
} catch (\Exception $e) {
?>
    <table class="form-table tollbridge-override-settings <?php echo $class; ?>" role="presentation">
        <tr>
            <td>
                <strong><?php echo $e->getMessage(); ?></strong>
            </td>
        </tr>
    </table>
<?php
}
?>
