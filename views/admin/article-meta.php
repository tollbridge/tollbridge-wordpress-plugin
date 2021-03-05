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
                <th scope="row">Override Global Settings?</th>
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
                            > <span>Use global settings</span>
                        </label>
                        <br />
                        <label>
                            <input type="radio" name="tollbridge_override_global_rules" value="1"
                            <?php
                            if ($override) {
                                echo 'checked="checked" ';
                            } ?>
                            > <span>Custom article settings</span>
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
                <th scope="row">Only grant access to these plans</th>
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
                <th scope="row">Change paywall access over time</th>
            </tr>
            <tr>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change" value="1" <?php
                            echo($time_access_change ? 'checked="checked"' : ''); ?>> <span>Yes</span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change" value="0" <?php
                            echo(!$time_access_change ? 'checked="checked"' : ''); ?>> <span>No</span>
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
                        After <input type="number" name="tollbridge_time_access_delay" value="<?php echo $article->getTimeAccessDelay(); ?>" min="0" size="2"> days, change articles from:
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
                            echo($direction == 'to_free' ? 'checked="checked"' : ''); ?>> <span>Paywalled to free</span>
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="tollbridge_time_access_change_direction" value="to_paid" <?php
                            echo($direction == 'to_paid' ? 'checked="checked"' : ''); ?>> <span>Free to paywalled</span>
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
