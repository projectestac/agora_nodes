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




    
    wp.customize('reactor_options[show_title]', function (value) {
        value.bind(function (to) {
            if (to === '') {
                $('.site-title, .site-description').css('display', 'none');
            } else if (to === 1) {
                $('.site-title, .site-description').css('display', 'block');
            }
        });
    });

    // Top Bar
    wp.customize('reactor_options[topbar_title]', function (value) {
        value.bind(function (to) {
            $('li.name h1 a').html(to);
        });
    });

    wp.customize('reactor_options[topbar_fixed]', function (value) {
        value.bind(function (to) {
            if (to === '') {
                $('.top-bar-container').removeClass('fixed');
                $('body').css('padding-top', 0);
            } else if (to === 1) {
                $('.top-bar-container').addClass('fixed');
                $('body').css('padding-top', $('.top-bar').outerHeight());
            }
        });
    });

    wp.customize('reactor_options[topbar_contain]', function (value) {
        value.bind(function (to) {
            if (to === '') {
                $('.top-bar-container').removeClass('contain-to-grid');
            } else if (to === 1) {
                $('.top-bar-container').addClass('contain-to-grid');
            }
        });
    });

    wp.customize('reactor_options[megadrop_textarea]', function (value) {
        value.bind(function (to) {
            $('.top-megadrop').html(to);
        });
    });

    // Posts & Pages
    wp.customize('reactor_options[post_readmore]', function (value) {
        value.bind(function (to) {
            $('.more-link').html(to);
        });
    });

    // Footer
    wp.customize('reactor_options[footer_siteinfo]', function (value) {
        value.bind(function (to) {
            $('#colophon').html(to);
        });
    });

})(jQuery);