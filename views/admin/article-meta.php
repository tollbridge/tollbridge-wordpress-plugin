<?php
global $post;
$manager = new \Tollbridge\Paywall\Manager();
$global_rules_set = $manager->globalSettingsAreActive();
$article = new \Tollbridge\Paywall\Frontend\Article();
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
                            <input type="radio" name="tollbridge_override_global_rules" value="0"
                            <?php
                            if (!$override) {
                                echo 'checked="checked" ';
                            } ?>
                            > <span><?php _e('Use global settings', 'tollbridge'); ?></span>
                        </label>
                        <br />
                        <label>
                            <input type="radio" name="tollbridge_override_global_rules" value="1"
                            <?php
                            if ($override) {
                                echo 'checked="checked" ';
                            } ?>
                            > <span><?php _e('Custom article settings', 'tollbridge'); ?></span>
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
    $plans = $manager->getActivePlans();
    ?>
    <table class="form-table tollbridge-override-settings <?php echo $class; ?>" role="presentation">
        <tbody>
            <tr class="tollbridge_global_option">
                <th scope="row"><?php _e('Only grant access to these plans', 'tollbridge'); ?></th>
            </tr>
            <tr class="tollbridge_global_option">
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
                                    .'value="'.$id.'" '.$checked.'> '
                                    .'<span>'.$plan.'</span>'
                                    .'</label><br />';
                            }
                            echo $content;
                        ?>
                        </label>
                        <br>
                    </fieldset>
                </td>
            </tr>

            <?php
            $time_access_change = $article->getTimeAccessChange();
            ?>
            <tr>
                <th scope="row"><?php _e('Change paywall access over time', 'tollbridge'); ?></th>
            </tr>
            <tr>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change" value="1" <?php
                            echo($time_access_change ? 'checked="checked"' : ''); ?>> <span><?php _e('Yes', 'tollbridge'); ?></span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change" value="0" <?php
                            echo(!$time_access_change ? 'checked="checked"' : ''); ?>> <span><?php _e('No', 'tollbridge'); ?></span>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <?php
            $class = '';
            if (!$time_access_change) {
                $class = 'hidden';
            }
            ?>
            <tr>
                <td>
                    <label class="tollbridge_time_access_dependent <?php echo $class; ?>">
                        <?php _e('After', 'tollbridge'); ?> <input type="number" name="tollbridge_time_access_delay" value="<?php echo $article->getTimeAccessDelay(); ?>" min="0" size="2"> <?php _e('days, change articles from:', 'tollbridge'); ?>
                    </label>
                </td>
            </tr>

            <?php
            $direction = $article->getTimeAccessChangeDirection();
            ?>
            <tr>
                <td>
                    <fieldset class="tollbridge_time_access_dependent <?php echo $class; ?>">
                        <label>
                            <input type="radio" name="tollbridge_time_access_change_direction" value="to_free" <?php
                            echo($direction == 'to_free' ? 'checked="checked"' : ''); ?>> <span><?php _e('Paywalled to free', 'tollbridge'); ?></span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change_direction" value="to_paid" <?php
                            echo($direction == 'to_paid' ? 'checked="checked"' : ''); ?>> <span><?php _e('Free to paywalled', 'tollbridge'); ?></span>
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
