<?php
global $post;
$global_rules_set = get_option('tollbridge_is_using_global_rules');
$meta = get_post_meta($post->ID);
$override = $meta['tollbridge_override_global_rules'][0] ?? false;

if ($global_rules_set) {
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Override Global Settings?</th>
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

$existing_plans = [];
if (isset($meta['tollbridge_plans_with_access'][0])) {
    $existing_plans = @unserialize($meta['tollbridge_plans_with_access'][0]);
    if (!is_array($existing_plans)) {
        $existing_plans = [];
    }
}
$manager = new \Tollbridge\Paywall\Manager();
$plans = $manager->getActivePlans();
?>
<table class="form-table tollbridge-override-settings <?php echo $class; ?>" role="presentation">
    <tbody>
        <tr class="tollbridge_global_option">
            <th scope="row">Only grant access to these plans</th>
            <td>
                <fieldset>
                    <label>
                    <?php
                    $content = '';
                    foreach ($plans as $plan) {
                        $checked = '';
                        if (in_array($plan, $existing_plans)) {
                            $checked = ' checked="checked"';
                        }
                        $content .= '<label>
                            <input type="checkbox" name="tollbridge_plans_with_access[]" '
                            .'value="'.$plan.'" '.$checked.'> '
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
        $time_access_change = $meta['tollbridge_time_access_change'][0] ?? 0;
        ?>
        <tr>
            <th scope="row">Change paywall access over time</th>
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change" value="1" <?php
                        echo ($time_access_change ? 'checked="checked"' : '');
                        ?>> <span>Yes</span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change" value="0" <?php
                        echo (!$time_access_change ? 'checked="checked"' : '');
                        ?>> <span>No</span>
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
            <th scope="row"></th>
            <td>
                <label class="tollbridge_time_access_dependent <?php echo $class; ?>">
                    After <input type="number" name="tollbridge_time_access_delay" value="<?php echo $meta['tollbridge_time_access_delay'][0] ?? 0; ?>" min="0"> days, change articles from:
                </label>
            </td>
        </tr>

        <?php
        $direction = $meta['tollbridge_time_access_change_direction'][0] ?? 'to_free';
        ?>
        <tr>
            <th scope="row"></th>
            <td>
                <fieldset class="tollbridge_time_access_dependent <?php echo $class; ?>">
                    <label>
                        <input type="radio" name="tollbridge_time_access_change_direction" value="to_free" <?php
                        echo ($direction == 'to_free' ? 'checked="checked"' : '');
                        ?>> <span>Paywalled to free</span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change_direction" value="to_paid" <?php
                        echo ($direction == 'to_paid' ? 'checked="checked"' : '');
                        ?>> <span>Free to paywalled</span>
                    </label>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
