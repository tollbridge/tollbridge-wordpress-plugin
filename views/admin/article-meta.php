<?php
global $post;
$manager          = new \Tollbridge\Paywall\Manager();
$global_rules_set = $manager->globalSettingsAreActive();
$article          = new \Tollbridge\Paywall\Frontend\Article();
$article->setId( $post->ID );
$override = $article->hasMetaOverride();

if ( $global_rules_set ) {
    ?>
    <table class="form-table" role="presentation">
        <tbody>
        <tr>
            <th scope="row"><?php _e( 'Override Global Settings?', 'tollbridge' ); ?></th>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="tollbridge_override_global_rules" value="0"
                            <?php
                            if ( !$override ) {
                                echo 'checked="checked" ';
                            } ?>
                        > <span><?php _e( 'Use Global Settings', 'tollbridge' ); ?></span>
                    </label>
                    <br />
                    <label>
                        <input type="radio" name="tollbridge_override_global_rules" value="1"
                            <?php
                            if ( $override ) {
                                echo 'checked="checked" ';
                            } ?>
                        > <span><?php _e( 'Custom Article Settings', 'tollbridge' ); ?></span>
                    </label>
                    <br>
                </fieldset>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}

$classGlobal = ( $global_rules_set && !$override ) ? 'hidden' : '';
try {
    $existing_plans = $article->getPlansWithAccess();
    $plans          = $manager->getActivePlans(); ?>
    <table class="form-table tollbridge-override-settings <?php echo $classGlobal; ?>" role="presentation">
        <tbody>
        <tr class="tollbridge_global_option">
            <th scope="row"><?php _e( 'Only Grant Access To These Plans', 'tollbridge' ); ?></th>
        </tr>
        <tr class="tollbridge_global_option">
            <td>
                <fieldset>
                    <label>
                        <?php
                        $content = '';

                        foreach ( $plans as $id => $plan ) {
                            $checked = '';

                            if ( in_array( $id, $existing_plans ) ) {
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

        <?php $disablePaywall = $article->isDisableLeakyPaywall(); ?>
        <tr class="tollbridge_global_option">
            <th scope="row"><?php _e( 'Change Paywall Type', 'tollbridge' ); ?></th>
        </tr>
        <tr class="tollbridge_global_option">
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="tollbridge_disable_leaky_paywall" value="0" <?php
                        echo !$disablePaywall ? 'checked="checked"' : ''; ?>> <span><?php _e( 'No (Use Platform Default)', 'tollbridge' ); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="tollbridge_disable_leaky_paywall" value="1" <?php
                        echo $disablePaywall ? 'checked="checked"' : ''; ?>> <span><?php _e( 'Hard Paywall', 'tollbridge' ); ?></span>
                    </label>
                </fieldset>
            </td>
        </tr>

        <?php $changeMessagePaywall = $article->isChangeMessagePaywall(); ?>
        <tr class="tollbridge_global_option">
            <th scope="row"><?php _e( 'Change Paywall Widget Text', 'tollbridge' ); ?></th>
        </tr>
        <tr class="tollbridge_global_option">
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="tollbridge_change_message_paywall" value="0" <?php
                        echo !$changeMessagePaywall ? 'checked="checked"' : ''; ?>> <span><?php _e( 'No (Use Platform Default)', 'tollbridge' ); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="tollbridge_change_message_paywall" value="1" <?php
                        echo $changeMessagePaywall ? 'checked="checked"' : ''; ?>> <span><?php _e( 'Yes', 'tollbridge' ); ?></span>
                    </label>
                    <?php if ($changeMessagePaywall): ?>
                        <br>
                         <?php endif; ?>
                </fieldset>
            </td>
        </tr>
        <tr class="tollbridge_global_option tollbridge_change_message_paywall <?php echo ($changeMessagePaywall ? '' : 'hidden') ; ?>">
            <td>
                <fieldset>
                    <label for="message_paywall_title"><?php _e('Title', 'tollbridge'); ?></label><br>
                    <input style="width: 100%" id="message_paywall_title" name="tollbridge_message_paywall_title" type="text" placeholder="<?php echo $manager->getPaywallTitle(); ?>" value="<?php echo $article->getPaywallTitle() ?: $manager->getPaywallTitle(); ?>">
                    <br>
                    <label for="message_paywall_body"><?php _e('Text', 'tollbridge'); ?></label><br>
                    <textarea style="padding: 8px; width: 100%" id="message_paywall_body" rows="5" name="tollbridge_message_paywall_body" placeholder="<?php echo $manager->getPaywallBody(); ?>"><?php echo $article->getPaywallBody() ?: $manager->getPaywallBody(); ?></textarea>
                </fieldset>
            </td>
        </tr>

        <?php $time_access_change = $article->getTimeAccessChange(); ?>
        <tr>
            <th scope="row"><?php _e( 'Change Paywall Access Over Time', 'tollbridge' ); ?></th>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change" value="1" <?php
                        echo $time_access_change ? 'checked="checked"' : ''; ?>> <span><?php _e( 'Yes', 'tollbridge' ); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change" value="0" <?php
                        echo !$time_access_change ? 'checked="checked"' : ''; ?>> <span><?php _e( 'No', 'tollbridge' ); ?></span>
                    </label>
                </fieldset>
            </td>
        </tr>

        <?php
        $class = '';

        if ( !$time_access_change ) {
            $class = 'hidden';
        } ?>
        <tr class="tollbridge_global_option tollbridge_time_access_dependent <?php echo $class; ?>">
            <td>
                <label>
                    <?php _e( 'After', 'tollbridge' ); ?> <input type="number" name="tollbridge_time_access_delay" value="<?php echo $article->getTimeAccessDelay(); ?>" min="0" size="2"> <?php _e( 'days, change articles from:', 'tollbridge' ); ?>
                </label>
            </td>
        </tr>

        <?php $direction = $article->getTimeAccessChangeDirection(); ?>
        <tr class="tollbridge_global_option tollbridge_time_access_dependent <?php echo $class; ?>">
            <td>
                <fieldset>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change_direction" value="to_free" <?php
                        echo $direction == 'to_free' ? 'checked="checked"' : ''; ?>> <span><?php _e( 'Paywalled to free', 'tollbridge' ); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="tollbridge_time_access_change_direction" value="to_paid" <?php
                        echo $direction == 'to_paid' ? 'checked="checked"' : ''; ?>> <span><?php _e( 'Free to paywalled', 'tollbridge' ); ?></span>
                    </label>
                </fieldset>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
} catch ( \Exception $e ) {
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
