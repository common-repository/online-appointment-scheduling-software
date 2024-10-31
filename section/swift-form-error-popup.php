<?php
/**
 *      SwiftForm error popup.
 *      -   Showing SwiftForm error messages.
 */
if (isset($_GET['swift_err']) && isset($_GET['swift_err_msg']) && !empty($_GET['swift_err']) && !empty($_GET['swift_err_msg']) && $_GET['swift_err'] == 1) {
    add_action('wp_enqueue_scripts', 'swift_form_error_popup_script');
}

if (!function_exists('swift_form_error_popup_script')) {

    function swift_form_error_popup_script() {
        wp_enqueue_script('sf-error-popup', plugins_url('../js/sf-error-popup.js', __FILE__), array('jquery'), '', true);
    }

}

add_action("wp_footer", "swift_form_error_popup_cb");

if (!function_exists('swift_form_error_popup_cb')) {

    function swift_form_error_popup_cb() {
        ?>
        <style type="text/css">
            .sf-err-modal{
                position: fixed;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                top: 0;
            }
            .sf-err-modal-container{
                margin: 0 auto;
                width: 600px;
                position: relative;
                padding: 20px;
                top: 30%;
                background-color: #fff;
                border-radius: 2px;
                box-shadow: 0 0 20px 0px rgba(0, 0, 0, 0.5);
            }
            .sf-err-modal-close{
                color: #000;
                cursor: pointer;
                float: right;
                font-size: 21px;
                font-weight: 700;
                line-height: 1;
                opacity: 0.2;
                position: relative;
                right: -20px;
                text-shadow: 0 1px 0 #fff;
            }
            .sf-err-modal-alert{
                background-color: #fcf8e3;
                border: 1px solid #faebcc;
                border-radius: 4px;
                line-height: normal;
                padding: 15px 35px 15px 40px;
                position: relative;
            }
            .sf-err-modal-alert p {
                margin: 0;
            }
            .sf-err-modal-alert span {
                display: inline-block;
                left: 10px;
                padding: 0;
                position: absolute;
                top: 13px;
                vertical-align: bottom;
            }
            .sf-err-modal-alert img {
                width: 20px;
            }

        </style>
        <div class="sf-err-modal" id="SwiftFormErModal" style="display: none;">
            <div class="sf-err-modal-container">

                <div class="sf-err-modal-content">
                    <div class="sf-err-modal-alert">
                        <div class="sf-err-modal-close">&times;</div>
                        <span><img src="<?php echo plugins_url('../images/sf-alert.png', __FILE__); ?>" alt="alert" /></span>
        <!--                        <p><b>Error</b> message goes here.</p>-->
                        <p><?php echo $_GET['swift_err_msg']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
?>