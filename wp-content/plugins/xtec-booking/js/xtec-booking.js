function not_acces_day_template(booking) {

    const currentUrl = window.location.href;

    if (booking == 'booking' || currentUrl.search('post-new.php') > 0) {

        jQuery('a[data-toggle^="tooltip"]').removeAttr('data-original-title');
        jQuery('*[data-cal-date]').unbind('click');
        jQuery('.cal-cell').unbind('dblclick');

        xtec_booking_name_days();
    }
}

function xtec_booking_name_days() {

    if (jQuery('div[class^="cal-cell1"]').length > 0) {
        let i = 0;
        jQuery('div[class^="cal-cell1"]').each(function () {
            if (i < 7) {
                jQuery(this).text(days[i]);
                i = i + 1;
            } else {
                return false;
            }
        });
    }
}

function xtec_booking_reset_modal() {
    jQuery('#modalTitle').html('<p><img id="xtec-booking-wait" src="../wp-admin/images/loading.gif"></p>');
    jQuery('#modalResource').text('');
    jQuery('#modalStartHour').text('');
    jQuery('#modalEndHour').text('');
    jQuery('#modalContent').html('');
    jQuery('#modalBy').text('');
}

function get_booking_content(events) {

    jQuery('a[data-event-id="' + events.id + '"]').tooltip('hide');

    jQuery.post(
        ajaxurl,
        {
            'action': 'get_event_modal',
            'data': events.id,
        })
        .done(function (response) {

            let request = JSON.parse(response);

            jQuery('#modalTitle').text(' ' + request.title);
            jQuery('#modalResource').text(' ' + request.resource);
            jQuery('#modalStartHour').text(' ' + request.startHour);
            jQuery('#modalEndHour').text(' ' + request.endHour);
            jQuery('#modalContent').html(' ' + request.content);
            jQuery('#modalBy').text(' ' + request.by);

        });

}

function xtec_add_calendar(data, booking = false) {
    const $calendar = jQuery('#xtec_calendar');

    if (!$calendar.length) {
        return;
    }

    // 1. Simplificació de l'idioma
    let lang_locale = (typeof locale !== 'undefined' && locale.locale) ? locale.locale.replace('_', '-') : 'ca-ES';
    if (!lang_locale.includes('-')) {
        lang_locale += '-ES';
    }

    const d = new Date();
    const dateNow = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

    const options = {
        first_day: 1,
        events_source: data,
        view: 'month',
        tmpl_path: tmplsCalendar,
        tmpl_cache: false,
        day: dateNow,
        language: lang_locale,
        modal: '#events-modal',
        modal_title: (events) => get_booking_content(events),
        onAfterEventsLoad: function (events) {
            if (!events) {
                return;
            }
            const listHtml = events.map(val => `<li><a href="${val.url}">${val.title}</a></li>`).join('');
            jQuery('#eventlist').html(listHtml);
        },
        onAfterViewLoad: function (view) {
            jQuery('.page-header h3').text(this.getTitle());
            jQuery('.btn-group button').removeClass('active');
            jQuery(`button[data-calendar-view="${view}"]`).addClass('active');
        },
        onAfterModalHidden: () => xtec_booking_reset_modal(),
        classes: { months: { general: 'label' } }
    };

    const calendar = $calendar.calendar(options);

    jQuery('input[id^="resource-"]').prop('disabled', false); // .prop() és millor que .removeAttr()
    jQuery('body').css('cursor', 'default');
    jQuery('#xtec_calendar_wait').addClass('display_div_wait');

    jQuery('.btn-group button[data-calendar-nav]').on('click', function () {
        calendar.navigate(jQuery(this).data('calendar-nav'));
        not_acces_day_template(booking);
    });

    jQuery('.btn-group button[data-calendar-view]').on('click', function () {
        calendar.view(jQuery(this).data('calendar-view'));
        not_acces_day_template(booking);
    });

    jQuery('#first_day').on('change', function () {
        const val = jQuery(this).val();
        calendar.setOptions({ first_day: val ? parseInt(val, 10) : null });
        calendar.view();
    });

    jQuery('#language').on('change', function () {
        calendar.setLanguage(jQuery(this).val());
        calendar.view();
    });

    jQuery('#events-in-modal').on('change', function () {
        const $this = jQuery(this);
        calendar.setOptions({ modal: $this.is(':checked') ? $this.val() : null });
    });

    jQuery('#format-12-hours, #show_wbn, #show_wb').on('change', function () {
        const $this = jQuery(this);
        const isChecked = $this.is(':checked');
        const id = $this.attr('id');

        if (id === 'format-12-hours') {
            calendar.setOptions({format12: isChecked});
        }
        else if (id === 'show_wbn') {
            calendar.setOptions({display_week_numbers: isChecked});
        }
        else if (id === 'show_wb') {
            calendar.setOptions({weekbox: isChecked});
        }

        calendar.view();
    });

    not_acces_day_template(booking);
}

function xtec_change_button() {
    let option = false;
    jQuery('input[id^="resource-"]').each(function () {
        let select = jQuery(this).attr('checked');
        if (select) {
            option = true;
            return false;
        }
    });

    if (!option) {
        jQuery('#xtec_selection').attr('data-action', 'select');
        jQuery('#xtec_selection').attr('value', selectResources);
    } else {
        jQuery('#xtec_selection').attr('data-action', 'unselect');
        jQuery('#xtec_selection').attr('value', unselectResources);
    }
}

function xtec_events(update = false, booking = false) {

    if (update !== false) {

        if (update == '') {
            update = 'no-events';
        }

        jQuery.post(
            ajaxurl,
            {
                'action': 'resource_selected',
                'data': update,
            })
            .done(function (response) {

                let request = JSON.parse(response);

                jQuery('#xtec_calendar').remove();
                if (booking == 'booking') {
                    jQuery('#xtec_calendar_pos').append('<div id="xtec_calendar" style="min-width:252px;"></div>');
                } else {
                    jQuery('#xtec_calendar_pos').append('<div id="xtec_calendar" style="margin-left:50px;max-width:92%;"></div>');
                }

                if (booking == 'booking') {
                    xtec_add_calendar(request, 'booking');
                } else {
                    xtec_add_calendar(request);
                }

                jQuery('#xtec_selection').removeAttr('disabled', true);

                xtec_change_button();
            })
            .fail(function (response) {
                jQuery('input[id^="resource-"]').each(function () {
                    jQuery(this).removeAttr('disabled', true);
                });

                xtec_change_button();

                jQuery('#xtec_selection').removeAttr('disabled', true);
                jQuery("body").css("cursor", "default");
                jQuery("#xtec_calendar_wait").addClass('display_div_wait');
            });

    } else {

        if (typeof events !== 'undefined') {
            return events;
        }

    }
}

function checkDateBefore() {
    const ara = new Date();
    // Data actual a les 00:00:00 per comparar només els dies
    const avui = new Date(ara.getFullYear(), ara.getMonth(), ara.getDate());

    const finishTimeStr = jQuery('#xtec-booking-finish-time').val();

    // 1. Funció auxiliar per convertir els strings en objectes Date
    const parseDateTime = (dateStr, timeStr) => {
        if (!dateStr) {
            return null;
        }

        // Suporta formats separats per guió o barra (ex: 26-02-2026 o 26/02/2026)
        const [d, m, y] = dateStr.split(/[-/]/).map(Number);

        if (timeStr) {
            const [h, min] = timeStr.split(':').map(Number);
            return new Date(y, m - 1, d, h, min, 0);
        }
        return new Date(y, m - 1, d, 0, 0, 0);
    };

    // 2. Funció auxiliar per validar un camp i aplicar-li els estils
    const validateField = (dateStr, msgSelector, inputSelector) => {
        if (!dateStr) {
            return;
        }

        const dateToCompare = parseDateTime(dateStr, finishTimeStr);
        // Si tenim hora, comparem amb el moment exacte. Si no, comparem amb les 00:00 d'avui.
        const referenceTime = finishTimeStr ? ara : avui;

        // És anterior a la data/hora actual?
        const isPast = referenceTime > dateToCompare;

        // Utilitzem toggleClass(classe, booleà) per evitar fer if/else
        jQuery(msgSelector).toggleClass('display_message_date', !isPast);
        jQuery(inputSelector).toggleClass('border_info_date', isPast);
    };

    validateField(
        jQuery('#xtec-booking-start-date').val(),
        '#xtec-booking-before-date',
        '#xtec-booking-start-date'
    );

    validateField(
        jQuery('#xtec-booking-finish-date').val(),
        '#xtec-booking-before-date-finish',
        '#xtec-booking-finish-date'
    );
}

function xtec_booking_check_dates(StartDate, FinishDate) {

    let yearStart = parseInt(StartDate.substring(6, 10));
    let yearFinish = parseInt(FinishDate.substring(6, 10));
    let monthStart = parseInt(StartDate.substring(3, 5));
    let monthFinish = parseInt(FinishDate.substring(3, 5));
    let dayStart = parseInt(StartDate.substring(0, 2));
    let dayFinish = parseInt(FinishDate.substring(0, 2));

    let mkStart = new Date(yearStart, monthStart, dayStart, 0, 0, 0);
    let mkEnd = new Date(yearFinish, monthFinish, dayFinish, 0, 0, 0);

    let DaySelected = new Date(yearStart, monthStart - 1, dayStart, 0, 0, 0);

    checkDateBefore();

    if (mkStart > mkEnd) {
        jQuery('#xtec-booking-finish-date').val(StartDate);
        xtec_booking_check_time();
    } else {
        xtec_booking_check_time();
        if (yearFinish > yearStart || monthFinish > monthStart || dayFinish > dayStart) {
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

function xtec_booking_addDay() {
    let StartDate = jQuery('#xtec-booking-start-date').val();
    let FinishDate = jQuery('#xtec-booking-finish-date').val();

    if (StartDate == FinishDate) {
        let yearFinish = parseInt(FinishDate.substring(6, 10));
        let monthFinish = parseInt(FinishDate.substring(3, 5));
        let dayFinish = parseInt(FinishDate.substring(0, 2));
        let NewData = new Date(yearFinish, monthFinish, dayFinish + 1, 0, 0, 0);
        if (NewData.getMonth() < 10) {
            if (NewData.getDate() < 10) {
                FinishDate = '0' + NewData.getDate() + '-0' + NewData.getMonth() + '-' + NewData.getFullYear();
            } else {
                FinishDate = NewData.getDate() + '-0' + NewData.getMonth() + '-' + NewData.getFullYear();
            }
        } else {
            if (NewData.getDate() < 10) {
                FinishDate = '0' + NewData.getDate() + '-' + NewData.getMonth() + '-' + NewData.getFullYear();
            } else {
                FinishDate = NewData.getDate() + '-' + NewData.getMonth() + '-' + NewData.getFullYear();
            }
        }
        jQuery('#xtec-booking-finish-date').val(FinishDate);
        jQuery('#xtec-booking-row-days').removeClass('xtec-booking-display-none');
        jQuery('#text_days').removeClass('xtec-booking-display-none');
    }
}

function xtec_booking_check_time() {
    const startTime = jQuery('#xtec-booking-start-time').val();
    const $finishInput = jQuery('#xtec-booking-finish-time');
    const finishTime = $finishInput.val();

    if (!startTime || !startTime.includes(':')) {
        return;
    }

    // Extreure i convertir a números.
    const [hStart, mStart] = startTime.split(':').map(Number);
    const [hFinish, mFinish] = finishTime ? finishTime.split(':').map(Number) : [NaN, NaN];

    if (isNaN(hStart) || isNaN(mStart)) {
        return;
    }

    // 2. Condició per saber si cal actualitzar l'hora de finalització
    const isFinishEmptyOrInvalid = !finishTime || isNaN(hFinish) || isNaN(mFinish);
    const isFinishBeforeOrEqualStart = hFinish < hStart || (hFinish === hStart && mFinish <= mStart);

    // Si tot està bé, no fem res. Només actuem si falta l'hora final o és il·lògica.
    if (isFinishEmptyOrInvalid || isFinishBeforeOrEqualStart) {

        let newHour = hStart + 1;

        if (newHour >= 24) {
            newHour = 0;
            if (typeof xtec_booking_addDay === 'function') {
                xtec_booking_addDay();
            }
        }

        // 3. Formatem la nova hora de manera nativa (ex: 9 -> "09")
        const formattedFinishTime = `${String(newHour).padStart(2, '0')}:${String(mStart).padStart(2, '0')}`;

        // 4. Actualitzem el DOM
        $finishInput
            .prop('disabled', false)
            .val(formattedFinishTime)
            .focus();
    }
}

function add_wait_gif() {
    jQuery('#message').remove();
    jQuery('<p><img id="xtec-booking-wait" src="../wp-admin/images/loading.gif"></p>').insertAfter('.wrap h1');
}

function xtec_booking_add_message() {
    jQuery('#xtec-booking-wait').remove();
    let message = '<div id="message" class="notice notice-error"><p>' + message_resources + '</p></div>';
    jQuery(message).insertAfter('.wrap h1');
}

/* global jQuery, locale, textSelectDelete, confirmText, confirmTextInd, selectResources, unselectResources,
   dies_reserva, ajaxurl, xtec_booking_check_dates, xtec_booking_check_time, checkDateBefore, xtec_events,
   xtec_add_calendar, add_wait_gif, xtec_booking_add_message */

jQuery(document).ready(function () {
    const currentUrl = window.location.href;

    // 1. Inicialitzem tots els components i esdeveniments
    initDatetimepickers();
    setupDateEvents();
    setupTimeEvents();
    setupFormValidation();

    // 2. Lògica condicional segons el tipus de post (URL)
    if (currentUrl.includes('post_type=calendar_resources')) {
        setupResourceTrashEvents();
    }

    if (currentUrl.includes('post_type=calendar_booking')) {
        setupBookingTrashEvents();
    }

    // 3. Gestió de selecció de recursos (passem les variables globals per evitar errors)
    const txtSelect = typeof selectResources !== 'undefined' ? selectResources : 'Selecciona\'ls tots';
    const txtUnselect = typeof unselectResources !== 'undefined' ? unselectResources : 'Deselecciona\'ls tots';
    setupResourceSelection(txtSelect, txtUnselect);

    // 4. Càrrega inicial del calendari
    if (typeof xtec_events === 'function' && typeof xtec_add_calendar === 'function') {
        xtec_add_calendar(xtec_events());
    }
});

function initDatetimepickers() {
    if (jQuery('#xtec-booking-start-date').length === 0) {
        return;
    }

    jQuery.datetimepicker.setLocale('ca');
    const dateCfg = { timepicker: false, format: 'd-m-Y', dayOfWeekStart: 1 };
    const timeCfg = { datepicker: false, format: 'H:i', mask: true };

    jQuery('#xtec-booking-start-date, #xtec-booking-finish-date').datetimepicker(dateCfg);
    jQuery('#xtec-booking-start-time, #xtec-booking-finish-time').datetimepicker(timeCfg);
}

function setupDateEvents() {
    jQuery('#xtec-booking-start-date').on('change', function () {
        const start = jQuery(this).val();
        const $finish = jQuery('#xtec-booking-finish-date');

        if (!start) {
            $finish.val('').prop('disabled', true);
        } else {
            if (!$finish.val()) {
                $finish.val(start);
            }
            $finish.prop('disabled', false);
            if (typeof xtec_booking_check_dates === 'function') {
                xtec_booking_check_dates(start, $finish.val());
            }
        }
    });

    jQuery('#xtec-booking-finish-date').on('change', function () {
        if (typeof xtec_booking_check_dates === 'function') {
            xtec_booking_check_dates(jQuery('#xtec-booking-start-date').val(), jQuery(this).val());
        }
    });
}

function setupTimeEvents() {
    jQuery('#xtec-booking-start-time').on('blur', function () {
        const val = jQuery(this).val();
        if (!val || val.includes('_')) {
            jQuery(this).val('');
            jQuery('#xtec-booking-finish-time').val('').prop('disabled', true);
        } else {
            if (typeof xtec_booking_check_time === 'function') {
                xtec_booking_check_time();
            }
        }
    });

    jQuery('#xtec-booking-finish-time').on('blur', function () {
        if (typeof checkDateBefore === 'function') {
            checkDateBefore();
        }
        const isSameDay = jQuery('#xtec-booking-start-date').val() === jQuery('#xtec-booking-finish-date').val();
        if (isSameDay && typeof xtec_booking_check_time === 'function') {
            xtec_booking_check_time();
        }
    });
}

function setupFormValidation() {
    jQuery('#post').on('submit', function () {
        const isSameDay = jQuery('#xtec-booking-start-date').val() === jQuery('#xtec-booking-finish-date').val();
        if (isSameDay) {
            return true;
        }

        const anyDayChecked = jQuery('input[name^="_xtec-booking-day-"]:checked').length > 0;
        if (anyDayChecked) {
            jQuery('#error_message_days').addClass('xtec-booking-display-none');
            return true;
        }

        // Si no hi ha cap dia marcat i són dies diferents:
        jQuery('#error_message_days').removeClass('xtec-booking-display-none');
        alert(typeof dies_reserva !== 'undefined' ? dies_reserva : 'Selecciona un dia');
        jQuery('input[name^="_xtec-booking-day-"]').first().focus();
        jQuery('#publish').removeClass('disabled');
        jQuery('span.spinner').removeClass('is-active');

        return false;
    });
}

function setupResourceTrashEvents() {
    jQuery('body').on('click', 'span.trash a', function (e) {
        e.preventDefault();
        if (!confirm(typeof confirmTextInd !== 'undefined' ? confirmTextInd : 'N\'esteu segur?')) {
            return;
        }

        if (typeof add_wait_gif === 'function') {
            add_wait_gif();
        }

        const href = jQuery(this).attr('href');
        const postId = new URLSearchParams(href.split('?')[1]).get('post');

        jQuery.post(ajaxurl, { action: 'resource_booking', data: [postId] }, function (response) {
            if (response === 'true') {
                window.location.href = href;
            } else if (typeof xtec_booking_add_message === 'function') {
                xtec_booking_add_message();
            }
        });
    });

    jQuery('#bulk-action-selector-top, #bulk-action-selector-bottom').find('option[value="trash"]').remove();
}

function setupBookingTrashEvents() {
    const $bulkSelectors = jQuery('#bulk-action-selector-top, #bulk-action-selector-bottom');
    const txtDelete = typeof textSelectDelete !== 'undefined' ? textSelectDelete : 'Mou a la paperera';
    const txtConfirm = typeof confirmText !== 'undefined' ? confirmText : 'Esteu segur?';
    const txtConfirmInd = typeof confirmTextInd !== 'undefined' ? confirmTextInd : 'Esteu segur de voler esborrar això?';

    $bulkSelectors.find('option[value="trash"]').text(txtDelete);
    $bulkSelectors.find('option[value="edit"]').remove();

    jQuery('#posts-filter').on('submit', function (e) {
        const actionTop = jQuery('#bulk-action-selector-top').val();
        const actionBottom = jQuery('#bulk-action-selector-bottom').val();
        if ((actionTop === 'trash' || actionBottom === 'trash') && !confirm(txtConfirm)) {
            e.preventDefault();
        }
    });

    jQuery('span.trash').on('click', function (e) {
        if (!confirm(txtConfirm)) {
            e.preventDefault();
        }
    });

    if (jQuery('#delete-action').length > 0 && jQuery('#xtec-booking-start-date').length > 0) {
        jQuery('a.submitdelete.deletion').on('click', function (e) {
            if (!confirm(txtConfirmInd)) {
                e.preventDefault();
            }
        });
    }
}

function setupResourceSelection(txtSelect, txtUnselect) {
    // 1. Canvi individual de recurs
    jQuery('input[id^="resource-"]').on('change', function () {
        const selected = jQuery('input[id^="resource-"]:checked').map(function() {
            return this.id.replace('resource-', '');
        }).get();

        jQuery('body').css('cursor', 'progress');
        jQuery('#xtec_calendar_wait').removeClass('display_div_wait');
        jQuery('input[id^="resource-"]').prop('disabled', true);

        if (typeof xtec_events === 'function') {
            xtec_events(selected);
        }
    });

    // 2. Botó global de Seleccionar / Deseleccionar
    jQuery('#xtec_selection').on('click', function () {
        const $btn = jQuery(this);
        const isUnselect = $btn.data('action') === 'unselect';

        $btn.prop('disabled', true);
        jQuery('input[name^="resource-"]').prop('checked', !isUnselect);

        $btn.data('action', isUnselect ? 'select' : 'unselect')
            .val(isUnselect ? txtSelect : txtUnselect);

        // Només fem trigger del primer per no saturar de peticions AJAX el calendari
        jQuery('input[name^="resource-"]').first().trigger('change');
    });
}
