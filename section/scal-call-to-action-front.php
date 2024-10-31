<?php

function scal_is_custom_post_type($post = NULL) {
    $all_custom_post_types = get_post_types(array('_builtin' => FALSE));

    // there are no custom post types
    if (empty($all_custom_post_types))
        return FALSE;

    $custom_types = array_keys($all_custom_post_types);
    $current_post_type = get_post_type($post);

    // could not detect current type
    if (!$current_post_type)
        return FALSE;

    return in_array($current_post_type, $custom_types);
}

$scal_cta_settings = get_option('scal_cta_settings');
if (!$scal_cta_settings['scal_cta_flag']) {
    return;
} else {
    $scal_cta_settings = get_option('scal_cta_settings');

    if (!empty($scal_cta_settings['scal_cta_local_html_content']) || !empty($scal_cta_settings['scal_cta_form_id'])) {
        remove_filter('the_content', 'wpautop');
        $br = false;
        add_filter('the_content', function( $content ) use ( $br ) {
                    return wpautop($content, $br);
                }, 10);

        add_filter('the_content', 'scal_filter_call_to_action_box',11);

        function scal_filter_call_to_action_box($content) {
            $scal_cta_settings = get_option('scal_cta_settings');
            $cta_show_flag = '';

            if (!empty($scal_cta_settings['scal_cta_show_on'])) {
                if (in_array('pages', $scal_cta_settings['scal_cta_show_on'])) {
                    if (is_page()) {
                        $cta_show_flag = "true";
                        if (!empty($scal_cta_settings['scal_cta_dont_show_on'])) {
                            if (in_array('home', $scal_cta_settings['scal_cta_dont_show_on'])) {
                                if (is_front_page() && is_home())
                                    $cta_show_flag = '';
                                else if (is_front_page())
                                    $cta_show_flag = '';
                            }
                        }
                    }
                }
                if (!empty($scal_cta_settings['scal_cta_show_on']) && in_array('posts', $scal_cta_settings['scal_cta_show_on'])) {
                    if (is_single())
                        $cta_show_flag = "true";
                }
                if (!empty($scal_cta_settings['scal_cta_dont_show_on']) && in_array('cpt', $scal_cta_settings['scal_cta_dont_show_on'])) {
                    // check if it is custom post type
                    if (scal_is_custom_post_type())
                        $cta_show_flag = "";
                }
            }

            $cta_inline_style = '';
            if ($cta_show_flag == 'true') {
                $cta_inline_style.=!empty($scal_cta_settings['scal_cta_html_bg_color']) ? "background:" . $scal_cta_settings['scal_cta_html_bg_color'] . ";" : '';
                $cta_inline_style .=!empty($scal_cta_settings['scal_cta_html_css']) ? trim($scal_cta_settings['scal_cta_html_css']) : '';

                $strToAppend = '';
                $strToAppend.='<div class="scal-cta-content" style="' . $cta_inline_style . '">';

                // check is htmleditor or swift form

                if ($scal_cta_settings['scal_cta_contents_flag']) {
                    $strToAppend.= stripslashes(($scal_cta_settings['scal_cta_local_html_content']));
                } else {
                    $strToAppend.='[swiftform id="' . $scal_cta_settings['scal_cta_form_id'] . '"]';
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function() {
                            var change_btn_txt = '<?php echo $scal_cta_settings['scal_cta_form_btn_text']; ?>';
                            jQuery(".cta-content #form_submit_btn").val(change_btn_txt);
                        });
                    </script>
                    <?php
                }

                $strToAppend.='</div>';
                $content = $content . $strToAppend;
            }
            return $content;
        }

    }
}
?>