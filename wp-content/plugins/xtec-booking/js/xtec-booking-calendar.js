function xtec_booking_get_thumbnail(resourceId, resource){

	jQuery('#xtec_booking_info_resource h2 span').text(resource);

	jQuery('#img_resource').html('');
	jQuery('#resource_no_data').css('display','block');
	jQuery('#no_image_resource').css('display','none');
	jQuery('#resource_wait').css('display','block');

	jQuery.post(
	    ajaxurl,
	    {
	        'action': 'get_thumbnail_resource',
	        'data':   resourceId,
	    })
	    .done( function( response ){

	    	jQuery('#resource_wait').css('display','none');

	    	if ( response != '' ){
	    		jQuery('#resource_no_data').css('display','none');
	    		jQuery('#img_resource').html(response);


			} else {
				jQuery('#resource_wait').css('display','none');
		    	jQuery('#img_resource').html('');
		    	jQuery('#resource_no_data').css('display','block');
		    	jQuery('#no_image_resource').css('display','block');
			}

	    } )
	    .fail( function( response ){

	    	jQuery('#resource_wait').css('display','none');
	    	jQuery('#img_resource').html('');
	    	jQuery('#resource_no_data').css('display','block');
	    	jQuery('#no_image_resource').css('display','block');

	    } );

}

jQuery( document ).ready( function() {

	//PREVENT DEFAULT BUTTONS CALENDAR - booking page
	if( jQuery('button[class^="button button-primary button-small"]').length > 0 ){
		jQuery('button[class^="button button-primary button-small"]').on('click',function(e){
			e.preventDefault();
		});
		jQuery('button[class^="button button-primary active"').on('click',function(e){
			e.preventDefault();
		});
		jQuery('button[class^="button button-primary"').on('click',function(e){
			e.preventDefault();
		});
	}

	//SHOW ONLY ACTIVE RESOURCE
	if( jQuery('select[name^="_xtec-booking-resource"]').length > 0 ){
		var selected;
		var id = jQuery('select[name^="_xtec-booking-resource"]').val();
		var resource = jQuery('select[name^="_xtec-booking-resource"] option:selected').text();

		if ( id != '' ){
			selected = id;
			jQuery("#xtec_calendar_wait").removeClass('display_div_wait');
			xtec_events( selected, 'booking' );
			xtec_booking_get_thumbnail(selected,resource);
		} else {
			xtec_booking_name_days();
		}

		jQuery('select[name^="_xtec-booking-resource"]').on('change',function(e){
			var id = jQuery('select[name^="_xtec-booking-resource"]').val();
			var resource = jQuery('select[name^="_xtec-booking-resource"] option:selected').text();

			if ( id != '' ){
				selected = id;
				jQuery("#xtec_calendar_wait").removeClass('display_div_wait');
				xtec_events(selected,'booking');
				xtec_booking_get_thumbnail(selected, resource);
			}

		});
	}

});