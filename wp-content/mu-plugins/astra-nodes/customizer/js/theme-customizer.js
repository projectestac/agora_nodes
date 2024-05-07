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

    }

    // Front page notice: Image.
    wp.customize('astra_nodes_options[front_page_notice_image]', function (value) {
        value.bind(function (url) {
            $('#front-page-notice-image').attr('src', url);
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

})(jQuery);