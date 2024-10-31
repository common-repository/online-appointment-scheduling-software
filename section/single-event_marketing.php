<?php
/**
 * The template for displaying all single press release posts
 *
 */
get_header();
wp_enqueue_script('scal-countdown', SWIFTCAL__PLUGIN_URL . 'js/countdown.js', array('jquery'), '', true);
wp_enqueue_script('swift-form-jstz', SWIFTCAL__PLUGIN_URL . "js/jstz.min.js", '', '', true);
wp_enqueue_script('swift-moment', SWIFTCAL__PLUGIN_URL . 'js/moment.js', '', '', true);
wp_enqueue_script('swift-moment-tz', SWIFTCAL__PLUGIN_URL . 'js/moment-timezone.js', '', '', true);
wp_enqueue_style('sc-bootstrap', SWIFTCAL__PLUGIN_URL . 'css/bootstrap-grid.min.css');

$event_feed_page_id = get_option('event_marketing_feed_page_id');
$url = '';
if (has_post_thumbnail(get_the_ID())) {
    $url = (get_the_post_thumbnail_url(get_the_ID(), 'full'));
}
?>
<div class="container bootstrap-wrapper">
    <div class="scal-page-wrap row">
        <div class="col-lg-8">
            <?php while (have_posts()) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class('event-main-content'); ?>>
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="event-title"><a href="javascript:;" class="add-to-calendar tooltip-bottom" data-tooltip="Add to Calendar"><?php the_title(); ?></a></h1>
                        </div>
                    </div>

                    <?php if (has_post_thumbnail(get_the_ID())) { ?>
                        <div class="row">
                            <div class="col-lg-12 event-detail-img">
                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php get_the_title(); ?>" class="img-fluid" />
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <?php
                            date_default_timezone_set(get_option('timezone_string'));

                            $event_duration = get_post_meta(get_the_ID(), 'scal_event_duration', true);
                            $event_location = get_post_meta(get_the_ID(), 'scal_event_location', true);
                            $event_start_date = get_post_meta(get_the_ID(), 'scal_event_start', true);
                            $cal_event_date = date_parse_from_format("m-d-Y", $event_start_date);
                            $month_name = date('M', mktime(0, 0, 0, $cal_event_date['month'], 10));
                            $event_start_date = !empty($event_start_date) ? $event_start_date : date('m-d-Y H:i:s');


                            $event_start_date_tmp = @explode(" ", $event_start_date);
                            if (isset($event_start_date_tmp[0]) && !empty($event_start_date_tmp[0]) && isset($event_start_date_tmp[1]) && !empty($event_start_date_tmp[1])) {
                                $eve_st_dt_tmp = @explode("-", $event_start_date_tmp[0]);
                                if (!empty($eve_st_dt_tmp)) {
                                    $str_date = (string) $eve_st_dt_tmp[2] . "-" . $eve_st_dt_tmp[0] . "-" . $eve_st_dt_tmp[1] . " " . $event_start_date_tmp[1];
                                } else {
                                    $str_date = "";
                                }
                            } else {
                                $str_date = "";
                            }
                            $event_date = !empty($str_date) ? strtotime($str_date) : strtotime(date('Y-m-d'));
                            $today_date = strtotime(date("Y-m-d"));
                            $end_date = date('Y-m-d H:i', strtotime('+' . $event_duration . ' hour', $event_date));

//                            if ($event_date > $today_date) {
//                                echo '<a href="javascript:;" class="add-to-calendar tooltip-bottom" data-tooltip="Add to Calendar"><i class="fa fa-calendar"></i> <span class="event_st_dt"></span> <i class="fa fa-calendar-plus-o"></i></a>';
//                            } else {
//                                echo '<i class="fa fa-calendar"></i> <span class="event_st_dt"></span>';
//                            }
                            ?>
                        </div>
                        <div class="col-lg-6 col-sm-12 text-right">
                            <?php if ($event_date > $today_date) { ?>
                                <a href="javascript:;" class="add-to-calendar tooltip-bottom" data-tooltip="Add to Calendar"><span id="future_date"></span></a>
                            <?php } ?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="scal-event-date-wrap">
                                <div class="scal-event-date"><?php echo $cal_event_date['day']; ?></div>
                                <div class="scal-event-month"><?php echo $month_name; ?></div>
                                <div class="scal-event-year"><?php echo $cal_event_date['year']; ?></div>
                            </div>
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php edit_post_link('Edit', '<p class="pr-edit-post tooltip-right" data-tooltip="Only you can see this because you\'re logged in.">', '</p>'); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="col-lg-4 scal-sidebar-bg">
            <?php if ($event_date > $today_date) { ?>
                <div class="row">
                    <div class="col-lg-12 scal-add-to-calendar">
                        <h4><i class="fa fa-calendar-plus-o"></i> Add To Calendar</h4>
                        <script type="text/javascript">
                            (function () {
                                if (window.addtocalendar)
                                    if (typeof window.addtocalendar.start == "function")
                                        return;
                                if (window.ifaddtocalendar == undefined) {
                                    window.ifaddtocalendar = 1;
                                    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                                    s.type = 'text/javascript';
                                    s.charset = 'UTF-8';
                                    s.async = true;
                                    s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://addtocalendar.com/atc/1.5/atc.min.js';
                                    var h = d[g]('body')[0];
                                    h.appendChild(s);
                                }
                            })();
                        </script>
                        <span class="addtocalendar" data-calendars="iCalendar, Google Calendar, Outlook">
                            <var class="atc_event">
                                <var class="atc_date_start"><?php echo (!empty($str_date)) ? gmdate('Y-m-d', strtotime($str_date)) : date('Y-m-d'); ?></var>
                                <var class="atc_date_end"><?php echo (!empty($event_duration)) ? gmdate('Y-m-d', strtotime($end_date)) : date('Y-m-d'); ?></var>
                                <var class="atc_title"><?php the_title(); ?></var>
                                <var class="atc_timezone"></var>
                                <var class="atc_location"><?php echo $event_location; ?></var>
                            </var>
                        </span>
                        <ul class="calender-imgs">
                            <li><a href="#" id="cal_gcal" target="_blank"><img src="<?php echo plugins_url('../images/gcal.png', __FILE__); ?>" alt="Google Calendar"><span><i class="fa fa-google"></i> Google Calendar</span></a></li>
                            <li><a href="#" id="cal_ical" target="_blank"><img src="<?php echo plugins_url('../images/ical.png', __FILE__); ?>" alt="iCal"><span><i class="fa fa-apple"></i> iCal</span></a></li>
                            <li><a href="#" id="cal_outlook" target="_blank"><img src="<?php echo plugins_url('../images/oulook.png', __FILE__); ?>" alt="Outlook"><span><i class="fa fa-windows"></i> Outlook</span></a></li>
                        </ul>
                        <hr class="scal_hr" />
                    </div>
                </div>
            <?php } ?>
            <div class="scal-sidebar">
                <div class="scal-widget">
                    <div class="scal-widget">
                        <div class="pr-print scal-pagination">
                            <div class="scal-sidebar-link tooltip-left" data-tooltip="Send this release to someone"><a href="mailto:?Subject=<?php echo get_the_title(); ?>&Body=<?php echo get_the_permalink(); ?>" target="_new"><i class="fa fa-paper-plane"></i></a></div>
                            <div class="scal-sidebar-link tooltip-left" data-tooltip="RSS Feed"><a href="<?php echo get_permalink($event_feed_page_id); ?>" target="_blank"><i class="fa fa-rss fa-lg"></i></a></div>
                        </div>
                    </div>
                    <?php if (is_active_sidebar('event-cal-sidebar')) : ?>
                        <div class="scal-sidebar-widget">
                            <?php dynamic_sidebar('event-cal-sidebar'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$event_start_date = @explode(" ", $event_start_date);
if (isset($event_start_date[0]) && !empty($event_start_date[0]) && isset($event_start_date[1]) && !empty($event_start_date[1])) {
    $eve_st_dt_tmp = @explode("-", $event_start_date[0]);
    if (!empty($eve_st_dt_tmp)) {
        $eve_time = date("H:i", strtotime($event_start_date[1]));
        $eve_st_dt = $eve_st_dt_tmp[2] . "-" . $eve_st_dt_tmp[0] . "-" . $eve_st_dt_tmp[1] . " " . $eve_time;
    } else {
        $eve_st_dt = date('Y-m-d') . " " . date("H:i");
    }
} else {
    $eve_st_dt = date('Y-m-d') . " " . date("H:i");
}
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        // Get the timezone
        // If it's already in storage, just grab from there
        if (!sessionStorage.getItem('timezone')) {
            var tz = jstz.determine() || 'UTC';
            sessionStorage.setItem('timezone', tz.name());
        }
        var currTz = sessionStorage.getItem('timezone');

        // Create a Moment.js object
        var stamp = "<?php echo $eve_st_dt; ?>";
        var momentTime = moment.tz(stamp, '<?php echo get_option('timezone_string'); ?>');
        // Adjust using Moment Timezone
        var tzTime = momentTime.tz(currTz).format('MMMM D, YYYY h:mm A');
        jQuery(".event_st_dt").html(tzTime);


        // event clock for frontend
        jQuery('.addtocalendar').find('.atc_timezone').html(jstz.determine().name());

        var nav = jQuery("body").find("nav").css("position");
        var header = jQuery("body").find("header").css("position");
        var padding = '';

        if (header === 'fixed' || header === 'absolute') {
            padding = jQuery("body").find("header").height();
        } else if (nav === 'fixed') {
            padding = jQuery("body").find("nav").height();
        }
        if (padding !== '') {
            jQuery(".scal-page-wrap").css('padding-top', padding);
        }

<?php
if (isset($end_date) && !empty($end_date)) {

//        $event_start_date_countdown = gmdate('Y/m/d g:i:s', strtotime($end_date));
    $event_start_date_countdown = str_replace('-', "/", $end_date);
    ?>
            jQuery("#future_date").countdown('<?php echo $event_start_date_countdown; ?>', function (event) {
                jQuery(this).html("<i class='fa fa-clock-o'></i> " + event.strftime('%-D days, %-Hh, %-Mm'));
            });

<?php } ?>

        jQuery(".add-to-calendar").on('click', function () {
            jQuery('.addtocalendar').find('.atc_timezone').html(jstz.determine().name());
            jQuery('html, body').animate({
                scrollTop: jQuery(".scal-add-to-calendar").offset().top
            }, 1000);
        });

        setTimeout(function () {
            jQuery('#cal_outlook').attr('href', jQuery('.atcb-list li').eq(2).find('a').attr('href'));
            jQuery('#cal_gcal').attr('href', jQuery('.atcb-list li').eq(1).find('a').attr('href'));
            jQuery('#cal_ical').attr('href', jQuery('.atcb-list li').eq(0).find('a').attr('href'));
        }, 5000);
    });
</script>
<?php get_footer(); ?>