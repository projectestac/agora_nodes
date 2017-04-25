function not_acces_day_template(booking){

	var currentUrl = window.location.href;

	if ( booking == 'booking' || currentUrl.search("post-new.php") > 0 ){

		jQuery('a[data-toggle^="tooltip"]').removeAttr('data-original-title');
		jQuery('*[data-cal-date]').unbind("click");
		jQuery('.cal-cell').unbind("dblclick");

		xtec_booking_name_days();
	}
}

function xtec_booking_name_days(){

	if ( jQuery('div[class^="cal-cell1"]').length > 0 ){
		i = 0;
		jQuery('div[class^="cal-cell1"]').each(function() {
			if( i < 7 ){
				jQuery(this).text(days[i]);
				i = i+1;
			} else {
				return false;
			}
		});
	}
}

function xtec_booking_reset_modal(){
	jQuery('#modalTitle').html('<p><img id="xtec-booking-wait" src="../wp-admin/images/loading.gif"></p>');
	jQuery('#modalResource').text('');
	jQuery('#modalStartHour').text('');
	jQuery('#modalEndHour').text('');
	jQuery('#modalContent').html('');
	jQuery('#modalBy').text('');
}

function get_booking_content(events){

	jQuery('a[data-event-id="'+events.id+'"]').tooltip('hide');

	jQuery.post(
	    ajaxurl,
	    {
	        'action': 'get_event_modal',
	        'data':   events.id,
	    })
	    .done( function( response ){

	    	var request = JSON.parse( response );

	    	jQuery('#modalTitle').text(' '+request.title);
	    	jQuery('#modalResource').text(' '+request.resource);
	    	jQuery('#modalStartHour').text(' '+request.startHour);
	    	jQuery('#modalEndHour').text(' '+request.endHour);
	    	jQuery('#modalContent').html(' '+request.content);
	    	jQuery('#modalBy').text(' '+request.by);

	    } )

}

function xtec_add_calendar(data, booking = false){

	if ( jQuery('#xtec_calendar').length > 0 ){

		// Check language
		lang_locale = locale.locale;
		if ( lang_locale.search('_') != -1 ){
			lang_locale = lang_locale.replace('_','-');
		} else {
			lang_locale = lang_locale + '-ES';
		}
		
		var d = new Date();
		dateNow =  d.getFullYear()+'-';
		if ( d.getMonth()+1 > 9 ){ dateNow += (d.getMonth()+1)+'-'; } else { dateNow += '0'+(d.getMonth()+1)+'-'; }
		if ( d.getDate() > 9 ){ dateNow += d.getDate(); } else { dateNow += '0'+d.getDate(); }

		var options = {
			first_day: 1,
			events_source: data,
			view: 'month',
			tmpl_path: tmplsCalendar,
			tmpl_cache: false,
			day: dateNow,
			language: lang_locale,
			modal: "#events-modal",
			modal_title: function(events) {
				get_booking_content(events);
			},
			onAfterEventsLoad: function( events ) {
				if ( ! events ) {
					return;
				}
				var list = jQuery('#eventlist');
				list.html('');

				jQuery.each( events, function( key, val ) {
					jQuery(document.createElement('li'))
						.html('<a href="' + val.url + '">' + val.title + '</a>')
						.appendTo(list);
				});
			},
			onAfterViewLoad: function( view ) {
				jQuery('.page-header h3').text( this.getTitle() );
				jQuery('.btn-group button').removeClass( 'active' );
				jQuery('button[data-calendar-view="' + view + '"]').addClass( 'active' );
			},
			onAfterModalHidden: function(events) {
				xtec_booking_reset_modal();
			},
			classes: {
				months: {
					general: 'label'
				}
			}
		};

		var calendar = jQuery("#xtec_calendar").calendar( options );

		jQuery('input[id^="resource-"]').each(function() {
			jQuery(this).removeAttr('disabled',true);
		});

		jQuery("body").css("cursor", "default");
		jQuery("#xtec_calendar_wait").addClass('display_div_wait');

		jQuery('.btn-group button[data-calendar-nav]').each(function() {
			var $this = jQuery(this);
			$this.click(function() {
				calendar.navigate($this.data('calendar-nav'));
				not_acces_day_template(booking);
			});
		});

		jQuery('.btn-group button[data-calendar-view]').each(function() {
			var $this = jQuery(this);
			$this.click(function() {
				calendar.view($this.data('calendar-view'));
				not_acces_day_template(booking);
			});
		});

		jQuery('#first_day').change(function(){
			var value = jQuery(this).val();
			value = value.length ? parseInt(value) : null;
			calendar.setOptions({first_day: value});
			calendar.view();
		});

		jQuery('#language').change(function(){
			calendar.setLanguage(jQuery(this).val());
			calendar.view();
		});

		jQuery('#events-in-modal').change(function(){
			var val = jQuery(this).is(':checked') ? jQuery(this).val() : null;
			calendar.setOptions({modal: val});
		});
		jQuery('#format-12-hours').change(function(){
			var val = jQuery(this).is(':checked') ? true : false;
			calendar.setOptions({format12: val});
			calendar.view();
		});
		jQuery('#show_wbn').change(function(){
			var val = jQuery(this).is(':checked') ? true : false;
			calendar.setOptions({display_week_numbers: val});
			calendar.view();
		});
		jQuery('#show_wb').change(function(){
			var val = jQuery(this).is(':checked') ? true : false;
			calendar.setOptions({weekbox: vsal});
			calendar.view();
		});

		not_acces_day_template(booking);

	}

}

function xtec_change_button(){
	option = false;
	jQuery('input[id^="resource-"]').each(function() {
		select = jQuery(this).attr('checked');
		if( select ){
			option = true;
			return false;
		}
	});

	if( ! option ){
		jQuery('#xtec_selection').attr('data-action','select');
		jQuery('#xtec_selection').attr('value',selectResources);
	} else {
		jQuery('#xtec_selection').attr('data-action','unselect');
		jQuery('#xtec_selection').attr('value',unselectResources);
	}
}

function xtec_events( update = false, booking = false ){

	if ( update !== false ){

		if( update == '' ){
			update = 'no-events';
		}

		jQuery.post(
		    ajaxurl,
		    {
		        'action': 'resource_selected',
		        'data':   update,
		    })
		    .done( function( response ){

		    	var request = JSON.parse( response );

		    	jQuery('#xtec_calendar').remove();
		    	if ( booking == 'booking' ){
		    		jQuery('#xtec_calendar_pos').append('<div id="xtec_calendar" style="min-width:252px;"></div>');
		    	} else {
		    		jQuery('#xtec_calendar_pos').append('<div id="xtec_calendar" style="margin-left:50px;max-width:92%;"></div>');
		    	}

		    	if( booking == 'booking' ){
		    		xtec_add_calendar(request, 'booking');
		    	} else {
		    		xtec_add_calendar(request);
		    	}

		    	jQuery('#xtec_selection').removeAttr('disabled',true);

		    	xtec_change_button();
		    } )
		    .fail( function( response ){
		    	jQuery('input[id^="resource-"]').each(function() {
					jQuery(this).removeAttr('disabled',true);
				});

				xtec_change_button();

		    	jQuery('#xtec_selection').removeAttr('disabled',true);
				jQuery("body").css("cursor", "default");
				jQuery("#xtec_calendar_wait").addClass('display_div_wait');
		    } );

	} else {

		if ( typeof events !== 'undefined' ){
			return events;
		}

	}
}

function checkDateBefore(){
	var d = new Date();
	var dateNow = new Date(d.getFullYear(),d.getMonth(),d.getDate());

	var StartDate = jQuery( '#xtec-booking-start-date' ).val();
	var yearStart = parseInt(StartDate.substring(6,10));
	var monthStart = parseInt(StartDate.substring(3,5));
	var dayStart = parseInt(StartDate.substring(0,2));

	var DaySelected = new Date(yearStart,monthStart-1,dayStart,0,0,0);

	if ( dateNow > DaySelected ){
		jQuery('#xtec-booking-before-date').removeClass('display_message_date');
		jQuery('#xtec-booking-start-date').addClass('border_info_date');
	} else {
		var dateNow = new Date();
		var FinishTime = jQuery( '#xtec-booking-finish-time' ).val();
		if (FinishTime != ''){
			var hourFinish = parseInt(FinishTime.substring(0,2));
			var minutesFinish = parseInt(FinishTime.substring(3,5));
			var DaySelected = new Date(yearStart,monthStart-1,dayStart,hourFinish,minutesFinish,0);
			if ( dateNow > DaySelected ){
				jQuery('#xtec-booking-before-date').removeClass('display_message_date');
				jQuery('#xtec-booking-start-date').addClass('border_info_date');
			} else {
				jQuery('#xtec-booking-before-date').addClass('display_message_date');
				jQuery('#xtec-booking-start-date').removeClass('border_info_date');
			}
		} else {
			jQuery('#xtec-booking-before-date').addClass('display_message_date');
			jQuery('#xtec-booking-start-date').removeClass('border_info_date');
		}
	}

	var d = new Date();
	var dateNow = new Date(d.getFullYear(),d.getMonth(),d.getDate());

	var FinishDate = jQuery( '#xtec-booking-finish-date' ).val();

	if ( FinishDate != '' ){
		var yearFinish = parseInt( FinishDate.substring(6,10) );
		var monthFinish = parseInt( FinishDate.substring(3,5) );
		var dayFinish = parseInt( FinishDate.substring(0,2) );

		var DaySelected = new Date(yearFinish,monthFinish-1,dayFinish,0,0,0);

		if(DaySelected != ''){
			if ( dateNow > DaySelected ){
				jQuery('#xtec-booking-before-date-finish').removeClass('display_message_date');
				jQuery('#xtec-booking-finish-date').addClass('border_info_date');
			} else {
				if ( dateNow < DaySelected ){
					jQuery('#xtec-booking-before-date-finish').addClass('display_message_date');
					jQuery('#xtec-booking-finish-date').removeClass('border_info_date');
				} else {
					var dateNow = new Date();
					var FinishTime = jQuery( '#xtec-booking-finish-time' ).val();
					if (FinishTime != ''){
						var hourFinish = parseInt(FinishTime.substring(0,2));
						var minutesFinish = parseInt(FinishTime.substring(3,5));

						var DaySelected = new Date(yearFinish,monthFinish-1,dayFinish,hourFinish,minutesFinish,0);

						if ( dateNow > DaySelected ){
							jQuery('#xtec-booking-before-date-finish').removeClass('display_message_date');
							jQuery('#xtec-booking-finish-date').addClass('border_info_date');
						} else {
							jQuery('#xtec-booking-before-date-finish').addClass('display_message_date');
							jQuery('#xtec-booking-finish-date').removeClass('border_info_date');
						}
					} else {
						jQuery('#xtec-booking-before-date-finish').addClass('display_message_date');
						jQuery('#xtec-booking-finish-date').removeClass('border_info_date');
					}
				}
			}
		}
	}
}

function xtec_booking_check_dates(StartDate,FinishDate){

	var yearStart = parseInt(StartDate.substring(6,10));
	var yearFinish = parseInt(FinishDate.substring(6,10));
	var monthStart = parseInt(StartDate.substring(3,5));
	var monthFinish = parseInt(FinishDate.substring(3,5));
	var dayStart = parseInt(StartDate.substring(0,2));
	var dayFinish = parseInt(FinishDate.substring(0,2));

	var mkStart = new Date(yearStart,monthStart,dayStart,0,0,0);
	var mkEnd = new Date(yearFinish,monthFinish,dayFinish,0,0,0);

	var DaySelected = new Date(yearStart,monthStart-1,dayStart,0,0,0);

	checkDateBefore();

	if ( mkStart > mkEnd ){
		jQuery('#xtec-booking-finish-date').val(StartDate);
		xtec_booking_check_time();
	} else {
		xtec_booking_check_time();
		if ( yearFinish > yearStart || monthFinish > monthStart || dayFinish > dayStart ) {
			jQuery('#xtec-booking-row-days').removeClass('xtec-booking-display-none');
			jQuery('#text_days').removeClass('xtec-booking-display-none');
		} else {
			jQuery('#xtec-booking-row-days').addClass('xtec-booking-display-none');
			jQuery('#error_message_days').addClass('xtec-booking-display-none');
			jQuery('#text_days').addClass('xtec-booking-display-none');
			jQuery('input[name=_xtec-booking-day-monday]').prop("checked", false);
			jQuery('input[name=_xtec-booking-day-tuesday]').prop("checked", false);
			jQuery('input[name=_xtec-booking-day-wednesday]').prop("checked", false);
			jQuery('input[name=_xtec-booking-day-thursday]').prop("checked", false);
			jQuery('input[name=_xtec-booking-day-friday]').prop("checked", false);
			jQuery('input[name=_xtec-booking-day-saturday]').prop("checked", false);
			jQuery('input[name=_xtec-booking-day-sunday]').prop("checked", false);
		}
	}
}

function xtec_booking_addDay(){
	var StartDate = jQuery( '#xtec-booking-start-date' ).val();
	var FinishDate = jQuery( '#xtec-booking-finish-date' ).val();

	if( StartDate == FinishDate){
		var yearFinish = parseInt( FinishDate.substring(6,10) );
		var monthFinish = parseInt( FinishDate.substring(3,5) );
		var dayFinish = parseInt( FinishDate.substring(0,2) );
		var NewData = new Date( yearFinish,monthFinish,dayFinish+1,0,0,0 );
		if ( NewData.getMonth() < 10 ){
			if ( NewData.getDate() < 10 ){
				FinishDate = '0'+NewData.getDate()+'-0'+NewData.getMonth()+'-'+NewData.getFullYear();
			} else {
				FinishDate = NewData.getDate()+'-0'+NewData.getMonth()+'-'+NewData.getFullYear();
			}
		} else {
			if ( NewData.getDate() < 10 ){
				FinishDate = '0'+NewData.getDate()+'-'+NewData.getMonth()+'-'+NewData.getFullYear();
			} else {
				FinishDate = NewData.getDate()+'-'+NewData.getMonth()+'-'+NewData.getFullYear();
			}
		}
		jQuery('#xtec-booking-finish-date').val(FinishDate);
		jQuery('#xtec-booking-row-days').removeClass('xtec-booking-display-none');
		jQuery('#text_days').removeClass('xtec-booking-display-none');
	}
}

function xtec_booking_check_time(){
	var StartTime = jQuery( '#xtec-booking-start-time' ).val();
	var FinishTime = jQuery( '#xtec-booking-finish-time' ).val();
	var hourStart = parseInt(StartTime.substring(0,2));
	var minutesStart = parseInt(StartTime.substring(3,5));
	var hourFinish = parseInt(FinishTime.substring(0,2));
	var minutesFinish = parseInt(FinishTime.substring(3,5));
	if( StartTime != '' && jQuery.isNumeric(hourStart) && jQuery.isNumeric(minutesStart) ){
		if( FinishTime == '' || !jQuery.isNumeric(hourFinish) || !jQuery.isNumeric(minutesFinish) ){
			if ( minutesStart < 10 ){
				if ( hourStart == 9 ){
					FinishTime = (hourStart+1) + ':0' + minutesStart;
				} else if ( hourStart < 10 ){
					FinishTime = '0' + (hourStart+1) + ':0' + minutesStart;
				} else if ( hourStart == 23 ){
					FinishTime = '00:0' + minutesStart;
					xtec_booking_addDay();
				} else {
					FinishTime = (hourStart+1) + ':0' + minutesStart;
				}
			} else {
				if ( hourStart == 9 ){
					FinishTime = (hourStart+1) + ':' + minutesStart;
				} else if ( hourStart < 10 ){
					FinishTime = '0' + (hourStart+1) + ':' + minutesStart;
				} else if ( hourStart == 23 ){
					FinishTime = '00:' + minutesStart;
					xtec_booking_addDay();
				} else {
					FinishTime = (hourStart+1) + ':' + minutesStart;
				}
			}
			jQuery( '#xtec-booking-finish-time' ).removeAttr('disabled');
			jQuery( '#xtec-booking-finish-time' ).val(FinishTime);
			jQuery( '#xtec-booking-finish-time' ).focus();
		} else if ( hourFinish < hourStart ){
			if ( minutesStart < 10 ){
				if ( hourStart == 9 ){
					FinishTime = (hourStart+1) + ':0' + minutesStart;
				} else if ( hourStart < 10 ){
					FinishTime = '0' + (hourStart+1) + ':0' + minutesStart;
				} else if ( hourStart == 23 ){
					FinishTime = '00:0' + minutesStart;
					xtec_booking_addDay();
				} else {
					FinishTime = (hourStart+1) + ':0' + minutesStart;
				}
			} else {
				if ( hourStart == 9 ){
					FinishTime = (hourStart+1) + ':' + minutesStart;
				} else if ( hourStart < 10 ){
					FinishTime = '0' + (hourStart+1) + ':' + minutesStart;
				} else if ( hourStart == 23 ){
					FinishTime = '00:' + minutesStart;
					xtec_booking_addDay();
				} else {
					FinishTime = (hourStart+1) + ':' + minutesStart;
				}
			}
			jQuery( '#xtec-booking-finish-time' ).val(FinishTime);
			jQuery( '#xtec-booking-finish-time' ).focus();
		} else if ( hourFinish == hourStart ){
			if ( minutesFinish <= minutesStart ){
				if ( minutesStart < 10 ){
					if ( hourStart == 9 ){
						FinishTime = (hourStart+1) + ':0' + minutesStart;
					} else if ( hourStart < 10 ){
						FinishTime = '0' + (hourStart+1) + ':0' + minutesStart;
					} else if ( hourStart == 23 ){
						FinishTime = '00:0'+minutesStart;
						xtec_booking_addDay();
					} else {
						FinishTime = (hourStart+1)+':0'+minutesStart;
					}
				} else {
					if ( hourStart == 9 ){
						FinishTime = (hourStart+1) + ':' + minutesStart;
					} else if ( hourStart < 10 ){
						FinishTime = '0' + (hourStart+1) + ':' + minutesStart;
					} else if ( hourStart == 23 ){
						FinishTime = '00:'+minutesStart;
						xtec_booking_addDay();
					} else {
						FinishTime = (hourStart+1)+':'+minutesStart;
					}
				}
				jQuery( '#xtec-booking-finish-time' ).val(FinishTime);
				jQuery( '#xtec-booking-finish-time' ).focus();
			}
		}
	}
}

function add_wait_gif(){
	jQuery('#message').remove();
	jQuery('<p><img id="xtec-booking-wait" src="../wp-admin/images/loading.gif"></p>').insertAfter('.wrap h1');
}

function xtec_booking_add_message(){
	jQuery('#xtec-booking-wait').remove();
	var message = '<div id="message" class="notice notice-error"><p>'+message_resources+'</p></div>';
	jQuery(message).insertAfter('.wrap h1');
}

jQuery( document ).ready( function() {

	if( jQuery('#xtec-booking-start-date').length > 0 ){

		// Datetimepicker settings
		jQuery.datetimepicker.setLocale('ca');

		jQuery('#xtec-booking-start-date').datetimepicker({
			timepicker:false,
	 		format:'d-m-Y',
	 		dayOfWeekStart: 1
		});

		jQuery('#xtec-booking-finish-date').datetimepicker({
			timepicker:false,
	 		format:'d-m-Y',
	 		dayOfWeekStart: 1
		});

		jQuery('#xtec-booking-start-time').datetimepicker({
		  datepicker:false,
		  format:'H:i',
		  mask:true,
		});

		jQuery('#xtec-booking-finish-time').datetimepicker({
		  datepicker:false,
		  format:'H:i',
		  mask:true,
		});

	}

    // DATE
	jQuery( '#xtec-booking-start-date' ).on( 'change' , function(e){
		var StartDate = jQuery( e.target ).val();
		var FinishDate = jQuery( '#xtec-booking-finish-date' ).val();
		if ( StartDate == '' ){
			jQuery( '#xtec-booking-finish-date' ).val('');
			jQuery( '#xtec-booking-finish-date' ).attr('disabled', 'disabled');
		} else if ( FinishDate == '' ){
			jQuery( '#xtec-booking-finish-date' ).val(StartDate);
			jQuery( '#xtec-booking-finish-date' ).removeAttr('disabled');
		} else {
			xtec_booking_check_dates(StartDate,FinishDate);
		}
	});

	jQuery( '#xtec-booking-finish-date' ).on( 'change' , function(e){
		var FinishDate = jQuery(e.target).val();
		var StartDate = jQuery( '#xtec-booking-start-date' ).val();
		xtec_booking_check_dates(StartDate,FinishDate);
	});

	// TIME
	jQuery( '#xtec-booking-start-time' ).on( 'blur' , function(e){
		var StartTime = jQuery( e.target ).val();
		var hour = parseInt(StartTime.substring(0,2));
		var minutes = parseInt(StartTime.substring(3,5));
		var FinishTime = jQuery( '#xtec-booking-finish-time' ).val();
		if ( StartTime == '' || !jQuery.isNumeric(hour) || !jQuery.isNumeric(minutes) ){
			jQuery( '#xtec-booking-start-time' ).val('');
			jQuery( '#xtec-booking-finish-time' ).val('');
			jQuery( '#xtec-booking-finish-time' ).attr('disabled', 'disabled');
		} else {
			xtec_booking_check_time();
		}
	});

	jQuery( '#xtec-booking-finish-time' ).on( 'blur' , function(e){
		var FinishTime = jQuery( e.target ).val();
		var hour = parseInt(FinishTime.substring(0,2));
		var minutes = parseInt(FinishTime.substring(3,5));
		var StartTime = jQuery( '#xtec-booking-start-time' ).val();
		var StartDate = jQuery( '#xtec-booking-start-date' ).val();
		var FinishDate = jQuery( '#xtec-booking-finish-date' ).val();
		checkDateBefore();
		if( FinishTime == '' || !jQuery.isNumeric(hour) || !jQuery.isNumeric(minutes) ){
			jQuery( '#xtec-booking-start-time' ).val(StartTime);
		} else if ( StartDate == FinishDate ){
			xtec_booking_check_time();
		}
	});

	jQuery('#post').on('submit',function(e){

		var StartDate = jQuery( '#xtec-booking-start-date' ).val();
		var FinishDate = jQuery( '#xtec-booking-finish-date' ).val();

		if ( StartDate != FinishDate ){

			var checked = false;
			if ( jQuery('input[name=_xtec-booking-day-monday]').is(":checked") == true ){ checked = true; }
			if ( jQuery('input[name=_xtec-booking-day-tuesday]').is(":checked") == true ){ checked = true; }
			if ( jQuery('input[name=_xtec-booking-day-wednesday]').is(":checked") == true ){ checked = true; }
			if ( jQuery('input[name=_xtec-booking-day-thursday]').is(":checked") == true ){ checked = true; }
			if ( jQuery('input[name=_xtec-booking-day-friday]').is(":checked") == true ){ checked = true; }
			if ( jQuery('input[name=_xtec-booking-day-saturday]').is(":checked") == true ){ checked = true; }
			if ( jQuery('input[name=_xtec-booking-day-sunday]').is(":checked") == true ){ checked = true; }

			if ( checked != false ){
				jQuery('#error_message_days').addClass('xtec-booking-display-none');
				return true;
			} else {
				jQuery('#error_message_days').removeClass('xtec-booking-display-none');
				alert(dies_reserva);
				jQuery('input[name=_xtec-booking-day-monday]').focus();
				jQuery('#publish').removeClass('disabled');
				jQuery('span[class*="spinner"]').removeClass('is-active');
				return false;
			}

		} else {

			return true;

		}

	});

	dataEvents = xtec_events();

	xtec_add_calendar(dataEvents);

	var currentUrl = window.location.href;

	if( currentUrl.search("post_type=calendar_resources") > 0 ){

		jQuery('span.trash').on("click",function(e){

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

		if( jQuery('#bulk-action-selector-top').length > 0 ){
			jQuery('#bulk-action-selector-top option[value="trash"]').remove();
			jQuery('#bulk-action-selector-bottom option[value="trash"]').remove();
		}
	}

	if( jQuery('input[id^="resource-"]').length > 0 ){

		jQuery('input[id^="resource-"]').on("change", function(e){

			var bookingEvents;
			var selected = [];
			var i = 0;

			jQuery("body").css("cursor", "progress");
			jQuery("#xtec_calendar_wait").removeClass('display_div_wait');

			jQuery('input[id^="resource-"]').each(function() {
				jQuery(this).attr('disabled',true);
				if( jQuery(this).prop('checked') == true ){
					id = jQuery(this).attr('id');
					id = id.split('resource-');
					selected[i] = id[1];
					i++;
				}
			});

			xtec_events(selected);

		});

	}

	if( currentUrl.search("post_type=calendar_booking") > 0 ){
		jQuery('#bulk-action-selector-top option[value="trash"]').text(textSelectDelete);
		jQuery('#bulk-action-selector-bottom option[value="trash"]').text(textSelectDelete);

		jQuery('#posts-filter').on('submit',function(e){
			var OptionSelect = jQuery('#bulk-action-selector-top').val();
			var OptionSelect2 = jQuery('#bulk-action-selector-bottom').val();
			if( OptionSelect == 'trash' || OptionSelect2 == 'trash' ){
				var confirmRequest = confirm(confirmText);
				if( confirmRequest == false ){
					e.preventDefault();
				}
			}
		});

		jQuery('span.trash').on("click",function(e){
			var confirmRequest = confirm(confirmText);
			if( confirmRequest == false ){
				e.preventDefault();
			}
		});

		if( jQuery('#bulk-action-selector-top').length > 0 ){
			jQuery('#bulk-action-selector-top option[value="edit"]').remove();
			jQuery('#bulk-action-selector-bottom option[value="edit"]').remove();
		}
	}

	if ( ( jQuery('#delete-action').length > 0 ) && ( jQuery('#xtec-booking-start-date').length > 0 ) ){
		jQuery('a[class*="submitdelete deletion"]').on('click',function(e){
			var confirmRequest = confirm(confirmTextInd);
			if( confirmRequest == false ){
				e.preventDefault();
			}
		});
	}

	// SELECT / UNSELECT RESOURCES
	jQuery('#xtec_selection').on('click',function(){

		jQuery('#xtec_selection').attr('disabled',true);
		option = jQuery('#xtec_selection').attr('data-action');

		if ( option == 'unselect' ){
			jQuery('[name^="resource-"]').each(function(){
				jQuery(this).attr('checked',false);
			});
			jQuery('#xtec_selection').attr('data-action','select');
			jQuery('#xtec_selection').attr('value',selectResources);
		} else {
			jQuery('[name^="resource-"]').each(function(){
				jQuery(this).attr('checked',true);
			});
			jQuery('#xtec_selection').attr('data-action','unselect');
			jQuery('#xtec_selection').attr('value',unselectResources);
		}

		jQuery('[name^="resource-"]').each(function(){
			jQuery(this).trigger('change');
			return false;
		});

	});

});
