<?php
/*
 *      Setting page
 */
add_action("init", "scal_settings_post_init");

function scal_settings_post_init() {
    if (isset($_POST['scal_license_nonce']) && wp_verify_nonce($_POST['scal_license_nonce'], 'scal_license_nonce')) {
        $scal_license = sanitize_text_field($_POST['scal_license']);
        $scal_license_flage = $scal_license == 1 ? "lite" : "pro";

        $update1 = update_option("scal_license", $scal_license_flage);

        if ($update1) {
            wp_redirect(admin_url("admin.php?page=scal_control_panel&update=1"));
            die;
        }
    }

    // SwiftCalendar General settings tab ******************************
    if (isset($_POST['scal_save_event_settings']) && wp_verify_nonce($_POST['scal_save_event_settings'], 'scal_save_event_settings')) {
        $scal_event_flag = sanitize_text_field(!empty($_POST['scal_event_flag']) ? 1 : 0);
        $update1 = update_option('scal_event_flag', $scal_event_flag);

        if ($update1) {
            wp_redirect(admin_url("admin.php?page=scal_control_panel&tab=scal-general-settings&update=1"));
            die;
        }
    }

    // Corner widget settings tab ******************************
    if (isset($_POST['save_calendar_widget']) && wp_verify_nonce($_POST['save_calendar_widget'], 'save_calendar_widget')) {
        $swift_settings['enable_calendar_widget'] = sanitize_text_field(!empty($_POST['swift_settings']['enable_calendar_widget']) ? '1' : '0');
        $swift_settings['cw_swiftcloud_user_id'] = sanitize_text_field($_POST['swift_settings']['cw_swiftcloud_user_id']);
        $swift_settings['cw_light_color'] = sanitize_text_field($_POST['swift_settings']['cw_light_color']);
        $swift_settings['cw_dark_color'] = sanitize_text_field($_POST['swift_settings']['cw_dark_color']);
        $swift_settings['cw_text_color'] = sanitize_text_field($_POST['swift_settings']['cw_text_color']);
        $swift_settings['cw_main_title'] = sanitize_text_field($_POST['swift_settings']['cw_main_title']);
        $swift_settings['cw_schedulet_btn_txt'] = sanitize_text_field($_POST['swift_settings']['cw_schedulet_btn_txt']);
        $swift_settings['enable_call_us_section'] = sanitize_text_field(!empty($_POST['swift_settings']['enable_call_us_section']) ? '1' : '0');
        $swift_settings['cw_call_us_section_text_content'] = wp_kses_post($_POST['swift_settings']['cw_call_us_section_text_content']);
        $swift_settings['enable_email_form_option'] = sanitize_text_field($_POST['swift_settings']['enable_email_form_option']);
        $swift_settings['cw_send_btn_txt'] = sanitize_text_field($_POST['swift_settings']['cw_send_btn_txt']);
        $swift_settings['cw_form_id'] = sanitize_text_field($_POST['swift_settings']['cw_form_id']);
        $swift_settings['cw_thank_you_url'] = esc_url_raw($_POST['swift_settings']['cw_thank_you_url']);
        $swift_settings['cw_widget_position'] = sanitize_text_field($_POST['swift_settings']['cw_widget_position']);
        $swift_settings['swift_cal_settings'] = 1;

        $update = update_option('swift_cal_settings', $swift_settings);
        if ($update) {
            wp_redirect(admin_url("admin.php?page=scal_control_panel&tab=scal-corner-widget-settings&update=1"));
            die;
        }
    }
}

if (!function_exists('scal_settings_callback')) {

    function scal_settings_callback() {
        $scal_license_flag = get_option("scal_license");
        $event_license_flag = ($scal_license_flag == "pro") ? 'checked="checked"' : '';
        $event_license_show = ($scal_license_flag == "pro") ? 'display:block;' : 'display:none;';
        ?>

        <div class="wrap">
            <h3>Settings</h3><hr>
            <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
                <div id="message" class="notice notice-success is-dismissible below-h2">
                    <p>Setting updated successfully.</p>
                </div>
                <?php
            }
            ?>
            <!--license block-->
            <div class="inner_content">
                <div class="sc-license-wrap bg-light-yellow">
                    <h4>License: Now running the <input type="checkbox" value="1" data-ontext="Pro" data-offtext="Lite" name="scal_license" id="scal_license" <?php echo $event_license_flag; ?>> Version.</h4>
                    <div class="pro-license-wrap" style="<?php echo $event_license_show; ?>">
                        <form id="frmEventProLicense" method="post">
                            <?php wp_nonce_field('scal_license_nonce', 'scal_license_nonce'); ?>
                            <input type="text" required="required" name="scal_pro_license" id="scal_pro_license" class="regular-text" /><button type="submit" id="btn_event_pro_license" class="button button-pro-license"><span class="dashicons dashicons-unlock"></span> Connect / Enable</button>
                        </form>
                    </div>
                </div>
            </div>
            <!--license block-->

            <div class="inner_content">
                <h2 class="nav-tab-wrapper" id="scal-setting-tabs">
                    <a class="nav-tab custom-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == "scal-general-settings") ? 'nav-tab-active' : ''; ?>" id="scal-general-settings-tab" href="#scal-general-settings">General Settings</a>
                    <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "scal-corner-widget-settings") ? 'nav-tab-active' : ''; ?>" id="scal-scal-corner-widget-tab" href="#scal-corner-widget-settings">Corner Widget</a>
                    <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "scal-help-support-settings") ? 'nav-tab-active' : ''; ?>" id="scal-help-support-tab" href="#scal-help-support-settings">Help & Support</a>
                </h2>

                <div class="tabwrapper">
                    <div id="scal-general-settings" class="panel <?php echo (!isset($_GET['tab']) || $_GET['tab'] == "scal-general-settings") ? 'active' : ''; ?>">
                        <?php include 'sca_general_settings.php'; ?>
                    </div>

                    <div id="scal-corner-widget-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "scal-corner-widget-settings") ? 'active' : ''; ?>">
                        <?php include 'scal_widget_settings.php'; ?>
                    </div>

                    <div id="scal-help-support-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "scal-help-support-settings") ? 'active' : ''; ?>">
                        <?php include 'scal_help_support_settings.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
?>