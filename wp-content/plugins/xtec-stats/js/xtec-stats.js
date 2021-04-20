//FUNCTIONS

//Initialize select to change limits results
function xtec_stats_change_select(data){

	var option;

	switch(data){
		case 10:
			option = 0;
			break;
		case 25:
			option = 1;
			break;
		case 50:
			option = 2;
			break;
		case 100:
			option = 3;
			break;
		default:
			option = 4;
			break;
	}

	jQuery('#xtec-stats-limitResults option:eq('+option+')').prop('selected', true);

}

//Modify placeholder to change input radio
function xtec_stats_change_placeholder(e,username,content){

	if(e.target.value == 1){
		jQuery('#search_content').attr('placeholder',username);
	}else{
		jQuery('#search_content').attr('placeholder',content);
	}

}

//
function xtec_stats_change_arrow_direction(object){
	var spanClass = jQuery(object).find('span').attr('class');
	if(spanClass != 'dashicons dashicons-arrow-down xtec-stats-no-show xtec-stats-arrow'){
		if(spanClass == 'dashicons dashicons-arrow-down xtec-stats-arrow'){
			jQuery(object).find('span').attr('class','dashicons dashicons-arrow-up xtec-stats-arrow');
		}else{
			jQuery(object).find('span').attr('class','dashicons dashicons-arrow-down xtec-stats-arrow');
		}
	}
}

function xtec_stats_change_view(id){
	if( id == "tab_1" ){
		if ( jQuery('#message').length > 0 ){
			jQuery('#message').addClass('hidden-container');
		}
		jQuery('#target_2').addClass('hidden-container');
		jQuery('#target_1').removeClass('hidden-container');
		jQuery('#tab_1').addClass('nav-tab-active');
		jQuery('#tab_2').removeClass('nav-tab-active');
	} else {
		if ( jQuery('#message').length > 0 ){
			jQuery('#message').removeClass('hidden-container');
		}
		jQuery('#target_1').addClass('hidden-container');
		jQuery('#target_2').removeClass('hidden-container');
		jQuery('#tab_2').addClass('nav-tab-active');
		jQuery('#tab_1').removeClass('nav-tab-active');
	}
}

//Add events
jQuery(document).ready(function(e){

	jQuery('#xtec-stats-limitResults').on('change',function(e){
		jQuery('#xtec-stats-form-search').submit();
	});

	jQuery('input[name="search_type"]').on('change',function(e){

		if(typeof xtec_stats_username === 'undefined'){
			xtec_stats_username = 'username';
		}

		if(typeof xtec_stats_content === 'undefined'){
			xtec_stats_content = 'content';
		}

		xtec_stats_change_placeholder(e,xtec_stats_username,xtec_stats_content);
	});

	//Arrows to orderBy
	jQuery('button[id=xtec-stats-datetime]').on('mouseenter',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-datetime]'); });
	jQuery('button[id=xtec-stats-datetime]').on('mouseleave',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-datetime]'); });
	jQuery('button[id=xtec-stats-username]').on('mouseenter',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-username]'); });
	jQuery('button[id=xtec-stats-username]').on('mouseleave',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-username]'); });
	jQuery('button[id=xtec-stats-content]').on('mouseenter',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-content]'); });
	jQuery('button[id=xtec-stats-content]').on('mouseleave',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-content]'); });
	jQuery('button[id=xtec-stats-uri]').on('mouseenter',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-uri]'); });
	jQuery('button[id=xtec-stats-uri]').on('mouseleave',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-uri]'); });
	jQuery('button[id=xtec-stats-ip]').on('mouseenter',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-ip]'); });
	jQuery('button[id=xtec-stats-ip]').on('mouseleave',function(e){ xtec_stats_change_arrow_direction('button[id=xtec-stats-ip]'); });

	if(typeof xtec_stats_limitResults !== 'undefined'){
		xtec_stats_change_select(xtec_stats_limitResults);
	}

	jQuery('a[id*="tab_"]').on('click',function(e){
		xtec_stats_change_view(e.target.id);
	});

});

