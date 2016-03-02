/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes.
 */

( function( $ ) {

	// Header
	wp.customize('blogname', function(value) {
		value.bind(function(to) {
			$('.site-title a').html(to);
		});
	});
	wp.customize('blogdescription', function(value) {
		value.bind(function(to) {
			$('.site-description').html(to);
		});
	});
    wp.customize('reactor_options[show_title]', function(value) {
        value.bind(function(to) {
            if( to == '' ) {
				$('.site-title, .site-description').css('display', 'none');
			}
			else if( to == 1 ) {
				$('.site-title, .site-description').css('display', 'block');
			};
        });
    });

	// Top Bar
	wp.customize('reactor_options[topbar_title]', function(value) {
		value.bind(function(to) {
			$('li.name h1 a').html(to);
		});
	});
	wp.customize('reactor_options[topbar_fixed]', function(value) {
		value.bind(function(to) {
			if( to == '' ){
				$('.top-bar-container').removeClass('fixed');
				$('body').css('padding-top', 0);
			}
			else if( to == 1 ) {
				$('.top-bar-container').addClass('fixed');
				$('body').css('padding-top', $('.top-bar').outerHeight())
			}
		});
	});
	wp.customize('reactor_options[topbar_contain]', function(value) {
		value.bind(function(to) {
			if( to == '' ){
				$('.top-bar-container').removeClass('contain-to-grid');
			}
			else if( to == 1 ) {
				$('.top-bar-container').addClass('contain-to-grid');
			}
		});
	});
	wp.customize('reactor_options[megadrop_textarea]', function(value) {
		value.bind(function(to) {
			$('.top-megadrop').html(to);
		});
	});

	// Posts & Pages
	wp.customize('reactor_options[post_readmore]', function(value) {
		value.bind(function(to) {
			$('.more-link').html(to);
		});
	});

	// Footer
	wp.customize('reactor_options[footer_siteinfo]', function(value) {
		value.bind(function(to) {
			$('#colophon').html(to);
		});
	});

} )( jQuery );