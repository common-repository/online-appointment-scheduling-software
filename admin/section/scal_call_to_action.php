<?php
/*
 *      SwiftCalendar Call to action
 */

if (!function_exists('scal_call_to_action_callback')) {

    function scal_call_to_action_callback() {
        wp_enqueue_style('swiftcloud-colorpicker-style', plugins_url('../css/scal_spectrum.css', __FILE__), '', '', '');
        wp_enqueue_script('swiftcloud-colorpicker', plugins_url('../js/scal_spectrum.js', __FILE__), array('jquery'), '', true);

        $scal_cta_settings = get_option('scal_cta_settings');
        if (isset($_POST['scal_save_cta_box']) && wp_verify_nonce($_POST['scal_save_cta_box'], 'scal_save_cta_box')) {
            $scal_cta_settings['scal_cta_flag'] = $_POST['scal_cta_settings']['scal_cta_flag'];
            $scal_cta_settings['scal_cta_show_on'] = $_POST['scal_cta_settings']['scal_cta_show_on'];
            $scal_cta_settings['scal_cta_dont_show_on'] = $_POST['scal_cta_settings']['scal_cta_dont_show_on'];
            $scal_cta_settings['scal_cta_form_id'] = $_POST['scal_cta_settings']['scal_cta_form_id'];
            $scal_cta_settings['scal_cta_form_btn_text'] = $_POST['scal_cta_settings']['scal_cta_form_btn_text'];
            $scal_cta_settings['scal_cta_contents_flag'] = $_POST['scal_cta_settings']['scal_cta_contents_flag'];
            $scal_cta_settings['scal_cta_local_html_content'] = $_POST['scal_cta_settings']['scal_cta_local_html_content'];
            $scal_cta_settings['scal_cta_html_bg_color'] = $_POST['scal_cta_settings']['scal_cta_html_bg_color'];
            $scal_cta_settings['scal_cta_html_font_color'] = $_POST['scal_cta_settings']['scal_cta_html_font_color'];
            $scal_cta_settings['scal_cta_html_css'] = $_POST['scal_cta_settings']['scal_cta_html_css'];

            $update = update_option('scal_cta_settings', $scal_cta_settings);
        }
        ?>
        <div class="wrap">
            <div class="inner_content">
                <h2>Call To Action Post-Content Offer Box</h2><hr/>
                <?php
                if (isset($update) && !empty($update)) {
                    echo '<div id="message" class="updated below-h2"><p>Settings updated successfully!</p></div>';
                }
                ?>
                <form name="frm_cta_box" id="frm_cta_box" method="post">
                    <table class="form-table">
                        <tr>
                            <th><label for="scal_cta_flag">Call to Action Post-Content Offer</label></th>
                            <td>
                                <?php $ctaOnOff = ($scal_cta_settings['scal_cta_flag'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="scal_cta_settings[scal_cta_flag]" id="scal_cta_flag" class="scal_cta_flag" <?php echo $ctaOnOff; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th>Show on</th>
                            <td>
                                <?php
                                if (!empty($scal_cta_settings['scal_cta_show_on'])) {
                                    $checkedPage = (in_array('pages', $scal_cta_settings['scal_cta_show_on'])) ? 'checked="checked"' : '';
                                    $checkedPost = (in_array('posts', $scal_cta_settings['scal_cta_show_on']) ? 'checked="checked"' : '');
                                } else {
                                    $checkedPage = '';
                                    $checkedPost = '';
                                }
                                ?>
                                <label for="show_on1"><input type="checkbox" id="show_on1" name="scal_cta_settings[scal_cta_show_on][]" value="pages" <?php echo $checkedPage; ?>/>Pages</label>&nbsp;&nbsp;
                                <label for="show_on2"><input type="checkbox" id="show_on2" name="scal_cta_settings[scal_cta_show_on][]" value="posts" <?php echo $checkedPost; ?>/>Posts</label>&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <th>Don't show on </th>
                            <td>
                                <?php
                                if (!empty($scal_cta_settings['scal_cta_dont_show_on'])) {
                                    $checkedHome = (in_array('home', $scal_cta_settings['scal_cta_dont_show_on'])) ? 'checked="checked"' : '';
                                    $checkedBlog = (in_array('blog', $scal_cta_settings['scal_cta_dont_show_on']) ? 'checked="checked"' : '');
                                    $checked404 = (in_array('404', $scal_cta_settings['scal_cta_dont_show_on']) ? 'checked="checked"' : '');
                                    $checkedCpt = (in_array('cpt', $scal_cta_settings['scal_cta_dont_show_on']) ? 'checked="checked"' : '');
                                } else {
                                    $checkedHome = '';
                                    $checkedBlog = '';
                                    $checked404 = '';
                                    $checkedCpt = '';
                                }
                                ?>
                                <label for="cta_dont_show_on1"><input type="checkbox" id="cta_dont_show_on1" name="scal_cta_settings[scal_cta_dont_show_on][]" value="home" <?php echo $checkedHome; ?>/>Home Page</label>&nbsp;&nbsp;
                                <label for="cta_dont_show_on2"><input type="checkbox" id="cta_dont_show_on2" name="scal_cta_settings[scal_cta_dont_show_on][]" value="blog" <?php echo $checkedBlog; ?>/>Blog Page</label>&nbsp;&nbsp;
                                <label for="cta_dont_show_on3"><input type="checkbox" id="cta_dont_show_on3" name="scal_cta_settings[scal_cta_dont_show_on][]" value="404"  <?php echo $checked404; ?>/>404 Page</label>&nbsp;&nbsp;
                                <label for="cta_dont_show_on4"><input type="checkbox" id="cta_dont_show_on4" name="scal_cta_settings[scal_cta_dont_show_on][]" value="cpt"  <?php echo $checkedCpt; ?>/>Custom Post Type</label>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="scal_cta_contents_flag">Contents</label></th>
                            <td>
                                <?php $cta_contentOnOff = ($scal_cta_settings['scal_cta_contents_flag'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="Local HTML" data-offtext="Form ID" name="scal_cta_settings[scal_cta_contents_flag]" id="scal_cta_contents_flag" class="scal_cta_contents_flag" <?php echo $cta_contentOnOff; ?>>
                            </td>
                        </tr>
                        <tr class="show-sc-form" style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "") ? 'visibility: visible;' : 'display:none'); ?>">
                            <th><label for="scal_cta_form_id">Form ID number</label></th>
                            <td><input type="text" id="scal_cta_form_id" value="<?php echo $scal_cta_settings['scal_cta_form_id'] ?>" class="" name="scal_cta_settings[scal_cta_form_id]"/></td>
                        </tr>
                        <tr class="show-sc-form" style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "") ? 'visibility: visible;' : 'display:none'); ?>">
                            <th><label for="scal_cta_form_btn_text">Form Button Text</label></th>
                            <td><input type="text" id="scal_cta_form_btn_text" value="<?php echo $scal_cta_settings['scal_cta_form_btn_text'] ?>" class="" name="scal_cta_settings[scal_cta_form_btn_text]"/></td>
                        </tr>

                        <tr class="show-local-html"  style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "1") ? 'visibility: visible;' : 'display:none'); ?>">
                            <th><label for="cta_local_html">Local HTML</label></th>
                            <td>
                                <input style="display:none;" type="radio" class="" name="cta_local_html" value="html_content" />
                                <?php
                                $settings = array('editor_height' => 250, 'textarea_rows' => 12, 'media_buttons' => true, 'quicktags' => true, 'textarea_name' => 'scal_cta_settings[scal_cta_local_html_content]',);
                                wp_editor(stripslashes($scal_cta_settings['scal_cta_local_html_content']), 'scal_cta_local_html_id', $settings);
                                ?>
                            </td>
                        </tr>
                        <tr class="show-local-html"  style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "1") ? 'visibility: visible;' : 'display:none'); ?>">
                            <th><label for="scal_cta_html_bg_color">HTML Background Color</label></th>
                            <td><input type="text" id="scal_cta_html_bg_color" value="<?php echo $scal_cta_settings['scal_cta_html_bg_color'] ?>" class="" name="scal_cta_settings[scal_cta_html_bg_color]" placeholder="#FFFFFF"/></td>
                        </tr>
                        <tr class="show-local-html"  style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "1") ? 'visibility: visible;' : 'display:none'); ?>">
                            <th><label for="scal_cta_html_font_color">HTML Font Color</label></th>
                            <td><input type="text" id="scal_cta_html_font_color" value="<?php echo $scal_cta_settings['scal_cta_html_font_color'] ?>" class="" name="scal_cta_settings[scal_cta_html_font_color]" placeholder="#000"/></td>
                        </tr>
                        <tr class="show-local-html"  style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "1") ? 'visibility: visible;' : 'display:none'); ?>">
                            <th><label for="scal_cta_html_css">HTML Custom CSS</label></th>
                            <td>
                                <textarea id="scal_cta_html_css" class="" name="scal_cta_settings[scal_cta_html_css]" rows="6" cols="50"><?php echo $scal_cta_settings['scal_cta_html_css'] ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php wp_nonce_field('scal_save_cta_box', 'scal_save_cta_box'); ?>
                                <input type="submit" class="button button-primary" value="Save Changes" />
                                <input type="button" class="button button-primary show-local-html" value="Preview" id="cta_preview_popup" style="<?php echo (($scal_cta_settings['scal_cta_contents_flag'] == "1") ? 'visibility: visible;' : 'display:none'); ?>" />
                            </th>
                        </tr>
                    </table>
                </form>
            </div>

            <?php
            /*
             *      Preview section
             */
            $prv_bg_color = !empty($scal_cta_settings['scal_cta_html_bg_color']) ? $scal_cta_settings['scal_cta_html_bg_color'] : '#fff';
            $prv_text_color = !empty($scal_cta_settings['scal_cta_html_font_color']) ? $scal_cta_settings['scal_cta_html_font_color'] : '#000';
            $prv_custom_css = !empty($scal_cta_settings['scal_cta_html_css']) ? $scal_cta_settings['scal_cta_html_css'] : '';
            ?>
            <div id="scal_cta_prv_section" style="display:none;background:<?php echo $prv_bg_color; ?>;color:<?php echo $prv_text_color; ?>;<?php echo $prv_custom_css; ?>">
                <div class="cta_prv_inner">
                    <?php echo stripslashes($scal_cta_settings['scal_cta_local_html_content']); ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('.scal_cta_flag').rcSwitcher();
                    jQuery('.scal_cta_contents_flag:checkbox').rcSwitcher({width: 110}).on({
                        'turnon.rcSwitcher': function(e, dataObj) {
                            // to do on turning on a switch
                            jQuery(".show-local-html").show();
                            jQuery(".show-sc-form").hide();
                        },
                        'turnoff.rcSwitcher': function(e, dataObj) {
                            // to do on turning off a switch
                            jQuery(".show-sc-form").fadeIn();
                            jQuery(".show-local-html").fadeOut();
                            jQuery("#scal_cta_prv_section").fadeOut();
                        }
                    });

                    jQuery("#wp-scal_cta_local_html_id-wrap #scal_cta_local_html_id").css("background", jQuery("#scal_cta_html_bg_color").val());
                    jQuery("#wp-scal_cta_local_html_id-wrap #scal_cta_local_html_id").css("color", jQuery("#scal_cta_html_font_color").val());

                    //form validation
                    jQuery(".ctaError").remove();
                    jQuery("#frm_cta_box").submit(function(e) {
                        jQuery(".ctaError").remove();
                        if (jQuery('.scal_cta_flag:checkbox').is(':checked')) {
                            if (!jQuery('.scal_cta_contents_flag:checkbox').is(':checked')) {
                                if (jQuery.trim(jQuery("#scal_cta_form_id").val()) === '') {
                                    jQuery("#frm_cta_box").before('<div id="" class="error ctaError"><p>Form ID is Required to Enable This Function. Please visit <a href="https://swiftcloud.ai?pr=92">SwiftCloud.AI</a> (free or paid accounts will work) to generate this form.</p></div>');
                                    jQuery("#scal_cta_form_id").focus();
                                    e.preventDefault();
                                }
                            }
                        }
                    });

                    //
                    jQuery("#scal_cta_html_bg_color").spectrum({
                        preferredFormat: "hex",
                        color: "<?php echo (!empty($scal_cta_settings['scal_cta_html_bg_color']) ? $scal_cta_settings['scal_cta_html_bg_color'] : '#fff'); ?>",
                        showAlpha: true,
                        showButtons: false,
                        showInput: true
                    });
                    jQuery("#scal_cta_html_font_color").spectrum({
                        preferredFormat: "hex",
                        color: "<?php echo (!empty($scal_cta_settings['scal_cta_html_font_color']) ? $scal_cta_settings['scal_cta_html_font_color'] : '#000'); ?>",
                        showAlpha: true,
                        showButtons: false,
                        showInput: true
                    });

                    // change wp_editor's bg color
                    jQuery("#scal_cta_html_bg_color").change(function() {
                        jQuery("#wp-scal_cta_local_html_id-wrap #scal_cta_local_html_id").css("background", jQuery(this).val());
                        //preview section
                        jQuery("#scal_cta_prv_section").css("background", jQuery(this).val());
                    });
                    jQuery("#scal_cta_html_font_color").change(function() {
                        jQuery("#wp-scal_cta_local_html_id-wrap #scal_cta_local_html_id").css("color", jQuery(this).val());
                        //preview section
                        jQuery("#scal_cta_prv_section").css("color", jQuery(this).val());
                    });

                    /*Preview*/
                    jQuery("#cta_preview_popup").on("click", function() {
                        jQuery("#scal_cta_prv_section").fadeIn();
                    });
                    jQuery(".form-table").on("change", function() {
                        jQuery("#scal_cta_prv_section").hide();
                    });
                });
            </script>
        </div>
        <?php
    }

}
?>
