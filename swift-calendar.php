<?php
/*
 *  Plugin Name: Swift Calendar
 *  Plugin URL: http://SwiftCalendar.com?&pr=93
 *  Description: Easy Free Online Appointment Scheduler Software - add a corner widget, sidebar widget, or shortcode to allow visitors to schedule time in a calendar based on pre-set rules while subtracting appointments in your calendar. Premium (paid) features include reminders, payment options and more. http://SwiftCalendar.com/onboarding for setup help.
 *  Version: 1.3.3
 *  Author: Roger Vaughn, Tejas Hapani
 *  Author URI: https://Swiftcloud.ai
 *  Text Domain: swift-calendar
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    _e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'swift-calendar');
    exit;
}

define('SWIFTCAL_VERSION', '1.3.3');
define('SWIFTCAL__MINIMUM_WP_VERSION', '4.5');
define('SWIFTCAL__PLUGIN_URL', plugin_dir_url(__FILE__));
define('SWIFTCAL__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWIFTCAL_PLUGIN_PREFIX', 'scal_');

register_activation_hook(__FILE__, 'scal_install');
if (!function_exists('scal_install')) {

    function scal_install() {
        if (version_compare($GLOBALS['wp_version'], SWIFTCAL__MINIMUM_WP_VERSION, '<')) {
            add_action('admin_notices', create_function('', "
        echo '<div class=\"error\"><p>" . sprintf(esc_html__('Swift Calendar %s requires WordPress %s or higher.', 'swift-calendar'), SWIFTCAL_VERSION, SWIFTCAL__MINIMUM_WP_VERSION) . "</p></div>'; "));

            add_action('admin_init', 'scal_deactivate_self');

            function scal_deactivate_self() {
                if (isset($_GET["activate"]))
                    unset($_GET["activate"]);
                deactivate_plugins(plugin_basename(__FILE__));
            }

            return;
        }
        update_option('swift_calendar_db_version', SWIFTCAL_VERSION);

        scal_pre_load_data();
    }

}

add_action('plugins_loaded', 'scal_update_check');

if (!function_exists('scal_update_check')) {

    function scal_update_check() {
        if (get_option("swift_calendar_db_version") != SWIFTCAL_VERSION) {
            scal_install();
        }
    }

}

//Load admin modules
require_once 'admin/scal_admin.php';
require_once 'section/scal-preload.php';

/**
 *      Deactivation plugin
 */
register_deactivation_hook(__FILE__, 'scal_deactive_plugin');
if (!function_exists('scal_deactive_plugin')) {

    function scal_deactive_plugin() {

    }

}


register_uninstall_hook(__FILE__, 'scal_uninstall_callback');
if (!function_exists('scal_uninstall_callback')) {

    function scal_uninstall_callback() {
        global $wpdb;

        delete_option("swift_calendar_db_version");
        delete_option("scal_notice");

        // delete pages
        $pages = get_option('scal_pages');
        if ($pages) {
            $pages = explode(",", $pages);
            foreach ($pages as $pid) {
                wp_delete_post($pid, true);
            }
        }
        delete_option("scal_pages");

        /*
         * Delete cpt event_marketing and event_marketing_category
         */

        /* taxonomy */
        foreach (array('event_marketing_category') as $taxonomy) {
            $wpdb->delete(
                    $wpdb->term_taxonomy, array('taxonomy' => $taxonomy)
            );
        }

        /* Delete reviews posts */
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ('event_marketing');");
        $wpdb->query("DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;");

        /* Deregister swift reviews cpt */
        if (function_exists('unregister_post_type')) {
            unregister_post_type('event_marketing');
        }
    }

}

/**
 *      Enqueue style and scripts
 */
add_action('wp_enqueue_scripts', 'scal_enqueue_scripts_styles');

if (!function_exists('scal_enqueue_scripts_styles')) {

    function scal_enqueue_scripts_styles() {
        wp_enqueue_style('scal-public', plugins_url('/css/scal_public.css', __FILE__), '', '', '');
        wp_enqueue_style('swift-bs-modal', plugins_url('/css/scal_bs_modal.min.css', __FILE__), '', '', '');
        wp_enqueue_script('swiftcloud-bootstrap', "//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js", array('jquery'), '3.3.5', true);
        wp_enqueue_script('scal-custom', plugins_url('/js/scal_custom.js', __FILE__), array('jquery'), '', true);
    }

}


include 'swift-events-pagetemplater.php';
include 'section/swift-form-error-popup.php';
include 'section/scal-shortcodes.php';
include 'section/scal-widget-front.php';
include 'section/scal-function.php';
include 'section/scal-call-to-action-front.php';

// Add event custom post type to feed
function eventfeed_request($qv) {
    if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = array('post', 'press_release', 'event_marketing', 'swift_jobs', 'vhcard');
    return $qv;
}

add_filter('request', 'eventfeed_request');
?>