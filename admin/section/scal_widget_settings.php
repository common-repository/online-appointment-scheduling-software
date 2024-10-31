<?php
/*
 *      Widget control panel
 */

wp_enqueue_style('scal-public', SWIFTCAL__PLUGIN_URL . 'css/scal_public.css', '', '', '');
$swift_settings = get_option('swift_cal_settings');
?>
<form name="frm_calendar_widget" id="frm_calendar_widget" method="post">
    <table class="form-table">
        <tr>
            <th><label for="enable_calendar_widget">Calendar Widget</label></th>
            <td class="cal-on-off">
                <?php $widgetOnOff = (isset($swift_settings['enable_calendar_widget']) && !empty($swift_settings['enable_calendar_widget']) && $swift_settings['enable_calendar_widget'] == 1 ? 'checked="checked"' : ""); ?>
                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[enable_calendar_widget]" id="enable_calendar_widget" class="enable_calendar_widget" <?php echo $widgetOnOff; ?>>
            </td>
        </tr>
    </table>
    <table  class="form-table" id="cw_table2" style="<?php echo (isset($swift_settings['enable_calendar_widget']) && $swift_settings['enable_calendar_widget'] == 1) ? "display:block;" : "display:none;"; ?>">
        <tr>
            <th><label for="cw_swiftcloud_user_id">Username</label></th>
            <td><input type="text" id="cw_swiftcloud_user_id" value="<?php echo (isset($swift_settings['cw_swiftcloud_user_id']) && !empty($swift_settings['cw_swiftcloud_user_id'])) ? $swift_settings['cw_swiftcloud_user_id'] : "" ?>" name="swift_settings[cw_swiftcloud_user_id]" class="regular-text" placeholder=""/></td>
        </tr>
        <tr>
            <th><label for="cw_dark_color">Widget Header Color</label></th>
            <td><input type="text" id="cw_dark_color" value="<?php echo!empty($swift_settings['cw_dark_color']) ? $swift_settings['cw_dark_color'] : '#ff7200' ?>" name="swift_settings[cw_dark_color]" class="regular-text" placeholder="#ffe701"/></td>
        </tr>
        <tr>
            <th><label for="cw_light_color">Button Color</label></th>
            <td><input type="text" id="cw_light_color" value="<?php echo!empty($swift_settings['cw_light_color']) ? $swift_settings['cw_light_color'] : "#072c52" ?>" name="swift_settings[cw_light_color]" class="regular-text" placeholder="#072c52"/></td>
        </tr>
        <tr>
            <th><label for="cw_text_color">Text Color</label></th>
            <td><input type="text" id="cw_text_color" value="<?php echo!empty($swift_settings['cw_text_color']) ? $swift_settings['cw_text_color'] : '#072c52' ?>" name="swift_settings[cw_text_color]" class="regular-text" placeholder="#072c52"/></td>
        </tr>
        <tr>
            <th><label for="cw_widget_position">Widget Position</label></th>
            <td>
                <select id="cw_widget_position" name="swift_settings[cw_widget_position]">
                    <option value="right" <?php echo (isset($swift_settings['cw_widget_position']) && $swift_settings['cw_widget_position'] == 'right' ? 'selected="selected"' : ''); ?>>Right</option>
                    <option value="center" <?php echo (isset($swift_settings['cw_widget_position']) && $swift_settings['cw_widget_position'] == 'center' ? 'selected="selected"' : ''); ?>>Center</option>
                    <option value="left" <?php echo (isset($swift_settings['cw_widget_position']) && $swift_settings['cw_widget_position'] == 'left' ? 'selected="selected"' : ''); ?>>Left</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="cw_main_title">Widget Offer / Call-to-Action Text</label></th>
            <td><input type="text" id="cw_main_title" value="<?php echo (!empty($swift_settings['cw_main_title']) ? $swift_settings['cw_main_title'] : 'Free 1-on-1 Consultation'); ?>" class="regular-text" name="swift_settings[cw_main_title]" /></td>
        </tr>
        <tr>
            <th><label for="cw_schedulet_btn_txt">Scheduler Button Text</label></th>
            <td><input type="text" id="cw_schedulet_btn_txt" value="<?php echo (!empty($swift_settings['cw_schedulet_btn_txt']) ? $swift_settings['cw_schedulet_btn_txt'] : 'Book Your Session Now'); ?>" class="regular-text" name="swift_settings[cw_schedulet_btn_txt]"  /></td>
        </tr>
        <tr>
            <th><label for="enable_call_us_section">Call us Section </label></th>
            <td>
                <?php $callusToggle = (isset($swift_settings['enable_call_us_section']) && $swift_settings['enable_call_us_section'] == 1 ? 'checked="checked"' : ""); ?>
                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[enable_call_us_section]" id="enable_call_us_section" class="enable_call_us_section" <?php echo $callusToggle; ?>/>
            </td>
        </tr>
        <tr id="cw_html_editor_tr" style="<?php echo (isset($swift_settings['enable_call_us_section']) && $swift_settings['enable_call_us_section'] != 1) ? "display:none;" : ''; ?>">
            <th><label for="cw_call_us_section_text">Call us Section Text</label></th>
            <td>
                <input style="display:none;" type="radio" class="" name="cw_call_us_section_text" value="html_content" />
                <?php
                $settings = array('media_buttons' => true, 'quicktags' => true, 'textarea_name' => 'swift_settings[cw_call_us_section_text_content]', 'editor_height' => 200);
                $call_us_content = isset($swift_settings['cw_call_us_section_text_content']) ? $swift_settings['cw_call_us_section_text_content'] : "";
                wp_editor(stripslashes($call_us_content), 'cw_call_us_section_text_id', $settings)
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="enable_email_form_option">Email Form Option </label></th>
            <td>
                <?php $emailToggle = (isset($swift_settings['enable_email_form_option']) && $swift_settings['enable_email_form_option'] == 1 ? 'checked="checked"' : ""); ?>
                <input type="checkbox" value="1"  data-ontext="ON" data-offtext="OFF" name="swift_settings[enable_email_form_option]" id="enable_email_form_option" class="enable_email_form_option" <?php echo $emailToggle; ?>/>
            </td>
        </tr>
        <tr class="cw_form_id_tr" style="<?php echo (isset($swift_settings['enable_email_form_option']) && $swift_settings['enable_email_form_option'] != 1) ? "display:none;" : ''; ?>">
            <th><label for="cw_form_id">Form ID number</label></th>
            <td><input type="text" id="cw_form_id" value="<?php echo (isset($swift_settings['cw_form_id'])) ? $swift_settings['cw_form_id'] : ""; ?>" name="swift_settings[cw_form_id]" class="regular-text"/></td>
        </tr>
        <tr class="cw_form_id_tr" style="<?php echo (isset($swift_settings['enable_email_form_option']) && $swift_settings['enable_email_form_option'] != 1) ? "display:none;" : ''; ?>">
            <th><label for="cw_send_btn_txt">Send Button Text</label></th>
            <td><input type="text" id="cw_send_btn_txt" value="<?php echo (!empty($swift_settings['cw_send_btn_txt']) ? $swift_settings['cw_send_btn_txt'] : 'SEND'); ?>" name="swift_settings[cw_send_btn_txt]" class="regular-text" /></td>
        </tr>
        <tr class="cw_form_id_tr" style="<?php echo (isset($swift_settings['enable_email_form_option']) && $swift_settings['enable_email_form_option'] != 1) ? "display:none;" : ''; ?>">
            <th><label for="cw_thank_you_url">Email Us Confirmation / Thank-You URL</label></th>
            <td><input type="text" value="<?php echo (!empty($swift_settings['cw_thank_you_url']) ? $swift_settings['cw_thank_you_url'] : home_url() . '/thanks-calendar'); ?>" id="cw_thank_you_url" name="swift_settings[cw_thank_you_url]" class="regular-text"/></td>
        </tr>
    </table>
    <?php wp_nonce_field('save_calendar_widget', 'save_calendar_widget') ?>
    <input type="submit" id="scal_save_btn"  class="button button-primary" value="Save Changes" />
    <input type="button" id="scal_preview_btn" class="button button-primary" value="Preview" />
</form>
<div id="scal_preview">
    <?php
    $widget_header_color = !empty($swift_settings['cw_dark_color']) ? $swift_settings['cw_dark_color'] : '#FF7200';
    $button_color = !empty($swift_settings['cw_light_color']) ? $swift_settings['cw_light_color'] : '#FF7200';
    $text_color = !empty($swift_settings['cw_text_color']) ? $swift_settings['cw_text_color'] : '#4b4e55';
    ?>
    <div class="cw-calendar-widget" id="cwCalendarWidget">
        <div class="cw-main-title" style="background-color: <?php echo $widget_header_color; ?>">
            <h2><?php echo $swift_settings['cw_main_title']; ?></h2>
            <span class="cw-widget-toggle">+</span>
        </div>
        <div class="cw-calendar-widget-contet" style="display: none;color:<?php echo $text_color; ?>">
            <div class="cw-schedule-online">
                <div class="cw-schedule-online-title"><p>Schedule Online <i class="fa fa-calendar"></i></p></div>
                <button name="cw_book_ur_session_now" id="cw_book_now_btn" style="background-color: <?php echo $button_color; ?>"><?php echo $swift_settings['cw_schedulet_btn_txt']; ?></button>
            </div>
            <?php if ($swift_settings['enable_call_us_section']) { ?>
                <div class="cw-or">
                    <div class="cw-or-line">
                        <div class="cw-or-text"><p>or</p></div>
                    </div>
                </div>
                <div class="cw-call-us">
                    <div class="cw-call-us-title">
                        <?php echo nl2br($swift_settings['cw_call_us_section_text_content']); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($swift_settings['enable_email_form_option']) { ?>
                <div class="cw-or">
                    <div class="cw-or-line">
                        <div class="cw-or-text"><p>or</p></div>
                    </div>
                </div>
                <div class="cw-email-us">
                    <div class="cw-email-us-title"><p>Email Us</p></div>
                    <div class="cw-email-form">
                        <textarea name="cw_textarea" placeholder="How can we help?"></textarea>
                        <input type="text" id="" name="cw_phone_field"  placeholder="Phone"/>
                        <input type="email" id="" name="cw_email_us_field" required="required" placeholder="Email Address"/>
                        <input type="hidden" id="" name="formID" value="<?php echo $swift_settings['cw_form_id']; ?>" />
                        <button type="button" name="cw_send_btn" class="cw_send_btn" style="background-color: <?php echo $button_color; ?>"><i class="fa fa-paper-plane"></i> <?php echo (!empty($swift_settings['cw_send_btn_txt']) ? $swift_settings['cw_send_btn_txt'] : 'SEND'); ?></button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        //Color pickers
        jQuery("#cw_light_color").spectrum({
            preferredFormat: "hex",
            color: "<?php echo (!empty($swift_settings['cw_light_color']) ? $swift_settings['cw_light_color'] : '#072c52'); ?>",
            showAlpha: true,
            showButtons: false
        });
        jQuery("#cw_dark_color").spectrum({
            preferredFormat: "hex",
            color: "<?php echo (!empty($swift_settings['cw_dark_color']) ? $swift_settings['cw_dark_color'] : '#FF7200'); ?>",
            showAlpha: true,
            showButtons: false
        });
        jQuery("#cw_text_color").spectrum({
            preferredFormat: "hex",
            color: "<?php echo (!empty($swift_settings['cw_text_color']) ? $swift_settings['cw_text_color'] : '#072c52'); ?>",
            showAlpha: true,
            showButtons: false
        });

        //Toggle = Calendar Widget Show/Hide
        jQuery('.enable_calendar_widget:checkbox').rcSwitcher().on({
            'turnon.rcSwitcher': function (e, dataObj) {
                // to do on turning on a switch
                jQuery("#cw_table2").fadeIn();
            },
            'turnoff.rcSwitcher': function (e, dataObj) {
                // to do on turning off a switch
                jQuery("#cw_table2").fadeOut();
            }
        });
        //Toggle = Html editor show/hide
        jQuery('.enable_call_us_section:checkbox').rcSwitcher().on({
            'turnon.rcSwitcher': function (e, dataObj) {
                // to do on turning on a switch
                jQuery("#cw_html_editor_tr").fadeIn();
            },
            'turnoff.rcSwitcher': function (e, dataObj) {
                // to do on turning off a switch
                jQuery("#cw_html_editor_tr").fadeOut();
            }
        });
        // Toggle = Form id show/hide
        jQuery('.enable_email_form_option:checkbox').rcSwitcher().on({
            'turnon.rcSwitcher': function (e, dataObj) {
                // to do on turning on a switch
                jQuery(".cw_form_id_tr").fadeIn();
            },
            'turnoff.rcSwitcher': function (e, dataObj) {
                // to do on turning off a switch
                jQuery(".cw_form_id_tr").fadeOut();
            }
        });


        jQuery(".emailOptError").hide();
        jQuery("#frm_calendar_widget").submit(function (e) {
            jQuery(".emailOptError").hide();
            if (jQuery('.enable_calendar_widget:checkbox').is(':checked')) {
                if (jQuery.trim(jQuery("#cw_swiftcloud_user_id").val()) === '') {
                    jQuery("#frm_calendar_widget").before('<div id="" class="error emailOptError"><p>Heads up! You need to define your username for this to work - please click here to fix this now.</p></div>');
                    jQuery("#cw_swiftcloud_user_id").focus();
                    e.preventDefault();
                }
            }
            if (jQuery('.enable_email_form_option:checkbox').is(':checked')) {
                if (jQuery.trim(jQuery("#cw_form_id").val()) === '') {
                    jQuery("#frm_calendar_widget").before('<div id="" class="error emailOptError"><p>Form ID is Required to Enable This Function. Please visit <a href="https://SwiftCloud.AI?&pr=92">SwiftCloud.AI</a> (free or paid accounts will work) to generate this form.</p></div>');
                    jQuery("#cw_form_id").focus();
                    e.preventDefault();
                }
            }

        });

        // Calendar Preview
        jQuery("#scal_preview_btn").on('click', function () {
            jQuery("#scal_preview").fadeIn();
            jQuery(".cw-main-title").css("background-color", jQuery("#cw_dark_color").val());
            jQuery("#cw_book_now_btn").css("background-color", jQuery("#cw_light_color").val());
            jQuery(".cw_send_btn").css("background-color", jQuery("#cw_light_color").val());
            jQuery(".cw-calendar-widget-contet").css("color", jQuery("#cw_text_color").val());

        });
        jQuery(".cw-main-title").on("click", function () {
            jQuery(".cw-calendar-widget-contet").slideToggle('slow', function () {
                jQuery(".cw-widget-toggle").text(jQuery(this).is(':visible') ? "-" : "+");
            });
        });

    });
</script>

