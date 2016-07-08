jQuery( document ).ready( function() {
	if ( jQuery('a[class^="submitdelete deletion"]').length > 0 ){
		jQuery('a[class^="submitdelete deletion"]').on("click",function(e){

			e.preventDefault();

			add_wait_gif();

			var href = e.target.href;
			var post = href.split("post=");
			post = post[1].split("&");

			jQuery.post(
			    ajaxurl,
			    {
			        'action': 'resource_booking',
			        'data':   post,
			    },
			    function(response){
			        if(response != "true"){
			        	xtec_booking_add_message();
			        } else {
			        	window.location.href = href;
			        }
			    }
			);
		});
	}
});