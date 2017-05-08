jQuery( document ).ready( function() {

    // Feature "report content" for BuddyPress and bbpress
    jQuery('.xtec-report').live('click', function () {

        var id = jQuery(this).attr('id');

        var isBuddyPress = id.startsWith('xtec_bp_report-');
        var isBbpress = id.startsWith('xtec_bbpress_report-');
        var plugin = '';

        if (isBuddyPress) {
            var activity_id = jQuery(this).attr('id').replace('xtec_bp_report-', '');
            plugin = 'buddypress';

            jQuery('#'+id).addClass('loading');
        }

        if (isBbpress) {
            var activity_id = jQuery(this).attr('id').replace('xtec_bbpress_report-', '');
            plugin = 'bbpress';

            jQuery('#'+id).addClass('loading');
        }

        jQuery.post(
            ajaxurl,
            {
                action: 'xtec_report',
                plugin: plugin,
                id: activity_id,
            },
            function(response) {
                jQuery('#' + id).html(response);
                jQuery('#' + id).attr('class', 'disabled');
            }
        );

        return false;

    });
});
