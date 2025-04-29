/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes.
 */
(function ($) {

    // Admin bar logo.
    wp.customize('astra_nodes_options[organism_logo]', function (value) {
        value.bind(function (code) {
            // astra_nodes is defined in customize.php.
            if (code === 'ceb') {
                var image = astra_nodes.logo_image_ceb;
                var url = astra_nodes.logo_url_ceb;
            } else {
                var image = astra_nodes.logo_image_department;
                var url = astra_nodes.logo_url_department;
            }
            $('#logo-educacio').attr('src', image);
            $('#wp-admin-bar-logo-educacio-wrapper a.ab-item').attr('href', url);
        });
    });

    // Header logo.
    wp.customize('astra_nodes_options[custom_logo]', function (value) {
        value.bind(function (url) {
            $('.site-logo-img a.custom-logo-link').html(
                '<img class="custom-logo" src="' + url + '" alt="" decoding="async">'
            );
        });
    });

    // Text preceding the blog name.
    wp.customize('astra_nodes_options[pre_blog_name]', function (value) {
        value.bind(function (text) {
            $('#client-type').html(text);
        });
    });

    // Blog name.
    wp.customize('blogname', function (value) {
        value.bind(function (text) {
            $('#blog-name').html(text);
        });
    });

    // Text following the blog name.
    wp.customize('blogdescription', function (value) {
        value.bind(function (text) {
            $('#blog-description').html(text);
        });
    });

    // Postal address.
    wp.customize('astra_nodes_options[postal_address]', function (value) {
        value.bind(function (text) {
            $('#postal-address').html(text);
        });
    });

    // Postal code and city.
    wp.customize('astra_nodes_options[postal_code_city]', function (value) {
        value.bind(function (text) {
            $('#postal-code-city').html(text);
        });
    });

    // Map.
    wp.customize('astra_nodes_options[link_to_map]', function (value) {
        value.bind(function (url) {
            $('#contact-info-link-to-map').attr('href', url);
        });
    });

    // Contact.
    wp.customize('astra_nodes_options[contact_page]', function (value) {
        value.bind(function (url) {
            $('#contact-info-page-url').attr('href', url);
        });
    });

    // Email address.
    wp.customize('astra_nodes_options[email_address]', function (value) {
        value.bind(function (text) {
            let email = $('#email-address');
            email.attr('href', 'mailto:' + text);
            email.html(text);
        });
    });

    // Phone number.
    wp.customize('astra_nodes_options[phone_number]', function (value) {
        value.bind(function (text) {
            $('#phone-number').html(text);
        });
    });

    // Header buttons.
    for (let i = 1; i <= 6; i++) {

        wp.customize('astra_nodes_options[header_icon_' + i + '_classes]', function (value) {
            value.bind(function (classes) {
                $('#header-button-' + i).attr('class', classes + ' astra-nodes-header-icon');
            });
        });

        wp.customize('astra_nodes_options[header_icon_' + i + '_text]', function (value) {
            value.bind(function (text) {
                $('.header-button-link-' + i).html(text);
            });
        });

        wp.customize('astra_nodes_options[header_icon_' + i + '_link]', function (value) {
            value.bind(function (url) {
                $('.header-button-link-' + i).attr('href', url);
            });
        });

        wp.customize('astra_nodes_options[header_icon_' + i + '_open_in_new_tab]', function (value) {
            value.bind(function (target) {
                if (target) {
                    $('.header-button-link-' + i).attr('target', '_blank');
                } else {
                    $('.header-button-link-' + i).attr('target', '_self');
                }
            });
        });

        wp.customize('astra_nodes_options[header_icon_' + i + '_small_text]', function (value) {
            value.bind(function (isSmall) {
                const span = $('.header-button-link-' + i);
                if (isSmall) {
                    span.css('font-size', 'smaller');
                } else {
                    span.css('font-size', '');
                }
            });
        });

    }

    // Front page cards
    for (let i = 1; i <= 4; i++) {

        // Title
        wp.customize('astra_nodes_options[front_page_card_' + i + '_title]', function (value) {
            value.bind(function (text) {
                $('#card-title-' + i).html(text);
            });
        });

        // Image
        wp.customize('astra_nodes_options[front_page_card_' + i + '_image]', function (value) {
            value.bind(function (url) {
                $('#card-image-' + i).attr('src', url);
            });
        });

        // Color pick
        wp.customize('astra_nodes_options[front_page_card_' + i + '_color]', function (value) {
            value.bind(function (color) {
                $('#card-color-' + i).attr('style', 'background-color:' + color + ' !important');
            });
        });

        // Link
        wp.customize('astra_nodes_options[front_page_card_' + i + '_url]', function (value) {
            value.bind(function (url) {
                $('#card-link-' + i).attr('href', url);
            });
        });

        // New tab
        wp.customize('astra_nodes_options[front_page_card_' + i + '_open_in_new_tab]', function (value) {
            value.bind(function (checked) {
                if (checked) {
                    $('#card-link-' + i).attr('target', '_blank');
                } else {
                    $('#card-link-' + i).removeAttr('target');
                }
            });
        });
    }

    // Front page notice: Image.
    wp.customize('astra_nodes_options[front_page_notice_image]', function (value) {
        value.bind(function (url) {
            $('#front-page-notice-image').attr('src', url);
        });
    });

    // Front page notice: Color.
    wp.customize('astra_nodes_options[front_page_notice_background_color]', function (value) {
        value.bind(function (color) {
            $('#front-page-notice-text').attr('style', 'background-color:' + color);
        });
    });

    // Front page notice: URL.
    wp.customize('astra_nodes_options[front_page_notice_url]', function (value) {
        value.bind(function (url) {
            $('#notice-img-url').attr('href', url);
        });
    });

    // Front page notice: New Tab.
    wp.customize('astra_nodes_options[front_page_notice_open_in_new_tab]', function (value) {
        value.bind(function (checked) {
            if (checked) {
                $('#notice-img-url').attr('target', '_blank');
            } else {
                $('#notice-img-url').removeAttr('target');
            }
        });
    });

    // Front page notice: pre-title.
    wp.customize('astra_nodes_options[front_page_notice_pre_title]', function (value) {
        value.bind(function (text) {
            $('#front-page-notice-pre-title').html(text);
        });
    });

    // Front page notice: title.
    wp.customize('astra_nodes_options[front_page_notice_title]', function (value) {
        value.bind(function (text) {
            $('#front-page-notice-title').html(text);
        });
    });

    // Front page notice: content.
    wp.customize('astra_nodes_options[front_page_notice_content]', function (value) {
        value.bind(function (text) {
            $('#front-page-notice-content').html(text);
        });
    });

    // Front page slider: Height
    wp.customize('astra_nodes_options[front_page_slider_min_height]', function (value) {
        value.bind(function (height) {
            $('#slider-height-container').css('min-height', height + 'px');
            // Recalculate slide height individually
            for (let i = 1; i <= 5; i++) {
                $('.slide-' + i).css('height', height + 'px');
                $('.slide-' + i).css('min-height', height + 'px');
                $('#slider-image-' + i).css('height', height + 'px');
            }
        });
    });

    // Front page slider
    for (let i = 1; i <= 5; i++) {

        // Image
        wp.customize('astra_nodes_options[front_page_slider_image_' + i + ']', function (value) {
            value.bind(function (url) {
                $('#slider-image-' + i).attr('src', url);
            });
        });

        // URL
        wp.customize('astra_nodes_options[front_page_slider_link_' + i + ']', function (value) {
            value.bind(function (url) {
                $('#slider-link-' + i).attr('href', url);
            });
        });

        // New tab
        wp.customize('astra_nodes_options[front_page_slider_open_in_new_tab_' + i + ']', function (value) {
            value.bind(function (checked) {
                if (checked) {
                    $('#slider-link-' + i).attr('target', '_blank');
                } else {
                    $('#slider-link-' + i).removeAttr('target');
                }
            });
        });

        // Heading
        wp.customize('astra_nodes_options[front_page_slider_heading_' + i + ']', function (value) {
            value.bind(function (text) {
                $('#slider-heading-' + i).html(text);
            });
        });

        // Text
        wp.customize('astra_nodes_options[front_page_slider_text_' + i + ']', function (value) {
            value.bind(function (text) {
                $('#slider-text-' + i).html(text);
            });
        });
    }

})(jQuery);
