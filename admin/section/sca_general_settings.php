<?php
/*
 *      SwiftCalendar General settings tab
 */
$scal_event_flag = get_option("scal_event_flag");
$event_flag = ($scal_event_flag == 1) ? 'checked="checked"' : '';
?>
<form name="FrmscalSettings" id="FrmscalSettings" method="post">
    <table class="form-table" id="tbl-event-settings">
        <tr>
            <th><label>Enable Events Custom Post Type</label></th>
            <td><input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="scal_event_flag" id="scal_event_flag" <?php echo $event_flag; ?>></td>
        </tr>

        <tr>
            <th colspan="2">
                <?php wp_nonce_field('scal_save_event_settings', 'scal_save_event_settings'); ?>
                <button type="submit" class="button-primary" id="scal-settings-btn" value="scal_settings" name="scal_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        jQuery('#scal_event_flag').rcSwitcher();
        jQuery('#scal_license').rcSwitcher().on({
            width: 80,
            height: 24,
            autoFontSize: true,
            'turnon.rcSwitcher': function(e, dataObj) {
                jQuery(".pro-license-wrap").fadeIn();
            },
            'turnoff.rcSwitcher': function(e, dataObj) {
                jQuery(".pro-license-wrap").fadeOut();
            }
        });
    });
</script>
