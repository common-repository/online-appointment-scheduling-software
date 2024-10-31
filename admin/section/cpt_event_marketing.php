<?php
/*
 *      CPT: Event Marketing
 */

add_action('init', 'cpt_event_marketing');
if (!function_exists('cpt_event_marketing')) {

    function cpt_event_marketing() {
        $icon_url = plugins_url('../images/swiftcloud.png', __FILE__);
        $labels = array(
            'name' => _x('Event Marketing', 'post type general name', 'swift-calendar'),
            'singular_name' => _x('Event', 'post type singular name', 'swift-calendar'),
            'menu_name' => _x('Swift Calendar', 'admin menu', 'swift-calendar'),
            'add_new' => _x('Add New', '', 'swift-calendar'),
            'add_new_item' => __('Add New', 'swift-calendar'),
            'new_item' => __('New Event', 'swift-calendar'),
            'edit_item' => __('Edit Event', 'swift-calendar'),
            'view_item' => __('View Event', 'swift-calendar'),
            'all_items' => __('All Events', 'swift-calendar'),
            'search_items' => __('Search Event', 'swift-calendar'),
            'not_found' => __('No events found.', 'swift-calendar'),
            'not_found_in_trash' => __('No events found in trash.', 'swift-calendar')
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => true,
            'menu_icon' => __($icon_url, 'swift-calendar'),
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail'),
            'taxonomies' => array('event_marketing_category'),
            'rewrite' => array('slug' => 'event_marketing')
        );
        register_post_type('event_marketing', $args);

        /* -------------------------------------
         *      Add new taxonomy
         */
        $cat_labels = array(
            'name' => _x('Categories', 'taxonomy general name'),
            'singular_name' => _x('Category', 'taxonomy singular name'),
            'add_new_item' => __('Add New Category'),
            'new_item_name' => __('New Category Name'),
            'menu_name' => __('Categories'),
            'search_items' => __('Search Category'),
            'not_found' => __('No Category found.'),
        );

        $cat_args = array(
            'hierarchical' => true,
            'labels' => $cat_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'event_marketing_category'),
        );

        register_taxonomy('event_marketing_category', 'event_marketing', $cat_args);

        $default_cat = array(
            "Live Events" => array("slug" => "live_events",
                'child_cats' => array(
                    "On Site" => array("slug" => "on_site"),
                    "Off Site" => array("slug" => "off_site")
                )
            ),
            "Webcasts" => array("slug" => "webcasts"),
            "Future Events" => array("slug" => "future_events"),
            "Past Events" => array("slug" => "past_events"),
        );
        foreach ($default_cat as $d_cat_key => $d_cat_val) {
            $scal_cats = wp_insert_term($d_cat_key, "event_marketing_category", array('slug' => $d_cat_val['slug'], 'parent' => 0));
            if (isset($d_cat_val) && !empty($d_cat_val) && is_array($d_cat_val) && array_key_exists('child_cats', $d_cat_val)) {
                if (!empty($scal_cats)) {
                    if (!is_wp_error($scal_cats)) {
                        $parent_id = is_array($scal_cats) ? $scal_cats['term_id'] : $scal_cats->term_id;
                        foreach ($d_cat_val['child_cats'] as $child_cat_key => $child_cat_val) {
                            wp_insert_term($child_cat_key, "event_marketing_category", array('slug' => $child_cat_val['slug'], 'parent' => $parent_id));
                        }
                    }
                }
            }
        }
    }

}



add_filter('single_template', 'scal_plugin_templates_callback');
if (!function_exists('scal_plugin_templates_callback')) {

    function scal_plugin_templates_callback($template) {
        $post_types = array('event_marketing');
        if (is_singular($post_types) && !file_exists(get_stylesheet_directory() . '/single-event_marketing.php')) {
            $template = SWIFTCAL__PLUGIN_DIR . "section/single-event_marketing.php";
        }
        return $template;
    }

}


/*
 *  Custom field ::
 *      - Event start
 *      - Event Duration
 */
add_action('add_meta_boxes', 'scal_metaboxes');
if (!function_exists('scal_metaboxes')) {

    function scal_metaboxes() {
        add_meta_box('scal_events', 'SwiftCloud Calendar / Events', 'scal_events', 'event_marketing', 'normal', 'default');
    }

}

if (!function_exists('scal_events')) {

    function scal_events($post) {
        $event_start_date = get_post_meta($post->ID, 'scal_event_start', true);
        $event_duration = get_post_meta($post->ID, 'scal_event_duration', true);
        //hours=> duration label
        $duration = array(
            '0.5' => '0.5 Hour',
            '1' => '1 Hour',
            '1.5' => '1.5 Hours',
            '2' => '2 Hours',
            '3' => '3 Hours',
            '4' => '4 Hours',
            '6' => '6 Hours',
            '8' => '8 Hours',
            '12' => '12 Hours',
            '24' => '24 Hours',
            '48' => '2 Days',
            '72' => '3 Days',
            '96' => '4 Days',
            '120' => '5 Days',
            '168' => '1 Week',
            '240' => '10 Days',
            '288' => '12 Days',
            '336' => '2 Weeks',
            '672' => '4 Weeks',
        );

        $scal_event_location = get_post_meta($post->ID, 'scal_event_location', true);
        $scal_event_recurring = get_post_meta($post->ID, 'scal_event_recurring', true);
        $scal_event_repeat = get_post_meta($post->ID, 'scal_event_repeat', true);
        $scal_event_repeat_every = get_post_meta($post->ID, 'scal_event_repeat_every', true);
        $scal_event_repeat_every_week = get_post_meta($post->ID, 'scal_event_repeat_every_week', true);
        $scal_event_repeat_every_week_arr = (isset($scal_event_repeat_every_week) && !empty($scal_event_repeat_every_week)) ? @explode(",", $scal_event_repeat_every_week) : array();
        $scal_event_repeat_end = get_post_meta($post->ID, 'scal_event_repeat_end', true);
        $scal_event_repeat_end_after = get_post_meta($post->ID, 'scal_event_repeat_end_after', true);
        $scal_event_repeat_end_on = get_post_meta($post->ID, 'scal_event_repeat_end_on', true);
        ?>
        <label for="scal_event_recurring">Event Recurring: </label><br />
        <select name="scal_event_recurring" id='scal_event_recurring' class="regular-text">
            <option value="One Time" <?php echo ($scal_event_recurring == "One Time") ? "selected" : ""; ?>>One Time</option>
            <option value="Recurring" <?php echo ($scal_event_recurring == "Recurring") ? "selected" : ""; ?>>Recurring</option>
        </select><br /><br />

        <div class="rucurring_container" style="<?php echo (isset($scal_event_recurring) && !empty($scal_event_recurring) && $scal_event_recurring === 'Recurring') ? 'display: block' : 'display: none'; ?>">
            <label for="scal_event_repeat">Repeats: </label><br />
            <select id="scal_event_repeat" name="scal_event_repeat" class="regular-text">
                <option value="1" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '1') ? 'selected' : ''; ?> title="days">Daily</option>
                <option value="2" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '2') ? 'selected' : ''; ?> title="Every weekday (Monday to Friday)">Every weekday (Monday to Friday)</option>
                <option value="3" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '3') ? 'selected' : ''; ?> title="Every Monday, Wednesday, and Friday">Every Monday, Wednesday, and Friday</option>
                <option value="4" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '4') ? 'selected' : ''; ?> title="Every Tuesday and Thursday">Every Tuesday and Thursday</option>
                <option value="5" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '5') ? 'selected' : ''; ?> title="weeks">Weekly</option>
                <option value="6" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '6') ? 'selected' : ''; ?> title="months">Monthly</option>
                <option value="7" <?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '7') ? 'selected' : ''; ?> title="years">Yearly</option>
            </select><br /><br />

            <div class="scal_event_repeat_every_container">
                <label for="scal_event_repeat_every">Repeat every:</label><br />
                <select id="scal_event_repeat_every" name="scal_event_repeat_every" class="regular-text">
                    <?php for ($ic = 1; $ic <= 30; $ic++): ?>
                        <option value="<?php echo $ic; ?>" <?php echo ($scal_event_repeat_every == $ic) ? 'selected="selected"' : ''; ?>><?php echo $ic; ?></option>
                    <?php endfor; ?>
                </select>
                <label id="repeat_every_label">
                    <?php
                    switch ($scal_event_repeat) {
                        case '4': echo 'weeks';
                            break;
                        case '5': echo 'months';
                            break;
                        case '6': echo 'years';
                            break;
                        default: echo 'days';
                    }
                    ?>
                </label><br /><br />
            </div>

                <!--            <div class="scal_event_repeat_every_week_container" style="<?php echo (isset($scal_event_repeat) && !empty($scal_event_repeat) && $scal_event_repeat === '4') ? 'display: block' : 'display: none'; ?>">
                                <label for="scal_event_repeat_every_week_sun"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_sun" value="Sun" <?php echo (in_array('Sun', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Sun</label>&nbsp;&nbsp;
                                <label for="scal_event_repeat_every_week_mon"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_mon" value="Mon" <?php echo (in_array('Mon', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Mon</label>&nbsp;&nbsp;
                                <label for="scal_event_repeat_every_week_tue"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_tue" value="Tue" <?php echo (in_array('Tue', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Tue</label>&nbsp;&nbsp;
                                <label for="scal_event_repeat_every_week_wed"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_wed" value="Wed" <?php echo (in_array('Wed', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Wed</label>&nbsp;&nbsp;
                                <label for="scal_event_repeat_every_week_thu"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_thu" value="Thu" <?php echo (in_array('Thu', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Thu</label>&nbsp;&nbsp;
                                <label for="scal_event_repeat_every_week_fri"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_fri" value="Fri" <?php echo (in_array('Fri', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Fri</label>&nbsp;&nbsp;
                                <label for="scal_event_repeat_every_week_sat"><input type="checkbox" name="scal_event_repeat_every_week[]" id="scal_event_repeat_every_week_sat" value="Sat" <?php echo (in_array('Sat', $scal_event_repeat_every_week_arr)) ? 'checked="checked"' : ''; ?> /> Sat</label>&nbsp;&nbsp;
                                <br /><br />
                            </div>-->

            <label for="scal_event_repeat_start_on">Ends:</label><br />
            <input type="radio" id="scal_event_repeat_end" name="scal_event_repeat_end" value="Never" <?php echo (isset($scal_event_repeat_end) && !empty($scal_event_repeat_end) && $scal_event_repeat_end === 'Never') ? 'checked="checked"' : ''; ?> /> Never<br />
            <input type="radio" id="scal_event_repeat_end" name="scal_event_repeat_end" value="After" <?php echo (isset($scal_event_repeat_end) && !empty($scal_event_repeat_end) && $scal_event_repeat_end === 'After') ? 'checked="checked"' : ''; ?> /> After <input type="number" name="scal_event_repeat_end_after" value="<?php echo $scal_event_repeat_end_after; ?>" /> occurrences<br />
            <input type="radio" id="scal_event_repeat_end" name="scal_event_repeat_end" value="Specific" <?php echo (isset($scal_event_repeat_end) && !empty($scal_event_repeat_end) && $scal_event_repeat_end === 'Specific') ? 'checked="checked"' : ''; ?> /> On <input type="text" name="scal_event_repeat_end_on" id="scal_event_repeat_end_on" value="<?php echo isset($scal_event_repeat_end_on) && !empty($scal_event_repeat_end_on) ? $scal_event_repeat_end_on : ''; ?>" />
            <br /><br />
        </div>

        <label for="event_start_input">Event Start Date: </label><br />
        <input type="text" name="scal_event_start" id="event_start_input" class="regular-text" value="<?php echo $event_start_date; ?>" /><br /><br />

        <label for="scal_event_duration">Event Duration: </label><br />
        <select name="scal_event_duration" id="scal_event_duration" class="regular-text">
            <option value="">Select Duration</option>
            <?php
            foreach ($duration as $hours_key => $duration_lbl) {
                echo '<option ' . selected($event_duration, $hours_key) . ' value="' . $hours_key . '">' . $duration_lbl . '</option>';
            }
            ?>
        </select><br /><br />

        <label for="scal_event_location">Event Location: </label><br />
        <input type="text" name="scal_event_location" id="scal_event_location" class="regular-text" value="<?php echo $scal_event_location; ?>" /><br /><br />


        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#event_start_input').datetimepicker({
                    minDate: 0,
                    controlType: 'slider',
                    dateFormat: 'mm-dd-yy',
                    timeFormat: "hh:mmTT"
                });

                scal_event_repeat_end_on = jQuery("#scal_event_repeat_end_on").datepicker({
                    dateFormat: 'mm-dd-yy',
                    minDate: 0
                });
            });
        </script>
        <?php
    }

}

/**
 *      Save meta
 */
add_action('save_post', 'scal_save_ratings');

if (!function_exists('scal_save_ratings')) {

    function scal_save_ratings($post_id) {
        if (isset($_POST["scal_event_start"]) && !empty($_POST["scal_event_start"])) {
            $event_start = sanitize_text_field($_POST['scal_event_start']);
//            $date = DateTime::createFromFormat('m-d-Y h:iA', $event_start);
//            $new_event_date = $date->format('Y-m-d H:i:s');
            update_post_meta($post_id, 'scal_event_start', $event_start);
        }
        if (isset($_POST["scal_event_recurring"]) && !empty($_POST["scal_event_recurring"])) {
            $event_recurring = sanitize_text_field($_POST['scal_event_recurring']);
            update_post_meta($post_id, 'scal_event_recurring', $event_recurring);
        }
        if (isset($_POST["scal_event_repeat"]) && !empty($_POST["scal_event_repeat"])) {
            $scal_event_repeat = sanitize_text_field($_POST['scal_event_repeat']);
            update_post_meta($post_id, 'scal_event_repeat', $scal_event_repeat);
        }
        if (isset($_POST["scal_event_repeat_every"]) && !empty($_POST["scal_event_repeat_every"])) {
            $scal_event_repeat_every = sanitize_text_field($_POST['scal_event_repeat_every']);
            update_post_meta($post_id, 'scal_event_repeat_every', $scal_event_repeat_every);
        }
        if (isset($_POST["scal_event_repeat_every_week"]) && !empty($_POST["scal_event_repeat_every_week"])) {
            $scal_event_repeat_every_week = sanitize_text_or_array_field($_POST['scal_event_repeat_every_week']);
            $scal_event_repeat_every_week = @implode(",", $scal_event_repeat_every_week);
            update_post_meta($post_id, 'scal_event_repeat_every_week', $scal_event_repeat_every_week);
        }
        if (isset($_POST["scal_event_repeat_end"]) && !empty($_POST["scal_event_repeat_end"])) {
            $scal_event_repeat_end = sanitize_text_field($_POST['scal_event_repeat_end']);
            update_post_meta($post_id, 'scal_event_repeat_end', $scal_event_repeat_end);

            if ($scal_event_repeat_end == "After") {
                $scal_event_repeat_end_after = sanitize_text_field($_POST['scal_event_repeat_end_after']);
                update_post_meta($post_id, 'scal_event_repeat_end_after', $scal_event_repeat_end_after);
                update_post_meta($post_id, 'scal_event_repeat_end_on', '');
            } else if ($scal_event_repeat_end == "Specific") {
                $scal_event_repeat_end_on = sanitize_text_field($_POST['scal_event_repeat_end_on']);
                update_post_meta($post_id, 'scal_event_repeat_end_on', $scal_event_repeat_end_on);
                update_post_meta($post_id, 'scal_event_repeat_end_after', '');
            }
        }
        if (isset($_POST["scal_event_location"]) && !empty($_POST["scal_event_location"])) {
            $event_location = sanitize_text_field($_POST['scal_event_location']);
            update_post_meta($post_id, 'scal_event_location', $event_location);
        }
        if (isset($_POST["scal_event_duration"]) && !empty($_POST["scal_event_duration"])) {
            $event_duration = sanitize_text_field($_POST['scal_event_duration']);
            update_post_meta($post_id, 'scal_event_duration', $event_duration);
        }
    }

}



/**
 *         Add sidebar
 */
add_action('widgets_init', 'eventCal_widget_init');
if (!function_exists('eventCal_widget_init')) {

    function eventCal_widget_init() {
        register_sidebar(array(
            'name' => __('Event Sidebar', 'swift-calendar'),
            'id' => 'event-cal-sidebar',
            'description' => __('Add widgets here to appear in event calendar sidebar', 'swift-calendar'),
            'before_widget' => '<div class="scal-widget-border pr-m-b-15">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="scal-widget-title">',
            'after_title' => '</h3>',
        ));
    }

}

if (!function_exists('sanitize_text_or_array_field')) {

    function sanitize_text_or_array_field($array_or_string) {
        if (is_string($array_or_string)) {
            $array_or_string = sanitize_text_field($array_or_string);
        } elseif (is_array($array_or_string)) {
            foreach ($array_or_string as $key => &$value) {
                if (is_array($value)) {
                    $value = sanitize_text_or_array_field($value);
                } else {
                    $value = sanitize_text_field($value);
                }
            }
        }

        return $array_or_string;
    }

}