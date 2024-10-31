jQuery(document).ready(function($) {
    /* tooltip */
    //jQuery(".ttip").tooltip();

    /* plugin activation notice dismiss.*/
    jQuery("#scal-admin-notice .notice-dismiss").on('click', function() {
        var data = {
            'action': 'scal_dismiss_notice'
        };
        jQuery.post(scal_admin_ajax_obj.ajax_url, data, function(response) {

        });
    });

    jQuery("#scal_event_recurring").on("change", function() {
        if (jQuery(this).val() == "Recurring") {
            jQuery(".rucurring_container").slideDown();
        } else {
            jQuery(".rucurring_container").slideUp();
        }
    });

    jQuery("#scal_event_repeat").on("change", function() {
        if (jQuery(this).val() == "1" || jQuery(this).val() == "5" || jQuery(this).val() == "6" || jQuery(this).val() == "7") {
            jQuery(".scal_event_repeat_every_container").slideDown();
            jQuery("#repeat_every_label").text($(this).find(':selected').attr('title'));
        } else {
            jQuery(".scal_event_repeat_every_container").slideUp();
        }

        if (jQuery(this).val() == "6") {
            jQuery(".scal_event_repeat_every_week_container").slideDown();
        } else {
            jQuery(".scal_event_repeat_every_week_container").slideUp();
        }
    });
});