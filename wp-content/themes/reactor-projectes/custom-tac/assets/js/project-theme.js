jQuery(document).data('xtec_executed',false);

function xtec_scroll_window(){
    var Scrollposition = jQuery(window).scrollTop();
    var barHeight = jQuery('#wpadminbar').height();
    if ( ( Scrollposition > 100 ) && ( jQuery(document).data('xtec_executed') == false ) ){
        jQuery(document).data('xtec_executed',true);
        jQuery('#scroll-on').fadeIn('400');
        jQuery('#wpadminbar').addClass('bar_project_logo');
    } else if ( ( Scrollposition <= 100 ) && ( jQuery(document).data('xtec_executed') == true ) ){
        jQuery(document).data('xtec_executed',false);
        jQuery('#scroll-on').css('display','none');
        jQuery('#wpadminbar').removeClass('bar_project_logo');
    }
}

jQuery( document ).ready( function() {
    var Scrollposition = jQuery(window).scrollTop();
    if ( Scrollposition != 0 ){
        jQuery('#scroll-on').fadeIn('fast');
        jQuery('#wpadminbar').addClass('bar_project_logo');
    }

    jQuery( window ).scroll( function(e) {
        xtec_scroll_window();
    });
});
