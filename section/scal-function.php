<?php

/*
 *      Custom excerpt function
 */

if (!function_exists('scal_get_excerpt')) {

    function scal_get_excerpt($excerpt_length = 55, $id = false, $echo = false) {
        return scal_excerpt($excerpt_length, $id, $echo);
    }

}

if (!function_exists('scal_excerpt')) {

    function scal_excerpt($excerpt_length = 55, $id = false, $echo = false) {

        $text = '';

        if ($id) {
            $the_post = & get_post($my_id = $id);
            $text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
        } else {
            global $post;
            $text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
        }

        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);

        $excerpt_more = ' ' . '<a href=' . get_permalink($id) . ' class="scal-readmore">...continued</a>';
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        if ($echo)
            echo apply_filters('the_content', $text);
        else
            return $text;
    }

}


// check if recurreing event
add_action('wp', 'slp_update_recurrening_event');

function slp_update_recurrening_event() {
    $event_start_date = get_post_meta(get_the_ID(), 'scal_event_start', true);
    $event_start_date = !empty($event_start_date) ? $event_start_date : date('m-d-Y H:i:s');
    $event_duration = get_post_meta(get_the_ID(), 'scal_event_duration', true);

    $scal_event_recurring = get_post_meta(get_the_ID(), 'scal_event_recurring', true);
    if (isset($scal_event_recurring) && !empty($scal_event_recurring) && $scal_event_recurring === 'Recurring') {
        $scal_event_repeat = get_post_meta(get_the_ID(), 'scal_event_repeat', true);
        $scal_event_repeat_every = get_post_meta(get_the_ID(), 'scal_event_repeat_every', true);
        $scal_event_repeat_end = get_post_meta(get_the_ID(), 'scal_event_repeat_end', true);
        $scal_event_repeat_end_after = get_post_meta(get_the_ID(), 'scal_event_repeat_end_after', true);
        $scal_event_repeat_end_on = get_post_meta(get_the_ID(), 'scal_event_repeat_end_on', true);

        $str_date = "";
        $event_start_date_tmp = @explode(" ", $event_start_date);
        if (isset($event_start_date_tmp[0]) && !empty($event_start_date_tmp[0]) && isset($event_start_date_tmp[1]) && !empty($event_start_date_tmp[1])) {
            $eve_st_dt_tmp = @explode("-", $event_start_date_tmp[0]);
            if (!empty($eve_st_dt_tmp)) {
                $str_date = (string) $eve_st_dt_tmp[2] . "-" . $eve_st_dt_tmp[0] . "-" . $eve_st_dt_tmp[1] . " " . $event_start_date_tmp[1];
            }
        }
        $event_date = !empty($str_date) ? strtotime($str_date) : strtotime(date('Y-m-d'));
        $today_date = strtotime(date("Y-m-d"));

        switch ($scal_event_repeat) {
            case '1':   // daily
                if ($event_date < $today_date) {
                    $scal_event_new_date = date('m-d-Y h:iA', strtotime('+' . ($scal_event_repeat_every * 1) . ' day', $event_date));
                    update_post_meta(get_the_ID(), 'scal_event_start', $scal_event_new_date);
                }
                break;
            case '5':   // weekly
                if ($event_date < $today_date) {
                    $scal_event_new_date = date('m-d-Y h:iA', strtotime('+' . ($scal_event_repeat_every * 7) . ' day', $event_date));
                    update_post_meta(get_the_ID(), 'scal_event_start', $scal_event_new_date);
                }
                break;
            case '6':   // monthly
                if ($event_date < $today_date) {
                    $scal_event_new_date = date('m-d-Y h:iA', strtotime('+' . ($scal_event_repeat_every) . ' month', $event_date));
                    update_post_meta(get_the_ID(), 'scal_event_start', $scal_event_new_date);
                }
                break;
            case '7':   // yearly
                if ($event_date < $today_date) {
                    $scal_event_new_date = date('m-d-Y h:iA', strtotime('+' . ($scal_event_repeat_every) . ' year', $event_date));
                    update_post_meta(get_the_ID(), 'scal_event_start', $scal_event_new_date);
                }
                break;
        }
    }
}