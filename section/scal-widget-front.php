<?php
/**
 *      Swift Calendar Widget
 *
 *      calendar_widget frontend
 */
add_action('wp_footer', 'swift_calendar_widget', 10);

if (!function_exists('swift_calendar_widget')) {

    function swift_calendar_widget() {
        wp_enqueue_script('swift-widget-position', plugins_url('../js/swift_widget_position.js', __FILE__), array('jquery'), '', true);

        $swift_settings = get_option('swift_cal_settings');

        //Return if not off
        if (!isset($swift_settings['enable_calendar_widget']) || $swift_settings['enable_calendar_widget']==0)
            return;

        $cw_widget_position = '';
        $swift_global_position_class = swift_calendar_global_position_class($swift_settings['cw_widget_position']);

        $widget_header_color = !empty($swift_settings['cw_dark_color']) ? $swift_settings['cw_dark_color'] : '#FF7200';
        $button_color = !empty($swift_settings['cw_light_color']) ? $swift_settings['cw_light_color'] : '#FF7200';
        $text_color = $swift_settings['cw_text_color'];
        ?>
        <div class="cw-calendar-widget swiftcloud_widget <?php echo $swift_global_position_class; ?>" id="cwCalendarWidget" style="<?php //echo $cw_widget_position;   ?>">
            <div class="cw-main-title" style="background-color: <?php echo $widget_header_color; ?>">
                <h2><i class="fa fa-calendar"></i> <?php echo $swift_settings['cw_main_title']; ?></h2>
                <span class="cw-widget-toggle">+</span>
            </div>
            <div class="cw-calendar-widget-contet" style="display: none;color:<?php echo $text_color; ?>">
                <div class="cw-schedule-online">
                    <div class="cw-schedule-online-title"><p><i class="fa fa-calendar"></i> Schedule Online</p></div>
                    <?php $data_modal = !empty($swift_settings['cw_swiftcloud_user_id']) ? 'data-toggle="modal" data-target="#cw-book-now-modal" data-url="' . $swift_settings['cw_swiftcloud_user_id'] . '"' : ""; ?>
                    <button name="cw_book_ur_session_now" id="cw_book_now_btn" <?php echo $data_modal; ?>  style="background-color: <?php echo $button_color; ?>"><i class="fa fa-calendar"></i> <?php echo $swift_settings['cw_schedulet_btn_txt']; ?></button>
                </div>
                <?php if ($swift_settings['enable_call_us_section']) { ?>
                    <div class="cw-or">
                        <div class="cw-or-line">
                            <div class="cw-or-text"><p>or</p></div>
                        </div>
                    </div>
                    <div class="cw-call-us">
                        <div class="cw-call-us-title">
                            <?php echo stripslashes(nl2br($swift_settings['cw_call_us_section_text_content'])); ?>
                        </div>
                    </div>

                <?php } ?>
                <?php if ($swift_settings['enable_email_form_option']) { ?>
                    <?php wp_enqueue_script('swift-form-jstz', SWIFTCAL__PLUGIN_URL . "js/jstz.min.js", '', '', true); ?>
                    <div class="cw-or">
                        <div class="cw-or-line">
                            <div class="cw-or-text"><p>or</p></div>
                        </div>
                    </div>
                    <div class="cw-email-us">
                        <div class="cw-email-us-title"><p><i class="fa fa-envelope"></i> Email Us</p></div>
                        <div class="cw-email-form">
                            <form method="post" id="FrmWC" action="https://swiftcloud.ai/is/drive/formHandlingProcess001">
                                <textarea name="cw_textarea" placeholder="How can we help?"></textarea>
                                <input type="text" name="phone"  placeholder="Phone"/>
                                <input type="email" id="email" name="email" required="required" placeholder="Email Address"/>
                                <input type="hidden" name="ip_address" id="ip_address" value="<?php echo $_SERVER['SERVER_ADDR'] ?>">
                                <input type="hidden" name="browser" id="SC_browser" value="<?php echo $_SERVER['HTTP_USER_AGENT'] ?>">
                                <input type="hidden" name="trackingvars" class="trackingvars" id="trackingvars" >
                                <input type="hidden" name="timezone" id="SC_fh_timezone" value="">
                                <input type="hidden" name="language" id="SC_fh_language" >
                                <input type="hidden" name="capturepage" id="SC_fh_capturepage" value="" >
                                <input type="hidden" name="formid" value="<?php echo $swift_settings['cw_form_id']; ?>" id="formid" >
                                <input type="hidden" name="vTags" id="vTags" value="#swiftcalendar">
                                <input type="hidden" name="vThanksRedirect" value="<?php echo $swift_settings['cw_thank_you_url']; ?>">
                                <input type="hidden" name="sc_lead_referer" id="sc_lead_referer" value="" />
                                <input type="hidden" name="sc_referer_qstring" id="sc_referer_qstring" value=""/>
                                <input type="hidden" name="iSubscriber" value="817">
                                <!--<button type="submit" name="cw_send_btn" id="cw_send_btn" class="cw_send_btn" style="background-color: <?php echo $button_color; ?>"></button>-->

                                <div id="scalBtnContainer" style="display: inline-flex"></div>
                                <script type="text/javascript">
                                    var button = document.createElement("button");
                                    button.innerHTML = '<i class="fa fa-paper-plane"></i> <?php echo (!empty($swift_settings['cw_send_btn_txt']) ? $swift_settings['cw_send_btn_txt'] : 'SEND'); ?>';
                                    var body = document.getElementById("scalBtnContainer");
                                    body.appendChild(button);
                                    button.id = "cw_send_btn";
                                    button.name = "cw_send_btn";
                                    button.className = "cw_send_btn";
                                    button.value = 'send';
                                    button.type = 'submit';
                                    button.style = 'background-color: <?php echo $button_color; ?>';
                                </script>
                                <noscript>
                                <p style='color:red;font-size:18px;'>JavaScript must be enabled to submit this form. Please check your browser settings and reload this page to continue.</p>
                                </noscript>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="modal fade cw-popup " id="cw-book-now-modal" tabindex="-1" role="dialog" aria-labelledby="book-now-modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="close-modal"><img data-dismiss="modal" src="<?php echo plugins_url('../images/close.png', __FILE__) ?>" alt="close"/></div>
                        <div class="cw-modal-content">Message text</div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}


/* set global position class in swift corner widgets
 *  return : position class name
 */
if (!function_exists('swift_calendar_global_position_class')) {

    function swift_calendar_global_position_class($position) {
        switch ($position) {
            case 'left': {
                    return 'swift_left_bottom';
                    break;
                }
            case 'right': {
                    return 'swift_right_bottom';
                    break;
                }
            case 'center': {
                    return 'swift_center_bottom';
                    break;
                }

            case 'right_center': {
                    return 'swift_right_center';
                    break;
                }
            case 'left_center': {
                    return 'swift_left_center';
                    break;
                }

            case 'left_top': {
                    return 'swift_left_top';
                    break;
                }
            case 'right_top': {
                    return 'swift_right_top';
                    break;
                }
            case 'center_top': {
                    return 'swift_center_top';
                    break;
                }
        }
    }

}
?>