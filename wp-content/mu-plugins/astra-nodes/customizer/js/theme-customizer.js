/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes.
 */
(function ($) {

    // Header logo.
    wp.customize('astra_nodes_options[custom_logo]', function (value) {
        value.bind(function (url) {
            $('.site-logo-img a.custom-logo-link').html(
                '<img width="266" height="143" src="' + url + '" class="custom-logo" alt="" decoding="async">'
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

    // Front page button 1.
    wp.customize('astra_nodes_options[header_icon_1_classes]', function (value) {
        value.bind(function (text) {
            $('#header-button-1').attr('class', text + ' astra-nodes-header-icon');
        });
    });

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