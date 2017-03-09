jQuery(document).ready(function($) {

    /* Linkback */
    jQuery('#spread-the-word').on('click', '#bp-media-add-linkback', function() {
        var data = {
            action: 'rtmedia_linkback',
            linkback: jQuery('#bp-media-add-linkback:checked').length
        };
        jQuery.post(rtmedia_admin_ajax, data, function(response) {
        });
    })

    /* Fetch Feed */
//    var rtmedia_news_section = jQuery('#latest-news');
//    if (rtmedia_news_section.length > 0) {
//        var data = {
//            action: 'rtmedia_fetch_feed'
//        };
//        jQuery.post(rtmedia_admin_ajax, data, function(response) {
//            rtmedia_news_section.find('.inside').html(response);
//        });
//    }

    /* Select Request */
    jQuery('#bp-media-settings-boxes').on('change', '#select-request', function() {
        if (jQuery(this).val()) {
            jQuery('#bp_media_settings_form .bp-media-metabox-holder').html()
            jQuery('#bp_media_settings_form .bp-media-metabox-holder').html('<div class="support_form_loader"></div>');
            var data = {
                action: 'rtmedia_select_request',
                form: jQuery(this).val()
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#bp_media_settings_form .bp-media-metabox-holder').html()
                jQuery('#bp_media_settings_form .bp-media-metabox-holder').html(response).fadeIn('slow');
            });
        }
    });

    /* Cancel Request */
    jQuery('#bp-media-settings-boxes').on('click', '#cancel-request', function() {
        if (jQuery(this).val()) {
            jQuery('#bp_media_settings_form .bp-media-metabox-holder').html()
            jQuery('#bp_media_settings_form .bp-media-metabox-holder').html('<div class="support_form_loader"></div>');
            var data = {
                action: 'rtmedia_cancel_request'
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#bp_media_settings_form .bp-media-metabox-holder').html()
                jQuery('#bp_media_settings_form .bp-media-metabox-holder').html(response).fadeIn('slow');
            });
        }
    });

    /* Submit Request */

    jQuery('#bp-media-settings-boxes').on('submit', '#bp_media_settings_form,#rtmedia-settings-submit', function(e) {
        var return_code = true;
        var reg = new RegExp('^auto$|^[+-]?[0-9]+\\.?([0-9]+)?(px|em|ex|%|in|cm|mm|pt|pc)?$');
        jQuery("input[name*='defaultSizes']").each(function(el) {
            if (!reg.test(jQuery(this).val())) {
                alert("Invalid value for " + jQuery(this).attr('name').replace('rtmedia-options[', '').replace(']', '').replace(/_/g, ' '));
                return_code = false;
                return false;
            }

        });
	var general_videothumb = jQuery('input[name^="rtmedia-options[general_videothumbs]"]');
	if( return_code && typeof general_videothumb != "undefined" ) {
	    if( general_videothumb.val() <= 0 ) {
		alert("Number of video thumbnails to be generated should be greater than 0 in image sizes settings. ");
		return_code = false;
                return false;
	    }
	}
        if (!return_code) {
            e.preventDefault();
        }

    });

    jQuery(document).on('click', "#bpm-services .encoding-try-now,#rtm-services .encoding-try-now", function(e) {
        e.preventDefault();
        if (confirm(rtmedia_admin_strings.are_you_sure)) {
            jQuery(this).after('<img style="margin: 0 0 0 10px" src="' + rtmedia_admin_url + 'images/wpspin_light.gif" />')
            var data = {
                action: 'rtmedia_free_encoding_subscribe'
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.getJSON(ajaxurl, data, function(response) {
                if (response.error === undefined && response.apikey) {
                    var tempUrl = window.location.href;
                    var hash = window.location.hash;
                    tempUrl = tempUrl.replace(hash, '');
                    document.location.href = tempUrl + '&apikey=' + response.apikey + hash;
                } else {
                    jQuery('.encoding-try-now').next().remove();
                    jQuery('#settings-error-encoding-error').remove();
                    jQuery('#bp-media-settings-boxes').before('<div class="error" id="settings-error-encoding-error"><p>' + response.error + '</p></div>');
                }
            });
        }
    });

    jQuery(document).on('click', '#api-key-submit', function(e) {
        e.preventDefault();
        jQuery(this).after('<img style="margin: 0 0 0 10px" src="' + rtmedia_admin_url + 'images/wpspin_light.gif" />')
        var data = {
            action: 'rtmedia_enter_api_key',
            apikey: jQuery('#new-api-key').val()
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.getJSON(ajaxurl, data, function(response) {
            if (response.error === undefined && response.apikey) {
                var tempUrl = window.location.href;
                var hash = window.location.hash;
                tempUrl = tempUrl.replace(hash, '');
                if (tempUrl.toString().indexOf('&apikey=' + response.apikey) == -1)
                    tempUrl += '&apikey=' + response.apikey;
                if (tempUrl.toString().indexOf('&update=true') == -1)
                    tempUrl += '&update=true';
                document.location.href = tempUrl + hash;

            } else {
                jQuery('#settings-error-api-key-error').remove();
                jQuery('h2:first').after('<div class="error" id="settings-error-api-key-error"><p>' + response.error + '</p></div>');
            }
        });
    });

    jQuery(document).on('click', '#disable-encoding', function(e) {
        e.preventDefault();
        if (confirm(rtmedia_admin_strings.disable_encoding)) {
            jQuery(this).after('<img style="margin: 0 0 0 10px" src="' + rtmedia_admin_url + 'images/wpspin_light.gif" />')
            var data = {
                action: 'rtmedia_disable_encoding'
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                if (response) {
                    jQuery('settings-error-encoding-disabled').remove();
                    jQuery('h2:first').after('<div class="updated" id="settings-encoding-successfully-disabled"><p>' + response + '</p></div>');
                    jQuery('#bp-media-encoding-usage').remove();
                    jQuery('#disable-encoding').next().remove();
                    jQuery('#disable-encoding').remove();
                    jQuery('#new-api-key').val('');
                } else {
                    jQuery('#settings-error-encoding-disabled').remove();
                    jQuery('h2:first').after('<div class="error" id="settings-error-encoding-disabled"><p>' + rtmedia_admin_strings.something_went_wrong + '</p></div>');
                }
            });
        }
    });

    jQuery('.bp-media-encoding-table').on('click', '.bpm-unsubscribe', function(e) {
        e.preventDefault();
        //        var note=prompt(bp_media_admin_strings.reason_for_unsubscribe);
        jQuery("#bpm-unsubscribe-dialog").dialog({
            dialogClass: "wp-dialog",
            modal: true,
            buttons: {
                Unsubscribe: function() {
                    jQuery(this).dialog("close");
                    jQuery('.bpm-unsubscribe').after('<img style="margin: 0 0 0 10px" src="' + rtmedia_admin_url + 'images/wpspin_light.gif" />')
                    var data = {
                        action: 'rtmedia_unsubscribe_encoding_service',
                        note: jQuery('#bpm-unsubscribe-note').val(),
                        plan: jQuery('.bpm-unsubscribe').attr('data-plan'),
                        price: jQuery('.bpm-unsubscribe').attr('data-price')
                    };

                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.getJSON(ajaxurl, data, function(response) {
                        if (response.error === undefined && response.updated) {
                            jQuery('.bpm-unsubscribe').next().remove();
                            jQuery('.bpm-unsubscribe').after(response.form);
                            jQuery('.bpm-unsubscribe').remove();
                            jQuery('#settings-unsubscribed-successfully').remove();
                            jQuery('#settings-unsubscribe-error').remove();
                            jQuery('h2:first').after('<div class="updated" id="settings-unsubscribed-successfully"><p>' + response.updated + '</p></div>');
                            window.location.hash = '#settings-unsubscribed-successfully';
                        } else {
                            jQuery('.bpm-unsubscribe').next().remove();
                            jQuery('#settings-unsubscribed-successfully').remove();
                            jQuery('#settings-unsubscribe-error').remove();
                            jQuery('h2:first').after('<div class="error" id="settings-unsubscribe-error"><p>' + response.error + '</p></div>');
                            window.location.hash = '#settings-unsubscribe-error';
                        }
                    });
                }
            }
        });

    });

    function fireRequest(data) {
        return jQuery.post(ajaxurl, data, function(response) {
            if (response != 0) {
                var redirect = false;
                var progw = Math.ceil((((parseInt(response) * 20) + parseInt(data.values['finished'])) / parseInt(data.values['total'])) * 100);
                if (progw > 100) {
                    progw = 100;
                    redirect = true
                }
                ;
                jQuery('#rtprogressbar>div').css('width', progw + '%');
                finished = jQuery('#rtprivacyinstaller span.finished').html();
                jQuery('#rtprivacyinstaller span.finished').html(parseInt(finished) + data.count);
                if (redirect) {
                    jQuery.post(ajaxurl, {
                        action: 'rtmedia_privacy_redirect'
                    }, function(response) {
                        window.location = settings_url;
                    });
                }
            } else {
                jQuery('#map_progress_msgs').html('<div class="map_mapping_failure">Row ' + response + ' failed.</div>');
            }
        });
    }

    jQuery('#bpmedia-bpalbumimporter').on('change', '#bp-album-import-accept', function() {
        jQuery('.bp-album-import-accept').toggleClass('i-accept');
        jQuery('.bp-album-importer-wizard').slideToggle();
    });

    jQuery('#rtprivacyinstall').click(function(e) {
        e.preventDefault();
        $progress_parent = jQuery('#rtprivacyinstaller');
        $progress_parent.find('.rtprivacytype').each(function() {
            $type = jQuery(this).attr('id');
            if ($type == 'total') {
                $values = [];
                jQuery(this).find('input').each(function() {

                    $values [jQuery(this).attr('name')] = [jQuery(this).val()];

                });
                $data = {};
                for (var i = 1; i <= $values['steps'][0]; i++) {
                    $count = 20;
                    if (i == $values['steps'][0]) {
                        $count = parseInt($values['laststep'][0]);
                        if ($count == 0) {
                            $count = 20
                        }
                        ;
                    }
                    newvals = {
                        'page': i,
                        'action': 'rtmedia_privacy_install',
                        'count': $count,
                        'values': $values
                    }
                    $data[i] = newvals;
                }
                var $startingpoint = jQuery.Deferred();
                $startingpoint.resolve();
                jQuery.each($data, function(i, v) {
                    $startingpoint = $startingpoint.pipe(function() {
                        return fireRequest(v);
                    });
                });


            }
        });
    });

    function fireimportRequest(data) {
        return jQuery.getJSON(ajaxurl, data, function(response) {
            favorites = false;
            if (response) {
                var redirect = false;
                var media_progw = Math.ceil((((parseInt(response.page) * 5) + parseInt(data.values['finished'])) / parseInt(data.values['total'])) * 100);
                comments_total = jQuery('#bpmedia-bpalbumimporter .bp-album-comments span.total').html();
                users_total = jQuery('#bpmedia-bpalbumimporter .bp-album-users span.total').html();
                media_total = jQuery('#bpmedia-bpalbumimporter .bp-album-media span.total').html();
                comments_finished = jQuery('#bpmedia-bpalbumimporter .bp-album-comments span.finished').html();
                users_finished = jQuery('#bpmedia-bpalbumimporter .bp-album-users span.finished').html();
                var comments_progw = Math.ceil((((parseInt(response.comments)) + parseInt(comments_finished)) / parseInt(comments_total)) * 100);
                var users_progw = Math.ceil((parseInt(response.users) / parseInt(users_total)) * 100);
                if (media_progw > 100 || media_progw == 100) {
                    media_progw = 100;
                    favorites = true
                }
                ;
                jQuery('.bp-album-media #rtprogressbar>div').css('width', media_progw + '%');
                jQuery('.bp-album-comments #rtprogressbar>div').css('width', comments_progw + '%');
                jQuery('.bp-album-users #rtprogressbar>div').css('width', users_progw + '%');
                media_finished = jQuery('#bpmedia-bpalbumimporter .bp-album-media span.finished').html();
                if (parseInt(media_finished) < parseInt(media_total))
                    jQuery('#bpmedia-bpalbumimporter .bp-album-media span.finished').html(parseInt(media_finished) + data.count);
                jQuery('#bpmedia-bpalbumimporter .bp-album-comments span.finished').html(parseInt(response.comments) + parseInt(comments_finished));
                jQuery('#bpmedia-bpalbumimporter .bp-album-users span.finished').html(parseInt(response.users));
                if (favorites) {
                    favorite_data = {
                        'action': 'rtmedia_rt_album_import_favorites'
                    }
                    jQuery.post(ajaxurl, favorite_data, function(response) {
                        if (response.favorites !== 0 || response.favorites !== '0') {
                            if (!jQuery('.bp-album-favorites').length)
                                jQuery('.bp-album-comments').after('<br /><div class="bp-album-favorites"><strong>User\'s Favorites: <span class="finished">0</span> / <span class="total">' + response.users + '</span></strong><div id="rtprogressbar"><div style="width:0%"></div></div></div>');
                            $favorites = {};
                            if (response.offset != 0 || response.offset != '0')
                                start = response.offset * 1 + 1;
                            else
                                start = 1
                            for (var i = start; i <= response.users; i++) {
                                $count = 1;
                                if (i == response.users) {
                                    $count = parseInt(response.users % $count);
                                    if ($count == 0) {
                                        $count = 1;
                                    }
                                }

                                newvals = {
                                    'action': 'rtmedia_rt_album_import_step_favorites',
                                    'offset': (i - 1) * 1,
                                    'redirect': i == response.users
                                }
                                $favorites[i] = newvals;
                            }
                            var $startingpoint = jQuery.Deferred();
                            $startingpoint.resolve();
                            jQuery.each($favorites, function(i, v) {
                                $startingpoint = $startingpoint.pipe(function() {
                                    return fireimportfavoriteRequest(v);
                                });
                            });

                        } else {
                            window.setTimeout(reload_url, 2000);
                        }
                    }, 'json');
                }
            } else {
                jQuery('#map_progress_msgs').html('<div class="map_mapping_failure">Row ' + response.page + ' failed.</div>');
            }
        });
    }

    function fireimportfavoriteRequest(data) {
        return jQuery.post(ajaxurl, data, function(response) {
            redirect = false;
            favorites_total = jQuery('#bpmedia-bpalbumimporter .bp-album-favorites span.total').html();
            favorites_finished = jQuery('#bpmedia-bpalbumimporter .bp-album-favorites span.finished').html();
            jQuery('#bpmedia-bpalbumimporter .bp-album-favorites span.finished').html(parseInt(favorites_finished) + 1);
            var favorites_progw = Math.ceil((parseInt(favorites_finished + 1) / parseInt(favorites_total)) * 100);
            if (favorites_progw > 100 || favorites_progw == 100) {
                favorites_progw = 100;
                redirect = true;
            }
            jQuery('.bp-album-favorites #rtprogressbar>div').css('width', favorites_progw + '%');
            if (redirect) {
                window.setTimeout(reload_url, 2000);
            }
        });
    }

    function reload_url() {
        window.location = document.URL;
    }

    jQuery('#bpmedia-bpalbumimport-cleanup').click(function(e) {
        e.preventDefault();
        jQuery.post(ajaxurl, {
            action: 'rtmedia_rt_album_cleanup'
        }, function(response) {
            window.location = settings_rt_album_import_url;
        });

    });

    jQuery('#bpmedia-bpalbumimporter').on('click', '#bpmedia-bpalbumimport', function(e) {
        e.preventDefault();
        if (!jQuery('#bp-album-import-accept').prop('checked')) {
            jQuery('html, body').animate({
                scrollTop: jQuery('#bp-album-import-accept').offset().top
            }, 500);
            var $el = jQuery('.bp-album-import-accept'),
                    x = 500,
                    originalColor = '#FFEBE8',
                    i = 3; //counter

            (function loop() { //recurisve IIFE
                $el.css("background-color", "#EE0000");
                setTimeout(function() {
                    $el.css("background-color", originalColor);
                    if (--i)
                        setTimeout(loop, x); //restart loop
                }, x);
            }());
            return;
        } else {
            jQuery(this).prop('disabled', true);
        }
        wp_admin_url = ajaxurl.replace('admin-ajax.php', '');
        if (!jQuery('.bpm-ajax-loader').length)
            jQuery(this).after(' <img class="bpm-ajax-loader" src="' + wp_admin_url + 'images/wpspin_light.gif" /> <strong>' + rtmedia_admin_strings.no_refresh + '</strong>');


        $progress_parent = jQuery('#bpmedia-bpalbumimport');
        $values = [];
        jQuery(this).parent().find('input').each(function() {
            $values [jQuery(this).attr('name')] = [jQuery(this).val()];

        });

        if ($values['steps'][0] == 0)
            $values['steps'][0] = 1;

        $data = {};
        for (var i = 1; i <= $values['steps'][0]; i++) {
            $count = 5;
            if (i == $values['steps'][0]) {
                $count = parseInt($values['laststep'][0]);
                if ($count == 0) {
                    $count = 5
                }
                ;
            }
            newvals = {
                'page': i,
                'action': 'rtmedia_rt_album_import',
                'count': $count,
                'values': $values
            }
            $data[i] = newvals;
        }
        var $startingpoint = jQuery.Deferred();
        $startingpoint.resolve();
        jQuery.each($data, function(i, v) {
            $startingpoint = $startingpoint.pipe(function() {
                return fireimportRequest(v);
            });
        });


    });

    jQuery('#bp-media-settings-boxes').on('click', '.interested', function() {
        jQuery('.interested-container').removeClass('hidden');
        jQuery('.choice-free').attr('required', 'required');
    });
    jQuery('#bp-media-settings-boxes').on('click', '.not-interested', function() {
        jQuery('.interested-container').addClass('hidden');
        jQuery('.choice-free').removeAttr('required');
    });

    jQuery('#video-transcoding-main-container').on('click', '.video-transcoding-survey', function(e) {
        e.preventDefault();
        var data = {
            action: 'rtmedia_convert_videos_form',
            email: jQuery('.email').val(),
            url: jQuery('.url').val(),
            choice: jQuery('input[name="choice"]:checked').val(),
            interested: jQuery('input[name="interested"]:checked').val()
        }
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#video-transcoding-main-container').html('<p><strong>' + response + '</strong></p>');
        });
        return false;
    });

    jQuery('#bpmedia-bpalbumimporter').on('click', '.deactivate-bp-album', function(e) {
        e.preventDefault();
        $bpalbum = jQuery(this);
        var data = {
            action: 'rtmedia_rt_album_deactivate'
        }
        jQuery.get(ajaxurl, data, function(response) {
            if (response)
                location.reload();
            else
                $bpalbum.parent().after('<p>' + rtmedia_admin_strings.something_went_wrong + '</p>');
        });
    });

    jQuery('.updated').on('click', '.bpm-hide-encoding-notice', function() {
        jQuery(this).after('<img style="margin: 0 0 0 10px" src="' + rtmedia_admin_url + 'images/wpspin_light.gif" />');
        var data = {
            action: 'rtmedia_hide_encoding_notice'
        }
        jQuery.post(ajaxurl, data, function(response) {
            if (response) {
                jQuery('.bpm-hide-encoding-notice').closest('.updated').remove();
            }
        });
    });


    if (jQuery('#rtmedia-privacy-enable').is(":checked")) {
        jQuery(".privacy-driven-disable label input").prop("disabled", false);
        jQuery(".privacy-driven-disable label .rt-switch").bootstrapSwitch("setActive", true);
    } else {
        jQuery(".privacy-driven-disable label input").prop("disabled", true);
        jQuery(".privacy-driven-disable label .rt-switch").bootstrapSwitch("setActive", false);
        jQuery(".privacy-driven-disable").parent().parent().css("display", "none");
    }

    if (jQuery('#rtmedia-bp-enable-activity').is(":checked")) {
        jQuery(".rtmedia-bp-activity-setting").prop("disabled", false);
        jQuery(".privacy-driven-disable label .rt-switch").bootstrapSwitch("setActive", true);
    } else {
	   jQuery(".rtmedia-bp-activity-setting").prop("disabled", true);
       jQuery(".privacy-driven-disable label .rt-switch").bootstrapSwitch("setActive", false);
    }

    jQuery('#rtmedia-privacy-enable').on("click", function(e) {
        if (jQuery(this).is(":checked")) {
            jQuery(".privacy-driven-disable label input").prop("disabled", false);
            jQuery(".privacy-driven-disable label .rt-switch").bootstrapSwitch("setActive", true);
            jQuery(".privacy-driven-disable").parent().parent().css("display", "block");
        } else {
            jQuery(".privacy-driven-disable label input").prop("disabled", true);
            jQuery(".privacy-driven-disable label .rt-switch").bootstrapSwitch("setActive", false);
            jQuery(".privacy-driven-disable").parent().parent().css("display", "none");
        }
    });
    jQuery('#rtmedia-bp-enable-activity').on("click", function(e){
	if (jQuery(this).is(":checked")) {
	    jQuery(".rtmedia-bp-activity-setting").prop("disabled", false);
	} else {
	    jQuery(".rtmedia-bp-activity-setting").prop("disabled", true);
	}
    });
    var onData = '';
    var offData = '';
    if (rtmedia_on_label !== undefined)
        onData = 'data-on-label="' + rtmedia_on_label + '"';
    if (rtmedia_off_label !== undefined)
        offData = 'data-off-label="' + rtmedia_off_label + '"';
    jQuery("[data-toggle='switch']").wrap('<div class="rt-switch" ' + onData + ' ' + offData + ' />').parent().bootstrapSwitch();

    $(".rtmedia-tab-title").click(function() {
        hash = $(this).attr('href');
        window.location.hash = hash.substring(1, hash.length);
    });
    function manageHash() {

        hash = window.location.hash;
        $('#tab-' + hash.substr(1, hash.length)).click();
        if ($('#tab-' + hash.substr(1, hash.length)).length < 1)
            return 1;
        return $('#tab-' + hash.substr(1, hash.length)).parent().index() + 1;
    }

    function rtmediaGetParameterByName(name) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    jQuery('#rtmedia-submit-request').click(function(){
	var flag = true;
	var name = jQuery('#name').val();
	var email = jQuery('#email').val();
	var website = jQuery('#website').val();
	var phone = jQuery('#phone').val();
	var subject = jQuery('#subject').val();
	var details = jQuery('#details').val();
	var request_type = jQuery('input[name="request_type"]').val();
	var request_id = jQuery('input[name="request_id"]').val();
	var server_address = jQuery('input[name="server_address"]').val();
	var ip_address = jQuery('input[name="ip_address"]').val();
	var server_type = jQuery('input[name="server_type"]').val();
	var user_agent = jQuery('input[name="user_agent"]').val();
	var form_data = { name : name, email : email, website : website, phone : phone, subject : subject, details : details, request_id : request_id, request_type: 'premium_support', server_address : server_address, ip_address : ip_address, server_type : server_type, user_agent : user_agent};
	if(request_type == "bug_report") {
	    var wp_admin_username = jQuery('#wp_admin_username').val();
	    if(wp_admin_username == "") {
		alert("Please enter WP Admin Login.");
		return false;
	    }
	    var wp_admin_pwd = jQuery('#wp_admin_pwd').val();
	    if(wp_admin_pwd == "") {
		alert("Please enter WP Admin password.");
		return false;
	    }
	    var ssh_ftp_host = jQuery('#ssh_ftp_host').val();
	    if(ssh_ftp_host == "") {
		alert("Please enter SSH / FTP host.");
		return false;
	    }
	    var ssh_ftp_username = jQuery('#ssh_ftp_username').val();
	    if(ssh_ftp_username == "") {
		alert("Please enter SSH / FTP login.");
		return false;
	    }
	    var ssh_ftp_pwd = jQuery('#ssh_ftp_pwd').val();
	    if(ssh_ftp_pwd == "") {
		alert("Please enter SSH / FTP password.");
		return false;
	    }
	    form_data = { name : name, email : email, website : website, phone : phone, subject : subject, details : details, request_id : request_id, request_type: 'premium_support', server_address : server_address, ip_address : ip_address, server_type : server_type, user_agent : user_agent, wp_admin_username : wp_admin_username, wp_admin_pwd : wp_admin_pwd, ssh_ftp_host : ssh_ftp_host, ssh_ftp_username : ssh_ftp_username, ssh_ftp_pwd : ssh_ftp_pwd };
	}
	for(formdata in form_data) {
	    if(form_data[formdata] == "" && formdata != 'phone'  ) {
		alert("Please enter " + formdata.replace("_", " ") + " field.");
		return false;
	    }
	}
	data = {
		action: "rtmedia_submit_request",
		form_data: form_data
	    };
	jQuery.post(ajaxurl,data,function(data){
	    data = data.trim();
	    if(data == "false") {
		alert("Please fill all the fields.");
		return false;
	    }
	    $('#rtmedia_service_contact_container').empty();
	    $('#rtmedia_service_contact_container').append(data);
	});
	return false;
    });

    jQuery('#cancel-request').click(function(){
	return false;
    });

    $(window).hashchange(function(e, data) {
        e.preventDefault();
        manageHash();
    });
    if(jQuery(document).foundation !== undefined)
        jQuery(document).foundation();

    if(window.location.hash){
	jQuery('#bp-media-settings-boxes dl.tabs dd a').each(function(){
	    var hash = '#' + jQuery(this).attr('href').split('#')[1];
	    if(hash == window.location.hash){
		jQuery(this).click();
	    }
	});
    }
});

function rtmedia_addon_do_not_show() {
    var data = {
	action: 'rtmedia_addon_popup_not_show_again'
    };
    jQuery.post(rtmedia_admin_ajax, data, function(response) {
	jQuery('#TB_window').remove();
	jQuery('#TB_overlay').remove();
    });
}

jQuery(window).load(function(){
    jQuery('.rtmedia-addon-thickbox').trigger('click');
});