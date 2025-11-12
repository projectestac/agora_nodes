function not_acces_day_template(booking){

	let currentUrl = window.location.href;

	if ( booking == 'booking' || currentUrl.search("post-new.php") > 0 ){

		jQuery('a[data-toggle^="tooltip"]').removeAttr('data-original-title');
		jQuery('*[data-cal-date]').unbind("click");
		jQuery('.cal-cell').unbind("dblclick");

		xtec_booking_name_days();
	}
}

function xtec_booking_name_days(){

	if ( jQuery('div[class^="cal-cell1"]').length > 0 ){
		let i = 0;
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

	    	let request = JSON.parse( response );

	    	jQuery('#modalTitle').text(' '+request.title);
	    	jQuery('#modalResource').text(' '+request.resource);
	    	jQuery('#modalStartHour').text(' '+request.startHour);
	    	jQuery('#modalEndHour').text(' '+request.endHour);
	    	jQuery('#modalContent').html(' '+request.content);
	    	jQuery('#modalBy').text(' '+request.by);

	    } )

}

function xtec_add_calendar(data, booking = false) {

    if (jQuery('#xtec_calendar').length === 0) return;

    // Check language
    const lang_locale = xtec_get_locale();

    // Get today's date in YYYY-MM-DD format
    const dateNow = xtec_get_today_date();

    const options = {
        first_day: 1,
        events_source: data,
        view: 'month',
        tmpl_path: tmplsCalendar,
        tmpl_cache: false,
        day: dateNow,
        language: lang_locale,
        modal: "#events-modal",
        modal_title: get_booking_content,
        onAfterEventsLoad: xtec_after_events_load,
        onAfterViewLoad: xtec_after_view_load,
        onAfterModalHidden: xtec_booking_reset_modal,
        classes: {
            months: {
                general: 'label'
            }
        }
    };

    const calendar = jQuery("#xtec_calendar").calendar(options);

    // Enable all resource inputs
    jQuery('input[id^="resource-"]').prop('disabled', false);

    jQuery("body").css("cursor", "default");
    jQuery("#xtec_calendar_wait").addClass('display_div_wait');

    xtec_bind_calendar_navigation(calendar, booking);
    xtec_bind_calendar_view(calendar, booking);
    xtec_bind_calendar_controls(calendar);
    not_acces_day_template(booking);
}

/**
 * Format and return the locale string
 */
function xtec_get_locale() {
    let lang_locale = locale.locale;
    if (lang_locale.includes('_')) {
        lang_locale = lang_locale.replace('_', '-');
    } else {
        lang_locale += '-ES';
    }
    return lang_locale;
}

/**
 * Return today's date in YYYY-MM-DD format
 */
function xtec_get_today_date() {
    const d = new Date();
    const month = (d.getMonth() + 1).toString().padStart(2, '0');
    const day = d.getDate().toString().padStart(2, '0');
    return `${d.getFullYear()}-${month}-${day}`;
}

/**
 * Populate event list after events load
 */
function xtec_after_events_load(events) {
    if (!events) return;
    const list = jQuery('#eventlist').empty();
    jQuery.each(events, (key, val) => {
        jQuery('<li>').html(`<a href="${val.url}">${val.title}</a>`).appendTo(list);
    });
}

/**
 * Update page header and active button after view load
 */
function xtec_after_view_load(view) {
    jQuery('.page-header h3').text(this.getTitle());
    jQuery('.btn-group button').removeClass('active');
    jQuery(`button[data-calendar-view="${view}"]`).addClass('active');
}

/**
 * Bind navigation buttons to calendar
 */
function xtec_bind_calendar_navigation(calendar, booking) {
    jQuery('.btn-group button[data-calendar-nav]').each(function() {
        const $this = jQuery(this);
        $this.click(() => {
            calendar.navigate($this.data('calendar-nav'));
            not_acces_day_template(booking);
        });
    });
}

/**
 * Bind view buttons to calendar
 */
function xtec_bind_calendar_view(calendar, booking) {
    jQuery('.btn-group button[data-calendar-view]').each(function() {
        const $this = jQuery(this);
        $this.click(() => {
            calendar.view($this.data('calendar-view'));
            not_acces_day_template(booking);
        });
    });
}

/**
 * Bind other calendar control elements
 */
function xtec_bind_calendar_controls(calendar) {
    jQuery('#first_day').change(function() {
        const value = jQuery(this).val().length ? parseInt(jQuery(this).val()) : null;
        calendar.setOptions({ first_day: value });
        calendar.view();
    });

    jQuery('#language').change(function() {
        calendar.setLanguage(jQuery(this).val());
        calendar.view();
    });

    jQuery('#events-in-modal').change(function() {
        const val = jQuery(this).is(':checked') ? jQuery(this).val() : null;
        calendar.setOptions({ modal: val });
    });

    jQuery('#format-12-hours').change(function() {
        calendar.setOptions({ format12: jQuery(this).is(':checked') });
        calendar.view();
    });

    jQuery('#show_wbn').change(function() {
        calendar.setOptions({ display_week_numbers: jQuery(this).is(':checked') });
        calendar.view();
    });

    jQuery('#show_wb').change(function() {
        calendar.setOptions({ weekbox: vsal });
        calendar.view();
    });
}


function xtec_change_button(){
	let option = false;
	jQuery('input[id^="resource-"]').each(function() {
		let select = jQuery(this).attr('checked');
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

		    	let request = JSON.parse( response );

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

function checkDateBefore() {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

    // Helper function to parse date string "DD/MM/YYYY"
    function parseDate(dateStr) {
        const day = parseInt(dateStr.substring(0, 2));
        const month = parseInt(dateStr.substring(3, 5)) - 1;
        const year = parseInt(dateStr.substring(6, 10));
        return { day, month, year };
    }

    // Helper function to get Date object with optional time
    function getDateTime(dateStr, timeStr = '') {
        const { day, month, year } = parseDate(dateStr);
        if (timeStr) {
            const hour = parseInt(timeStr.substring(0, 2));
            const minute = parseInt(timeStr.substring(3, 5));
            return new Date(year, month, day, hour, minute, 0);
        }
        return new Date(year, month, day, 0, 0, 0);
    }

    // Helper function to toggle error display
    function toggleError(selector, condition) {
        if (condition) {
            jQuery(selector).removeClass('display_message_date');
        } else {
            jQuery(selector).addClass('display_message_date');
        }
    }

    // Check start date
    const startDateStr = jQuery('#xtec-booking-start-date').val();
    const startTimeStr = jQuery('#xtec-booking-finish-time').val();
    if (startDateStr) {
        const startDate = getDateTime(startDateStr, startTimeStr);
        const isValid = now <= startDate;
        toggleError('#xtec-booking-before-date', isValid);
        jQuery('#xtec-booking-start-date').toggleClass('border_info_date', !isValid);
    }

    // Check finish date
    const finishDateStr = jQuery('#xtec-booking-finish-date').val();
    if (finishDateStr) {
        const finishDate = getDateTime(finishDateStr, startTimeStr);
        const isFinishValid = now <= finishDate;
        toggleError('#xtec-booking-before-date-finish', isFinishValid);
        jQuery('#xtec-booking-finish-date').toggleClass('border_info_date', !isFinishValid);
    }
}


function xtec_booking_check_dates(StartDate,FinishDate){

	let yearStart = parseInt(StartDate.substring(6,10));
	let yearFinish = parseInt(FinishDate.substring(6,10));
	let monthStart = parseInt(StartDate.substring(3,5));
	let monthFinish = parseInt(FinishDate.substring(3,5));
	let dayStart = parseInt(StartDate.substring(0,2));
	let dayFinish = parseInt(FinishDate.substring(0,2));

	let mkStart = new Date(yearStart,monthStart,dayStart,0,0,0);
	let mkEnd = new Date(yearFinish,monthFinish,dayFinish,0,0,0);

	let DaySelected = new Date(yearStart,monthStart-1,dayStart,0,0,0);

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
	let StartDate = jQuery( '#xtec-booking-start-date' ).val();
	let FinishDate = jQuery( '#xtec-booking-finish-date' ).val();

	if( StartDate == FinishDate){
		let yearFinish = parseInt( FinishDate.substring(6,10) );
		let monthFinish = parseInt( FinishDate.substring(3,5) );
		let dayFinish = parseInt( FinishDate.substring(0,2) );
		let NewData = new Date( yearFinish,monthFinish,dayFinish+1,0,0,0 );
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

function xtec_booking_check_time() {
    const startInput = jQuery('#xtec-booking-start-time');
    const finishInput = jQuery('#xtec-booking-finish-time');

    const startTime = parseTime(startInput.val());
    let finishTime = parseTime(finishInput.val());

    if (!startTime) return;

    if (!finishTime || isBeforeOrEqual(finishTime, startTime)) {
        finishTime = computeFinishTime(startTime);
        finishInput.removeAttr('disabled').val(formatTime(finishTime)).focus();
    }

    /**
     * Parse "HH:MM" string into object {hour, minutes}
     */
    function parseTime(timeStr) {
        if (!timeStr) return null;
        const [h, m] = timeStr.split(':').map(Number);
        return (Number.isInteger(h) && Number.isInteger(m)) ? { hour: h, minutes: m } : null;
    }

    /**
     * Return true if timeA <= timeB
     */
    function isBeforeOrEqual(a, b) {
        return a.hour < b.hour || (a.hour === b.hour && a.minutes <= b.minutes);
    }

    /**
     * Compute default finish time (+1h)
     * Wraps to 00:MM and adds a day if start is 23h
     */
    function computeFinishTime(start) {
        const newHour = (start.hour + 1) % 24;
        if (start.hour === 23) xtec_booking_addDay();
        return { hour: newHour, minutes: start.minutes };
    }

    /**
     * Format {hour, minutes} to "HH:MM"
     */
    function formatTime(t) {
        const h = t.hour.toString().padStart(2, '0');
        const m = t.minutes.toString().padStart(2, '0');
        return `${h}:${m}`;
    }
}

function add_wait_gif(){
	jQuery('#message').remove();
	jQuery('<p><img id="xtec-booking-wait" src="../wp-admin/images/loading.gif"></p>').insertAfter('.wrap h1');
}

function xtec_booking_add_message(){
	jQuery('#xtec-booking-wait').remove();
	let message = '<div id="message" class="notice notice-error"><p>'+message_resources+'</p></div>';
	jQuery(message).insertAfter('.wrap h1');
}

jQuery(document).ready(function ($) {
    const $startDate   = $('#xtec-booking-start-date');
    const $finishDate  = $('#xtec-booking-finish-date');
    const $startTime   = $('#xtec-booking-start-time');
    const $finishTime  = $('#xtec-booking-finish-time');
    const currentUrl   = window.location.href;

    /** ---------------- Helpers ---------------- */
    const disableFinishDate = () => $finishDate.val('').prop('disabled', true);
    const enableFinishDate  = (value) => $finishDate.val(value).prop('disabled', false);

    const isValidTime = (time) => {
        if (!time) return false;
        const hour    = parseInt(time.substring(0, 2), 10);
        const minutes = parseInt(time.substring(3, 5), 10);
        return $.isNumeric(hour) && $.isNumeric(minutes);
    };

    /** ---------------- Init Pickers ---------------- */
    function initPickers() {
        if ($startDate.length === 0) return;

        $.datetimepicker.setLocale('ca');

        const initDatepicker = (el) =>
            $(el).datetimepicker({ timepicker: false, format: 'd-m-Y', dayOfWeekStart: 1 });

        const initTimepicker = (el) =>
            $(el).datetimepicker({ datepicker: false, format: 'H:i', mask: true });

        [$startDate, $finishDate].forEach(initDatepicker);
        [$startTime, $finishTime].forEach(initTimepicker);
    }

    /** ---------------- Date Handlers ---------------- */
    function bindDateEvents() {
        $startDate.on('change', function () {
            const startDate  = $(this).val();
            const finishDate = $finishDate.val();

            if (!startDate) return disableFinishDate();
            if (!finishDate) return enableFinishDate(startDate);
            xtec_booking_check_dates(startDate, finishDate);
        });

        $finishDate.on('change', function () {
            xtec_booking_check_dates($startDate.val(), $(this).val());
        });
    }

    /** ---------------- Time Handlers ---------------- */
    function bindTimeEvents() {
        $startTime.on('blur', function () {
            const startTime = $(this).val();
            if (!isValidTime(startTime)) {
                $startTime.val('');
                disableFinishDate();
            } else {
                xtec_booking_check_time();
            }
        });

        $finishTime.on('blur', function () {
            const finishTime = $(this).val();
            checkDateBefore();
            if (!isValidTime(finishTime)) return $startTime.val($startTime.val());
            if ($startDate.val() === $finishDate.val()) xtec_booking_check_time();
        });
    }

    /** ---------------- Form Submit ---------------- */
    function bindFormSubmit() {
        $('#post').on('submit', function () {
            const startDate  = $startDate.val();
            const finishDate = $finishDate.val();

            if (startDate === finishDate) return true;

            const days = [
                '_xtec-booking-day-monday',
                '_xtec-booking-day-tuesday',
                '_xtec-booking-day-wednesday',
                '_xtec-booking-day-thursday',
                '_xtec-booking-day-friday',
                '_xtec-booking-day-saturday',
                '_xtec-booking-day-sunday'
            ];

            const hasCheckedDay = days.some(name => $(`input[name=${name}]`).is(':checked'));
            if (!hasCheckedDay) {
                $('#error_message_days').removeClass('xtec-booking-display-none');
                alert(dies_reserva);
                $('input[name=_xtec-booking-day-monday]').focus();
                $('#publish').removeClass('disabled');
                $('span[class*="spinner"]').removeClass('is-active');
                return false;
            }
            $('#error_message_days').addClass('xtec-booking-display-none');
            return true;
        });
    }

    /** ---------------- Resources ---------------- */
    function handleResources() {
        if (!currentUrl.includes("post_type=calendar_resources")) return;

        $('span.trash').on("click", function (e) {
            e.preventDefault();
            add_wait_gif();

            const href = e.target.href;
            const post = href.split("post=")[1].split("&");

            $.post(ajaxurl, { action: 'resource_booking', data: post }, function (response) {
                if (response !== "true") xtec_booking_add_message();
                else window.location.href = href;
            });
        });

        $('#bulk-action-selector-top option[value="trash"], #bulk-action-selector-bottom option[value="trash"]').remove();
    }

    function handleResourceSelection() {
        if ($('input[id^="resource-"]').length === 0) return;

        $('input[id^="resource-"]').on("change", function () {
            let selected = [];

            $("body").css("cursor", "progress");
            $("#xtec_calendar_wait").removeClass('display_div_wait');

            $('input[id^="resource-"]').each(function () {
                $(this).prop('disabled', true);
                if ($(this).prop('checked')) {
                    selected.push($(this).attr('id').split('resource-')[1]);
                }
            });

            xtec_events(selected);
        });
    }

    /** ---------------- Bookings ---------------- */
    function handleBookings() {
        if (!currentUrl.includes("post_type=calendar_booking")) return;

        $('#bulk-action-selector-top option[value="trash"], #bulk-action-selector-bottom option[value="trash"]')
            .text(textSelectDelete);

        $('#posts-filter').on('submit', function (e) {
            const opt1 = $('#bulk-action-selector-top').val();
            const opt2 = $('#bulk-action-selector-bottom').val();
            if ((opt1 === 'trash' || opt2 === 'trash') && !confirm(confirmText)) e.preventDefault();
        });

        $('span.trash').on("click", function (e) {
            if (!confirm(confirmText)) e.preventDefault();
        });

        $('#bulk-action-selector-top option[value="edit"], #bulk-action-selector-bottom option[value="edit"]').remove();
    }

    /** ---------------- Deletion Confirmation ---------------- */
    function bindDeleteConfirmation() {
        if ($('#delete-action').length > 0 && $startDate.length > 0) {
            $('a[class*="submitdelete deletion"]').on('click', function (e) {
                if (!confirm(confirmTextInd)) e.preventDefault();
            });
        }
    }

    /** ---------------- Select/Unselect Resources ---------------- */
    function bindSelectionToggle() {
        $('#xtec_selection').on('click', function () {
            const $btn   = $(this);
            const action = $btn.attr('data-action');

            $btn.prop('disabled', true);

            const checkAll = (val) => $('[name^="resource-"]').prop('checked', val);

            if (action === 'unselect') {
                checkAll(false);
                $btn.attr('data-action', 'select').val(selectResources);
            } else {
                checkAll(true);
                $btn.attr('data-action', 'unselect').val(unselectResources);
            }

            $('[name^="resource-"]').first().trigger('change');
        });
    }

    /** ---------------- Init ---------------- */
    function init() {
        initPickers();
        bindDateEvents();
        bindTimeEvents();
        bindFormSubmit();
        handleResources();
        handleResourceSelection();
        handleBookings();
        bindDeleteConfirmation();
        bindSelectionToggle();

        // Calendar events
        const dataEvents = xtec_events();
        xtec_add_calendar(dataEvents);
    }

    init();
});