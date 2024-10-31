<?php

/**
 *      Shortcode : [swiftcalendar_events_upcomingonly no_of_events="5" category="all"]
 *      - Display upcoming events listing form event cpt.
 *          - no_of_events : optional; N number of events; Default: 5;
 *          - category : optional; pass category slug, Can pass multiple comma separated category slug; Default: All categories;
 */
add_shortcode('swiftcalendar_events_upcomingonly', 'scal_swiftcalendar_events_upcomingonly_callback');
if (!function_exists('scal_swiftcalendar_events_upcomingonly_callback')) {

    function scal_swiftcalendar_events_upcomingonly_callback($atts) {
        wp_enqueue_style('sc-bootstrap', SWIFTCAL__PLUGIN_URL . 'css/bootstrap-grid.min.css');

        global $wpdb;

        $op = '';
        $a = shortcode_atts(
                array(
            'no_of_events' => '',
            'category' => '',
                ), $atts);
        extract($a);

        $category = sanitize_text_field($category);
        $cat_array = !empty($category) ? explode(",", $category) : '';
        $no_of_events = !empty($no_of_events) ? $no_of_events : "10";

        /*
         *      Category
         */
        $scal_cat_arr = array();
        if (!empty($cat_array)) {
            foreach ($cat_array as $scal_cat) {
                $cat = get_term_by('slug', $scal_cat, 'event_marketing_category');
                if ($cat) {
                    $scal_cat_arr[] = $cat->term_id;
                }
            }
        }
        $cat_join = (!empty($scal_cat_arr)) ? ' LEFT JOIN ' . $wpdb->prefix . 'term_relationships as tr ON (p.ID = tr.object_id) ' : '';
        $cat_join_in = (!empty($scal_cat_arr)) ? ' AND tr.term_taxonomy_id IN (' . @implode(",", $scal_cat_arr) . ') ' : '';

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $post_per_page = intval($no_of_events);
        $offset = ($paged - 1) * $post_per_page;


        $scal_qry = '   SELECT SQL_CALC_FOUND_ROWS p.* FROM ' . $wpdb->prefix . 'posts as p
                        LEFT JOIN ' . $wpdb->prefix . 'postmeta AS mt ON ( p.ID = mt.post_id )
                        ' . $cat_join . '
                        WHERE 1=1
                        AND p.post_type = "event_marketing"
                        AND p.post_status = "publish"
                        AND mt.meta_key = "scal_event_start"
                        ' . $cat_join_in . '
                        AND STR_TO_DATE(mt.meta_value, "%m-%d-%Y") >= STR_TO_DATE("' . date("m-d-Y") . '", "%m-%d-%Y")
                        GROUP BY p.ID
                        ORDER BY STR_TO_DATE(mt.meta_value,"%m-%d-%Y") ASC
                        LIMIT ' . $offset . ", " . $post_per_page;
        $scal_posts = $wpdb->get_results($scal_qry);
        $sql_posts_total = $wpdb->get_var("SELECT FOUND_ROWS();");
        $pages = ceil($sql_posts_total / $post_per_page);

        $op .= '<div class="scal-event-wrap">';
        $op .= '<div class="bootstrap-wrapper">';

        if ($scal_posts) {
            foreach ($scal_posts as $post):
                setup_postdata($post);

                // event featured image
                $url = '';
                if (has_post_thumbnail($post->ID)) {
                    $url = (get_the_post_thumbnail_url($post->ID, 'full'));
                }

                $start_date = get_post_meta($post->ID, 'scal_event_start', true);
                $event_date = date_parse_from_format("m-d-Y", $start_date);
                $month_name = date('M', mktime(0, 0, 0, $event_date['month'], 10));

                $op .= '<div class="scal-event row" id="scal-' . $post->ID . '">';
                if (!empty($url)) {
                    $op .= '<div class="col-lg-4 col-sm-12 scalImg">';
                    $op .= '    <a href="' . get_the_permalink($post->ID) . '"><img src="' . $url . '" alt="' . get_the_title($post->ID) . '" class="img-fluid" /></a>';
                    $op .= '</div>';
                }

                $op .= '<div class="' . ((!empty($url)) ? "col-lg-6" : "col-lg-10") . ' col-sm-12">';
                $op .= '    <div class="scal-event-title">';
                $op .= '        <h3><a href="' . get_the_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h3>';
                $op .= '    </div>';
                $op .= '    <div class="scal-event-content">';
                $op .= '        <a href="' . get_the_permalink($post->ID) . '">' . scal_get_excerpt(50) . '</a>';
                $op .= '    </div>';
                $op .= '</div>'; //col-6

                $op .= '<div class="col-lg-2">';
                $op .= '    <div class="scal-event-date-wrap">';
                $op .= '        <a href="' . get_the_permalink($post->ID) . '">';
                $op .= '            <div class="scal-event-date">' . $event_date['day'] . '</div>';
                $op .= '            <div class="scal-event-month">' . $month_name . '</div>';
                $op .= '            <div class="scal-event-year">' . $event_date['year'] . '</div>';
                $op .= '        </a>';
                $op .= '    </div>';
                $op .= '</div>';

                $op .= '</div>'; // row
            endforeach;


            // pagination
            $range = 2;
            $showitems = ($range * 2) + 1;

            if (1 != $pages) {
                $op .= '<div class="row">';
                $op .= '<div class="col-lg-12">';
                $op .= "<div class='swift_pagination'>";
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link(1) . "'>&laquo;</a>";
                }
                if ($paged > 1 && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a>";
                }

                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                        $op .= ($paged == $i) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a>";
                    }
                }

                if ($paged < $pages && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a>";
                }
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link($pages) . "'>&raquo;</a>";
                }
                $op .= "</div>";
                $op .= "</div>";
                $op .= "</div>";
            }
        } else {
            $op .= "<div class='row'><div class='col-lg-12'><h3>No events found.....yet</h3></div></div>";
        }

        $op .= '</div>'; // bootstrap-wrapper
        $op .= '</div>'; // event wrap
        return $op;
    }

}

/**
 *      Shortcode : [swiftcalendar_events_historyonly no_of_events="5" category="all"]
 *      - Display history events listing form event cpt.
 *          - no_of_events : optional; N number of events; Default: 5;
 *          - category : optional; pass category slug, Can pass multiple comma separated category slug; Default: All categories;
 */
add_shortcode('swiftcalendar_events_historyonly', 'scal_swiftcalendar_events_historyonly_callback');
if (!function_exists('scal_swiftcalendar_events_historyonly_callback')) {

    function scal_swiftcalendar_events_historyonly_callback($atts) {
        wp_enqueue_style('sc-bootstrap', SWIFTCAL__PLUGIN_URL . 'css/bootstrap-grid.min.css');

        global $wpdb;

        $op = '';
        $a = shortcode_atts(
                array(
            'no_of_events' => '',
            'category' => '',
                ), $atts);
        extract($a);

        $category = sanitize_text_field($category);
        $cat_array = !empty($category) ? explode(",", $category) : '';
        $no_of_events = !empty($no_of_events) ? $no_of_events : "10";

        /*
         *      Category
         */
        $scal_cat_arr = array();
        if (!empty($cat_array)) {
            foreach ($cat_array as $scal_cat) {
                $cat = get_term_by('slug', $scal_cat, 'event_marketing_category');
                if ($cat) {
                    $scal_cat_arr[] = $cat->term_id;
                }
            }
        }
        $cat_join = (!empty($scal_cat_arr)) ? ' LEFT JOIN ' . $wpdb->prefix . 'term_relationships as tr ON (p.ID = tr.object_id) ' : '';
        $cat_join_in = (!empty($scal_cat_arr)) ? ' AND tr.term_taxonomy_id IN (' . @implode(",", $scal_cat_arr) . ') ' : '';

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $post_per_page = intval($no_of_events);
        $offset = ($paged - 1) * $post_per_page;


        $scal_qry = '   SELECT SQL_CALC_FOUND_ROWS p.* FROM ' . $wpdb->prefix . 'posts as p
                        LEFT JOIN ' . $wpdb->prefix . 'postmeta AS mt ON ( p.ID = mt.post_id )
                        ' . $cat_join . '
                        WHERE 1=1
                        AND p.post_type = "event_marketing"
                        AND p.post_status = "publish"
                        AND mt.meta_key = "scal_event_start"
                        ' . $cat_join_in . '
                        AND STR_TO_DATE(mt.meta_value, "%m-%d-%Y") < STR_TO_DATE("' . date("m-d-Y") . '", "%m-%d-%Y")
                        GROUP BY p.ID
                        ORDER BY STR_TO_DATE(mt.meta_value,"%m-%d-%Y") ASC
                        LIMIT ' . $offset . ", " . $post_per_page;
        $scal_posts = $wpdb->get_results($scal_qry);
        $sql_posts_total = $wpdb->get_var("SELECT FOUND_ROWS();");
        $pages = ceil($sql_posts_total / $post_per_page);

        $op .= '<div class="scal-event-wrap">';
        $op .= '<div class="bootstrap-wrapper">';

        if ($scal_posts) {
            foreach ($scal_posts as $post):
                setup_postdata($post);

                // event featured image
                $url = '';
                if (has_post_thumbnail($post->ID)) {
                    $url = (get_the_post_thumbnail_url($post->ID, 'full'));
                }

                $start_date = get_post_meta($post->ID, 'scal_event_start', true);
                $event_date = date_parse_from_format("m-d-Y", $start_date);
                $month_name = date('M', mktime(0, 0, 0, $event_date['month'], 10));

                $op .= '<div class="scal-event row" id="scal-' . $post->ID . '">';
                if (!empty($url)) {
                    $op .= '<div class="col-lg-4 col-sm-12 scalImg">';
                    $op .= '    <a href="' . get_the_permalink($post->ID) . '"><img src="' . $url . '" alt="' . get_the_title($post->ID) . '" class="img-fluid" /></a>';
                    $op .= '</div>';
                }

                $op .= '<div class="' . ((!empty($url)) ? "col-lg-6" : "col-lg-10") . ' col-sm-12">';
                $op .= '    <div class="scal-event-title">';
                $op .= '        <h3><a href="' . get_the_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h3>';
                $op .= '    </div>';
                $op .= '    <div class="scal-event-content">';
                $op .= '        <a href="' . get_the_permalink($post->ID) . '">' . scal_get_excerpt(50) . '</a>';
                $op .= '    </div>';
                $op .= '</div>'; //col-6

                $op .= '<div class="col-lg-2">';
                $op .= '    <div class="scal-event-date-wrap">';
                $op .= '        <a href="' . get_the_permalink($post->ID) . '">';
                $op .= '            <div class="scal-event-date">' . $event_date['day'] . '</div>';
                $op .= '            <div class="scal-event-month">' . $month_name . '</div>';
                $op .= '            <div class="scal-event-year">' . $event_date['year'] . '</div>';
                $op .= '        </a>';
                $op .= '    </div>';
                $op .= '</div>';

                $op .= '</div>'; // row
            endforeach;


            // pagination
            $range = 2;
            $showitems = ($range * 2) + 1;

            if (1 != $pages) {
                $op .= '<div class="row">';
                $op .= '<div class="col-lg-12">';
                $op .= "<div class='swift_pagination'>";
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link(1) . "'>&laquo;</a>";
                }
                if ($paged > 1 && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a>";
                }

                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                        $op .= ($paged == $i) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a>";
                    }
                }

                if ($paged < $pages && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a>";
                }
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) {
                    $op .= "<a href='" . get_pagenum_link($pages) . "'>&raquo;</a>";
                }
                $op .= "</div>";
                $op .= "</div>";
                $op .= "</div>";
            }
        } else {
            $op .= "<div class='row'><div class='col-lg-12'><h3>No events found.....yet</h3></div></div>";
        }

        $op .= '</div>'; // bootstrap-wrapper
        $op .= '</div>'; // event wrap
        return $op;
    }

}
?>