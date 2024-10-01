jQuery( document ).ready( function() {

    // Feature "report content" for BuddyPress and bbpress
    jQuery('.xtec-report').on('click', function () {

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

    // Controls the behavior of the "caixa-link" class, intended to be used in the "column" block.
    jQuery('.caixa-link').on('click', function () {

        // Get the path of the URL.
        let url_nodes = window.location.href.split('/');
        let search_str = url_nodes[2] + '/' + url_nodes[3];

        // Search the path in the id of the element (the id is expected to be a URL).
        let same_site = jQuery(this).attr('id').indexOf(search_str);

        // Open the link in the same tab if the id contains the path of the current page.
        if (same_site !== -1) {
            window.location.href = jQuery(this).attr('id');
        } else {
            window.open(jQuery(this).attr('id'), '_blank');
        }

    });

});
