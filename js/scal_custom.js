jQuery(document).ready(function($) {
    jQuery(".cw-main-title").on("click", function() {
        jQuery(".cw-calendar-widget-contet").slideToggle('slow', function() {
            jQuery(".cw-widget-toggle").text(jQuery(this).is(':visible') ? "-" : "+");
        });
    });

    //swift from hidden variables
    if (jQuery('#SC_fh_timezone').size() > 0) {
        jQuery('#SC_fh_timezone').val(jstz.determine().name());
    }
    if (jQuery('#SC_fh_capturepage').size() > 0) {
        jQuery('#SC_fh_capturepage').val(window.location.origin + window.location.pathname);
    }
    if (jQuery('#SC_fh_language').size() > 0) {
        jQuery('#SC_fh_language').val(window.navigator.userLanguage || window.navigator.language);
    }

    jQuery("#FrmWC").submit(function() {
        jQuery("#cw_send_btn").attr('disabled', 'disabled');
        jQuery("#cw_send_btn").css('background-color', '#9A9A9A');
        jQuery("#cw_send_btn i").removeClass('fa-paper-plane');
        jQuery("#cw_send_btn i").addClass('fa-spinner fa-pulse');
    });

    // ifram in modal
    jQuery("#cw_book_now_btn").on('click', function() {
        var url = jQuery(this).attr("data-url");
        jQuery(".cw-popup .cw-modal-content").html('');
        jQuery(".cw-popup .cw-modal-content").append('<iframe src="swiftschedule.com/' + url + '" />');
    });
    
    
});