<?php

/*
 *      Load data with plugin active
 */

function scal_pre_load_data() {
    update_option('scal_event_flag', 1);
}

/*
 *      Load data after user permisssion
 */

function scal_initial_data() {
    global $wpdb;

    /**
     *   Auto generate pages
     */
    $page_id = 0;
    $page_id_array = array();

    $event_upcoming = wp_kses_post('[swiftcalendar_events_upcomingonly]');
    $event_recent = wp_kses_post('[swiftcalendar_events_historyonly]');
    $swift_events_rss_feed_content = wp_kses_post('This page is being used for RSS Feed');

    $pages_array = array(
        "events-upcoming" => array("title" => sanitize_text_field("Upcoming Events"), "content" => $event_upcoming, "slug" => "events-upcoming", "option" => "", "template" => ""),
        "events-recent" => array("title" => sanitize_text_field("Past Events"), "content" => $event_recent, "slug" => "events-recent", "option" => "", "template" => ""),
        "events-feed" => array("title" => sanitize_text_field("Events Feed"), "content" => $swift_events_rss_feed_content, "slug" => "events-feed", "option" => "event_marketing_feed_page_id", "template" => "rss-events-feed.php"),
    );

    foreach ($pages_array as $key => $page) {
        $page_data = array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_title' => $page['title'],
            'post_name' => $page['slug'],
            'post_content' => $page['content'],
            'comment_status' => 'closed'
        );

        $page_id = wp_insert_post($page_data);
        $page_id_array[] = $page_id;

        /* Set default template */
        if (isset($page['template']) && !empty($page['template'])) {
            update_post_meta($page_id, "_wp_page_template", sanitize_text_field($page['template']));
        }

        if (isset($page['option']) && !empty($page['option'])) {
            update_option($page['option'], sanitize_text_field($page_id));
        }
    }
    $scal_pages_ids = @implode(",", $page_id_array);
    if (!empty($scal_pages_ids)) {
        update_option('scal_pages', sanitize_text_field($scal_pages_ids));
    }
}

?>