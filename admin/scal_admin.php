<?php
/*
 *  Admin modul of SwiftCalendar
 */


/** On plugin activation notice * */
if (version_compare($GLOBALS['wp_version'], SWIFTCAL_VERSION, '>=')) {
    add_action('admin_notices', 'scal_admin_notice');
}
if (!function_exists('scal_admin_notice')) {

    function scal_admin_notice() {
        if (!get_option('scal_notice') && !get_option('scal_pages')) {
            ?>
            <div class="notice notice-success is-dismissible sc-admin-notice" id="scal-admin-notice">
                <p><b>Swift Calendar Plugin</b></p>
                <form method="post">
                    <p class="sc-notice-msg"><?php _e('Want to auto-create the following pages to quickly get you set up? ', 'swift-calendar'); ?></p>
                    <ul>
                        <li>Events - Upcoming</li>
                        <li>Events - Recent</li>
                        <li>Events RSS Feed</li>
                    </ul>
                    <?php wp_nonce_field('scal_autogen_pages', 'scal_autogen_pages'); ?>
                    <button type="submit" value="yes" name="scal_autogen_yes" class="button button-green"><span class="dashicons dashicons-yes"></span> Yes</button>  <button type="submit" name="scal_autogen_no" value="no" class="button button-default button-red"><i class="fa fa-ban"></i> No</button>
                </form>
            </div>
            <?php
        }
    }

}

/**
 *      Admin menu
 */
add_action('admin_menu', 'scal_control_panel');
if (!function_exists('scal_control_panel')) {

    function scal_control_panel() {
        $icon_url = plugins_url('/images/swiftcloud.png', __FILE__);
        $parent_menu_slug = 'scal_control_panel';
        //$parent_menu_slug = 'edit.php?post_type=event_marketing';
        $menu_capability = 'manage_options';

        add_menu_page('Swift Calendar', 'Swift Calendar', $menu_capability, $parent_menu_slug, 'scal_settings_callback', $icon_url, 26);
        add_submenu_page($parent_menu_slug, "Settings", "Settings", $menu_capability, $parent_menu_slug, '');

        //cpt menu
        $scal_event_flag = get_option("scal_event_flag");
        if ($scal_event_flag == 1) {
            add_submenu_page($parent_menu_slug, "All Events", "All Events", $menu_capability, "edit.php?post_type=event_marketing", null);
            add_submenu_page($parent_menu_slug, "Add New Event", "Add New Event", $menu_capability, "post-new.php?post_type=event_marketing", null);
            add_submenu_page($parent_menu_slug, "Categories", "Categories", $menu_capability, "edit-tags.php?taxonomy=event_marketing_category&post_type=event_marketing", null);
        }
        add_submenu_page($parent_menu_slug, "Call to Action", "Call to Action", 'manage_options', 'scal_call_to_action', 'scal_call_to_action_callback');
        add_submenu_page($parent_menu_slug, "Updates & Tips", "Updates & Tips", 'manage_options', 'scal_dashboard', 'scal_dashboard_callback');
    }

}

/**
 *      Set current menu selected
 */
add_filter('parent_file', 'scal_set_current_menu');
if (!function_exists('scal_set_current_menu')) {

    function scal_set_current_menu($parent_file) {
        global $submenu_file, $current_screen, $pagenow;

        if ($current_screen->post_type == 'event_marketing') {
            if ($pagenow == 'post.php') {
                $submenu_file = "edit.php?post_type=" . $current_screen->post_type;
            }
            if ($pagenow == 'edit-tags.php') {
                if ($current_screen->taxonomy == 'event_marketing_category') {
                    $submenu_file = "edit-tags.php?taxonomy=event_marketing_category&post_type=" . $current_screen->post_type;
                }
            }
            $parent_file = 'scal_control_panel';
        }
        return $parent_file;
    }

}


/*
 *      Enqueue scripts and styles
 */
add_action('admin_enqueue_scripts', 'scal_admin_enqueue');
if (!function_exists('scal_admin_enqueue')) {

    function scal_admin_enqueue($hook) {
        wp_enqueue_style('scal-admin-style', plugins_url('/css/scal_admin.css', __FILE__), '', '', '');
        wp_enqueue_script('scal-admin-custom', plugins_url('/js/scal_admin.js', __FILE__), array('jquery'), '', true);

        wp_enqueue_style('swift-toggle-style', plugins_url('/css/scal_rcswitcher.css', __FILE__), '', '', '');
        wp_enqueue_script('swift-toggle', plugins_url('/js/scal_rcswitcher.js', __FILE__), array('jquery'), '', true);

        wp_enqueue_style('swiftcloud-colorpicker-style', plugins_url('/css/scal_spectrum.css', __FILE__), '', '', '');
        wp_enqueue_script('swiftcloud-colorpicker', plugins_url('/js/scal_spectrum.js', __FILE__), array('jquery'), '', true);

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('scal-jquery-ui', plugins_url('/css/scal_jquery-ui.css', __FILE__), '', '', '');
        wp_localize_script('scal-admin-custom', 'scal_admin_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));

        wp_enqueue_script('scal-tab-script', plugins_url('/js/sc_tab.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('jquery-ui-timepicker', plugins_url('/js/jquery-ui-timepicker-addon.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('jquery-ui-slider');
    }

}

include_once 'section/cpt_event_marketing.php';
include_once 'section/swift_dashboard.php';
include_once 'section/scal_settings.php';
include_once 'section/scal_widget.php';
include_once 'section/scal_call_to_action.php';
//include_once 'section/scal_widget_settings.php';

/*
 *      Init
 */
add_action("init", "scal_admin_forms_submit");

function scal_admin_forms_submit() {
    $events_args = array(
        'post_type' => 'event_marketing',
        'posts_per_page' => -1,
    );
    $scal_posts = new WP_Query($events_args);
    while ($scal_posts->have_posts()) {
        $scal_posts->the_post();
        $start_date = esc_attr(get_post_meta(get_the_ID(), 'scal_event_start', true));
        $parsed = date_parse_from_format('m-d-Y H:mA', $start_date);
//        print_r($parsed);
        if ($parsed['error_count'] <= 0) {
            $old_date_timestamp = mktime(
                    $parsed['hour'], $parsed['minute'], $parsed['second'], $parsed['month'], $parsed['day'], $parsed['year']
            );
//            echo " >>".$new_date = date('Y-m-d H:i:s', $old_date_timestamp);
//            update_post_meta(get_the_ID(), 'scal_event_start', $new_date, $start_date);
        }
    }


    /* update old swift cal. settings to new option */
    $swift_cal_settings = get_option('swift_cal_settings');
    if (empty($swift_cal_settings)) {
        $swift_cal_settings = get_option('swift_settings');
        update_option('swift_cal_settings', $swift_cal_settings);
    }

    /* on plugin active auto generate pages and options */
    if (isset($_POST['scal_autogen_pages']) && wp_verify_nonce($_POST['scal_autogen_pages'], 'scal_autogen_pages')) {
        if (isset($_POST['scal_autogen_yes']) && $_POST['scal_autogen_yes'] == 'yes') {
            scal_initial_data();
        }
        update_option('scal_notice', true);
    }
}

/* Dismiss notice callback */
add_action('wp_ajax_scal_dismiss_notice', 'scal_dismiss_notice_callback');
add_action('wp_ajax_nopriv_scal_dismiss_notice', 'scal_dismiss_notice_callback');

function scal_dismiss_notice_callback() {
    update_option('scal_notice', true);
    wp_die();
}
?>