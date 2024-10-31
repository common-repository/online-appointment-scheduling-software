<?php

/**
 *  Create widget to showing upcoming events.
 */
class scal_widget extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'scal-widget',
            'description' => "showing the x number of upcoming events",
        );
        parent::__construct('scal_upcoming_events', 'Upcoming Events', $widget_ops);
    }

    //display form
    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox"<?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Display post date?'); ?></label></p>
        <?php
    }

    // update form
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        return $instance;
    }

    //output
    public function widget($args, $instance) {
        global $wpdb;
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        $title = (!empty($instance['title']) ) ? $instance['title'] : __('Upcoming Events');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
        if (!$number)
            $number = 5;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;

        echo $args['before_widget'];
        $scal_qry = '   SELECT SQL_CALC_FOUND_ROWS p.* FROM ' . $wpdb->prefix . 'posts as p
                        LEFT JOIN ' . $wpdb->prefix . 'postmeta AS mt ON ( p.ID = mt.post_id )
                        WHERE 1=1
                        AND p.post_type = "event_marketing"
                        AND p.post_status = "publish"
                        AND mt.meta_key = "scal_event_start"
                        AND STR_TO_DATE(mt.meta_value, "%m-%d-%Y") >= STR_TO_DATE("' . date("m-d-Y") . '", "%m-%d-%Y")
                        GROUP BY p.ID
                        ORDER BY STR_TO_DATE(mt.meta_value,"%m-%d-%Y") ASC
                        LIMIT ' . $number;
        $scal_posts = $wpdb->get_results($scal_qry);

        echo $args['before_title'] . $title . $args['after_title'];
        echo '<div>';
        if ($scal_posts) {
            echo '<ul class="scal-upcoming-events">';

            foreach ($scal_posts as $post) {
                setup_postdata($post);
                ?>
                <li>
                    <a href="<?php echo get_permalink($post->ID); ?>">
                        <?php echo get_the_title($post->ID); ?>
                        <?php
                        if ($show_date) :
                            $start_date = get_post_meta($post->ID, 'scal_event_start', true);
                            $event_date = date_parse_from_format("m-d-Y", $start_date);
                            $event_date = date('D, M jS, Y', mktime(0, 0, 0, $event_date['month'], 10));

                            if (isset($event_date) && !empty($event_date)) {
                                echo "<span class='sidebar-event-date'><i class='fa fa-calendar'></i> " . $event_date . '</span>';
                            }
                        endif;
                        ?>
                    </a>
                </li>
                <?php
            }
            echo '</ul>';
        } else {
            echo '<h3>No events found.....yet</h3>';
        }
        echo '</div>';

        echo $args['after_widget'];
    }

}

add_action('widgets_init', function() {
    register_widget('scal_widget');
});
?>
