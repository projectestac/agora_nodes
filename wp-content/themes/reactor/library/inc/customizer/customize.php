<?php
/**
 * Reactor Theme Customizer
 * Add settings to the WP Theme Customizer
 * and generates custom CSS/JS from those settings
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @author Samuel Wood (Otto) (@Otto42 / ottopress.com)
 * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
 * @since 1.0.0
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Add Customizer generated CSS to header
 *
 * @since 1.0.0
 */
function reactor_customizer_css() {
	do_action('reactor_customizer_css');

	$output = ''; $body_css = '';

	if ( !empty( $body_css ) ) {
	    $output .= "\n" . 'body { ' .  $body_css . ' }';
	}
	if ( 0 == reactor_option('show_title', 1) ) {
	    $output .= "\n" . '.site-title, .site-description { display: none; }';
	}

	echo ( $output ) ? '<style>' . apply_filters('reactor_customizer_css', $output) . "\n" . '</style>' . "\n" : '';
}
add_action('wp_head', 'reactor_customizer_css');

/**
 * JavaScript handlers to make Theme Customizer preview reload changes asynchronously.
 * Credit: Twenty Twelve 1.0
 *
 * @since 1.0.0
 */
function reactor_customize_preview_js() {
	wp_enqueue_script('reactor-customizer', get_template_directory_uri() . '/library/inc/customizer/js/theme-customizer.js', array('customize-preview'), '', true );
}
add_action('customize_preview_init', 'reactor_customize_preview_js');

/**
 * Add CSS to the WP Theme Customizer page
 *
 * @since 1.0.0
 */
function reactor_customize_preview_css() {
	echo '
	<style type="text/css">
		.customize-control { margin-bottom:5px; }
		.customize-control-radio { padding:0; }
		.customize-control-checkbox label { line-height:20px; }
	</style>';
}
add_action('customize_controls_print_styles', 'reactor_customize_preview_css', 99);

/**
 * Register Customizer
 *
 * @author Samuel Wood (Otto) (@Otto42 / ottopress.com)
 * @link http://ottopress.com/2012/theme-customizer-part-deux-getting-rid-of-options-pages/
 * @since 1.0.0
 */
if ( !function_exists('reactor_customize_register') ) {
	add_action('customize_register', 'reactor_customize_register');

	function reactor_customize_register( $wp_customize ) {

		do_action('reactor_customize_register', $wp_customize);

		class WP_Customize_Textarea_Control extends WP_Customize_Control {
		public $type = 'textarea';

			public function render_content() { ?>
				<label><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
                </label>
			<?php
			}
		}

		/**
		 * modified dropdown-pages
		 * from wp-includes/class-wp-customize-control.php
		 *
		 * @since 1.0.0
		 */
		class WP_Customize_Dropdown_Categories_Control extends WP_Customize_Control {
		public $type = 'dropdown-categories';

			public function render_content() {
				$dropdown = wp_dropdown_categories(
					array(
						'name'             => '_customize-dropdown-categories-' . $this->id,
						'echo'             => 0,
						'hide_empty'       => false,
						'show_option_none' => '&mdash; ' . __('Select', 'reactor') . ' &mdash;',
						'hide_if_empty'    => false,
						'selected'         => $this->value(),
					 )
				 );

				$dropdown = str_replace('<select', '<select ' . $this->get_link(), $dropdown );

				printf(
					'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
					$this->label,
					$dropdown
				 );
			}
		}

		/**
		 * modified dropdown-pages
		 * from wp-includes/class-wp-customize-control.php
		 *
		 * @since 1.0.0
		 */
		class WP_Customize_Dropdown_Slide_Categories_Control extends WP_Customize_Control {
		public $type = 'dropdown-slide-categories';

			public function render_content() {
				$dropdown = wp_dropdown_categories(
					array(
						'name'              => '_customize-dropdown-slide-categories-' . $this->id,
						'echo'              => 0,
						'hide_empty'        => false,
						'show_option_none'  => '&mdash; ' . __('Select', 'reactor') . ' &mdash;',
						'hide_if_empty'     => false,
						'name'              => 'slide-cat',
						'taxonomy'          => 'slide-category',
						'selected'          => $this->value(),
					 )
				 );

				$dropdown = str_replace('<select', '<select ' . $this->get_link(), $dropdown );

				printf(
					'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
					$this->label,
					$dropdown
				 );
			}
		}

		/**
		 * Remove default WP Customize sections
		 *
		 * @since 1.0.0
		 */
		$wp_customize->remove_section('title_tagline');
		$wp_customize->remove_section('colors');
		$wp_customize->remove_section('header_image');
		$wp_customize->remove_section('background_image');
		$wp_customize->remove_section('static_front_page');
		$wp_customize->remove_section('nav');

		/**
		 * setup customizer settings
		 *
		 * @since 1.0.0
		 */

		// Header
		$wp_customize->add_section('reactor_customizer_general', array(
			'title'    => __('General', 'reactor'),
			'priority' => 5,
		 ) );

			$wp_customize->add_setting('blogname', array(
				'default'    => get_option('blogname'),
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			 ) );
				$wp_customize->add_control('blogname', array(
					'label'    => __('Site Title', 'reactor'),
					'section'  => 'reactor_customizer_general',
					'priority' => 1,
				 ) );

			$wp_customize->add_setting('blogdescription', array(
				'default'    => get_option('blogdescription'),
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			 ) );
				$wp_customize->add_control('blogdescription', array(
					'label'    => __('Tagline', 'reactor'),
					'section'  => 'reactor_customizer_general',
					'priority' => 2,
				 ) );

			$wp_customize->add_setting('reactor_options[show_title]', array(
				'default'    => 1,
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			 ) );
				$wp_customize->add_control('reactor_options[show_title]', array(
					'label'    => __('Show Title & Tagline', 'reactor'),
					'section'  => 'reactor_customizer_general',
					'type'     => 'checkbox',
					'priority' => 3,
				 ) );

			$wp_customize->add_setting('reactor_options[logo_image]', array(
				'default'    => '',
				'type'       => 'option',
				'capability' => 'manage_options',
			 ) );
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'reactor_logo_image', array(
					'label'    => __('Site Logo', 'reactor'),
					'section'  => 'reactor_customizer_general',
					'settings' => 'reactor_options[logo_image]',
					'priority' => 4,
				 ) ) );

			$wp_customize->add_setting('reactor_options[favicon_image]', array(
				'default'    => '',
				'type'       => 'option',
				'capability' => 'manage_options',
			 ) );
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'reactor_favicon_image', array(
					'label'    => __('Favicon', 'reactor'),
					'section'  => 'reactor_customizer_general',
					'settings' => 'reactor_options[favicon_image]',
					'priority' => 5,
				 ) ) );

			$wp_customize->add_setting('reactor_options[footer_siteinfo]', array(
				'default'    => '',
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			 ) );
				$wp_customize->add_control( 'reactor_options[footer_siteinfo]', array(
					'label'    => __('Footer Site Info', 'reactor'),
					'section'  => 'reactor_customizer_general',
					'priority' => 6,
				 ) );

		// Navigation
		$menus = get_theme_support('reactor-menus');

		if ( !is_array( $menus[0] ) ) {
			$menus[0] = array();
		}

		$wp_customize->add_section('reactor_customizer_nav', array(
			'title'          => __('Navigation', 'reactor'),
			'priority'       => 10,
			'description'    => '',
			'theme_supports' => 'reactor-menus',
			 ) );

		if ( in_array('top-bar-l', $menus[0] ) || in_array('top-bar-r', $menus[0] ) ) {
			$wp_customize->add_setting('reactor_options[topbar_title]', array(
				'default'        => get_bloginfo('name'),
				'type'           => 'option',
				'capability'     => 'manage_options',
				'transport'      => 'postMessage',
				'theme_supports' => 'reactor-menus',
			 ) );
				$wp_customize->add_control('reactor_options[topbar_title]', array(
					'label'    => __('Top Bar Title', 'reactor'),
					'section'  => 'reactor_customizer_nav',
					'type'     => 'text',
					'priority' => 1,
				 ) );

			$wp_customize->add_setting('reactor_options[topbar_title_url]', array(
				'default'        => home_url(),
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-menus',
			 ) );
				$wp_customize->add_control('reactor_options[topbar_title_url]', array(
					'label'    => __('Top Bar Title Link', 'reactor'),
					'section'  => 'reactor_customizer_nav',
					'type'     => 'text',
					'priority' => 2,
				 ) );

			$wp_customize->add_setting('reactor_options[topbar_fixed]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'transport'      => 'postMessage',
				'theme_supports' => 'reactor-menus',
			 ) );
				$wp_customize->add_control('reactor_options[topbar_fixed]', array(
					'label'    => __('Fixed Top Bar', 'reactor'),
					'section'  => 'reactor_customizer_nav',
					'type'     => 'checkbox',
					'priority' => 3,
				 ) );

			$wp_customize->add_setting('reactor_options[topbar_contain]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'transport'      => 'postMessage',
				'theme_supports' => 'reactor-menus',
			 ) );
				$wp_customize->add_control('reactor_options[topbar_contain]', array(
					'label'    => __('Contain Top Bar Width', 'reactor'),
					'section'  => 'reactor_customizer_nav',
					'type'     => 'checkbox',
					'priority' => 4,
				 ) );
		}

		if ( in_array('side-menu', $menus[0] ) ) {
			$wp_customize->add_setting('reactor_options[side_nav_type]', array(
				'default'        => 'accordion',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-menus',
			 ) );
				$wp_customize->add_control('reactor_options[side_nav_type]', array(
					'label'   => __('Side Menu Type', 'reactor'),
					'section' => 'reactor_customizer_nav',
					'type'    => 'radio',
					'choices' => array(
						'accordion' => __('Accordion', 'reactor'),
						'side_nav'  => __('Side Nav', 'reactor'),
					 ),
					 'priority' => 5
				 ) );
		}

		if ( in_array('main-menu', $menus[0] ) ) {
			$wp_customize->add_setting('reactor_options[mobile_menu]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-menus',
			 ) );
				$wp_customize->add_control('reactor_options[mobile_menu]', array(
					'label'    => __('Off Canvas Main Menu', 'reactor'),
					'section'  => 'reactor_customizer_nav',
					'type'     => 'checkbox',
					'priority' => 6
				 ) );
		}

		// Posts & Pages
		$layouts = get_theme_support('reactor-layouts');
		$theme_layouts = array();

		if ( !is_array( $layouts[0] ) ) {
			$layouts[0] = array();
		}

		if ( in_array( '1c', $layouts[0] ) ) {   $theme_layouts['1c']   = __('One Column', 'reactor'); }
		if ( in_array( '2c-l', $layouts[0] ) ) { $theme_layouts['2c-l'] = __('Two Columns, Left', 'reactor'); }
		if ( in_array( '2c-r', $layouts[0] ) ) { $theme_layouts['2c-r'] = __('Two Columns, Right', 'reactor'); }
		if ( in_array( '3c-l', $layouts[0] ) ) { $theme_layouts['3c-l'] = __('Three Columns, Left', 'reactor'); }
		if ( in_array( '3c-r', $layouts[0] ) ) { $theme_layouts['3c-r'] = __('Three Columns, Right', 'reactor'); }
		if ( in_array( '3c-c', $layouts[0] ) ) { $theme_layouts['3c-c'] = __('Three Columns, Center', 'reactor'); }

		$wp_customize->add_section('reactor_customizer_posts', array(
			'title'    => __('Posts & Pages', 'reactor'),
			'priority' => 20,
		 ) );

			$wp_customize->add_setting('reactor_options[page_layout]', array(
				'default'    => '2c-l',
				'type'       => 'option',
				'capability' => 'manage_options',
			 ) );
				$wp_customize->add_control('reactor_options[page_layout]', array(
					'label'    => __('Default Layout', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'select',
					'choices'  => $theme_layouts,
					'priority' => 4,
				 ) );

			$wp_customize->add_setting('reactor_options[page_links]', array(
				'default'        => 'numbered',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-links',
			 ) );
				$wp_customize->add_control('reactor_options[page_links]', array(
					'label'    => __('Page Link Type', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'select',
					'choices'  => array(
						'numbered'  => __('Numbered', 'reactor'),
						'prev_next' => __('Prev / Next', 'reactor'),
						 ),
					'priority' => 5,
				 ) );

			$wp_customize->add_setting('reactor_options[post_readmore]', array(
				'default'    => __('Read More', 'reactor'). '&raquo;',
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			 ) );
				$wp_customize->add_control('reactor_options[post_readmore]', array(
					'label'    => __('Read More Text', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'text',
					'priority' => 6,
				 ) );

			$wp_customize->add_setting('reactor_options[post_meta]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-post-meta',
			 ) );
				$wp_customize->add_control('reactor_options[post_meta]', array(
					'label'    => __('Show Post Meta', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'checkbox',
					'priority' => 7,
				 ) );

			$wp_customize->add_setting('reactor_options[comment_link]', array(
				'default'    => 1,
				'type'       => 'option',
				'capability' => 'manage_options',
			 ) );
				$wp_customize->add_control('reactor_options[comment_link]', array(
					'label'    => __('Show Comment Link', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'checkbox',
					'priority' => 8,
				 ) );

			$wp_customize->add_setting('reactor_options[tumblog_icons]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-tumblog-icons',
			 ) );
				$wp_customize->add_control('reactor_options[tumblog_icons]', array(
					'label'    => __('Show Tumblog Icons', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'checkbox',
					'priority' => 9,
				 ) );

			$wp_customize->add_setting('reactor_options[breadcrumbs]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-breadcrumbs',
			 ) );
				$wp_customize->add_control('reactor_options[breadcrumbs]', array(
					'label'    => __('Show Breadcrumbs', 'reactor'),
					'section'  => 'reactor_customizer_posts',
					'type'     => 'checkbox',
					'priority' => 10,
				 ) );

		// Login
		$wp_customize->add_section('reactor_customizer_login', array(
			'title'          => __('Login', 'reactor'),
			'priority'       => 45,
			'theme_supports' => 'reactor-custom-login',
		 ) );

			$wp_customize->add_setting('reactor_options[login_logo]', array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-custom-login',
			 ) );
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'reactor_login_logo', array(
					'label'    => __('Login Logo', 'reactor'),
					'section'  => 'reactor_customizer_login',
					'settings' => 'reactor_options[login_logo]',
				 ) ) );

			$wp_customize->add_setting('reactor_options[login_logo_url]', array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-custom-login',
			 ) );
				$wp_customize->add_control('reactor_options[login_logo_url]', array(
					'label'    => __('Logo Link URL', 'reactor'),
					'section'  => 'reactor_customizer_login',
					'type'     => 'text',
				 ) );

			$wp_customize->add_setting('reactor_options[login_logo_title]', array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-custom-login',
			 ) );
				$wp_customize->add_control('reactor_options[login_logo_title]', array(
					'label'    => __('Logo Title Attribute', 'reactor'),
					'section'  => 'reactor_customizer_login',
					'type'     => 'text',
				 ) );


		$templates = get_theme_support('reactor-page-templates');

		if ( !is_array( $templates[0] ) ) {
			$templates[0] = array();
		}

		// Front Page
		if ( in_array( 'front-page', $templates[0] ) ) {
		$wp_customize->add_section('frontpage_settings', array(
			'title'          => __('Front Page', 'reactor'),
			'priority'       => 50,
			'theme_supports' => 'reactor-page-templates'
		 ) );

			$wp_customize->add_setting('reactor_options[frontpage_post_category]', array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control( new WP_Customize_Dropdown_Categories_Control( $wp_customize, 'reactor_frontpage_post_category', array(
					'label'    => __('Post Category', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'dropdown-categories',
					'settings' => 'reactor_options[frontpage_post_category]',
					'priority' => 1,
				 ) ) );

			$wp_customize->add_setting('reactor_options[frontpage_exclude_cat]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_exclude_cat]', array(
					'label'    => __('Exclude From Blog', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'checkbox',
					'priority' => 2,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_slider_category]', array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => array('reactor-page-templates'),
			 ) );
				$wp_customize->add_control( new WP_Customize_Dropdown_Slide_Categories_Control( $wp_customize, 'reactor_frontpage_slider_category', array(
					'label'    => __('Slider Category', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'dropdown-slide-categories',
					'settings' => 'reactor_options[frontpage_slider_category]',
					'priority' => 3,
				 ) ) );

			$wp_customize->add_setting('reactor_options[frontpage_post_columns]', array(
				'default'        => '3',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_post_columns]', array(
					'label'   => __('Post Columns', 'reactor'),
					'section' => 'frontpage_settings',
					'type'    => 'select',
					'choices' => array(
						'1' => __('1 Column', 'reactor'),
						'2' => __('2 Columns', 'reactor'),
						'3' => __('3 Columns', 'reactor'),
						'4' => __('4 Columns', 'reactor'),
					),
					'priority' => 4,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_number_posts]', array(
				'default'        => 3,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_number_posts]', array(
					'label'    => __('Number of Posts', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'text',
					'priority' => 6,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_show_titles]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_show_titles]', array(
					'label'    => __('Show Post Titles', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'checkbox',
					'priority' => 7,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_link_titles]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_link_titles]', array(
					'label'    => __('Link Post Titles', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'checkbox',
					'priority' => 8,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_comment_link]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_comment_link]', array(
					'label'    => __('Show Comment Link', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'checkbox',
					'priority' => 9,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_post_meta]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => array('reactor-page-templates', 'reactor-post-meta'),
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_post_meta]', array(
					'label'    => __('Show Post Meta', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'checkbox',
					'priority' => 10,
				 ) );

			$wp_customize->add_setting('reactor_options[frontpage_page_links]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => array('reactor-page-templates', 'reactor-page-links'),
			 ) );
				$wp_customize->add_control('reactor_options[frontpage_page_links]', array(
					'label'    => __('Show Page Links', 'reactor'),
					'section'  => 'frontpage_settings',
					'type'     => 'checkbox',
					'priority' => 11,
				 ) );
		}

		// News Page
		if ( in_array( 'news-page', $templates[0] ) ) {
		$wp_customize->add_section('newspage_settings', array(
			'title'          => __('News Page', 'reactor'),
			'priority'       => 55,
			'theme_supports' => 'reactor-page-templates'
		 ) );

			$wp_customize->add_setting('reactor_options[newspage_slider_category]', array(
				'default'        => '',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => array('reactor-page-templates'),
			 ) );
				$wp_customize->add_control( new WP_Customize_Dropdown_Slide_Categories_Control( $wp_customize, 'reactor_newspage_slider_category', array(
					'label'    => __('Slider Category', 'reactor'),
					'section'  => 'newspage_settings',
					'type'     => 'dropdown-slide-categories',
					'settings' => 'reactor_options[newspage_slider_category]',
					'priority' => 1,
				 ) ) );

			$wp_customize->add_setting('reactor_options[newspage_post_columns]', array(
				'default'        => '2',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[newspage_post_columns]', array(
					'label'   => __('Post Columns', 'reactor'),
					'section' => 'newspage_settings',
					'type'    => 'select',
					'choices' => array(
						'1' => __('1 Column', 'reactor'),
						'2' => __('2 Columns', 'reactor'),
						'3' => __('3 Columns', 'reactor'),
					),
					'priority' => 2,
				 ) );

			$wp_customize->add_setting('reactor_options[newspage_number_posts]', array(
				'default'        => get_option('posts_per_page'),
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[newspage_number_posts]', array(
					'label'    => __('Number of Posts', 'reactor'),
					'section'  => 'newspage_settings',
					'type'     => 'text',
					'priority' => 4,
				 ) );

			$wp_customize->add_setting('reactor_options[newspage_post_meta]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => array('reactor-page-templates', 'reactor-post-meta'),
			 ) );
				$wp_customize->add_control('reactor_options[newspage_post_meta]', array(
					'label'    => __('Show Post Meta', 'reactor'),
					'section'  => 'newspage_settings',
					'type'     => 'checkbox',
					'priority' => 5,
				 ) );

			$wp_customize->add_setting('reactor_options[newspage_comment_link]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[newspage_comment_link]', array(
					'label'    => __('Show Comment Link', 'reactor'),
					'section'  => 'newspage_settings',
					'type'     => 'checkbox',
					'priority' => 6,
				 ) );
		}

		// Contact Page
		if ( in_array( 'contact', $templates[0] ) ) {
		$wp_customize->add_section('contactpage_settings', array(
			'title'          => __('Contact Page', 'reactor'),
			'priority'       => 60,
			'theme_supports' => 'reactor-page-templates'
		 ) );

			$wp_customize->add_setting('reactor_options[contact_email_to]', array(
				'default'        => get_option('admin_email'),
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[contact_email_to]', array(
					'label'    => __('Email Address', 'reactor'),
					'section'  => 'contactpage_settings',
					'type'     => 'text',
					'priority' => 2,
				 ) );

			$wp_customize->add_setting('reactor_options[contact_email_subject]', array(
				'default'        => get_bloginfo('name') . __(' - Contact Form Message', 'reactor'),
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[contact_email_subject]', array(
					'label'    => __('Email Subject', 'reactor'),
					'section'  => 'contactpage_settings',
					'type'     => 'text',
					'priority' => 3,
				 ) );

			$wp_customize->add_setting('reactor_options[contact_email_sent]', array(
				'default'        => __('Thank you! Your email was sent successfully.', 'reactor'),
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[contact_email_sent]', array(
					'label'    => __('Send Successful Message', 'reactor'),
					'section'  => 'contactpage_settings',
					'type'     => 'text',
					'priority' => 4,
				 ) );
		}

		// Portfolio
		if ( in_array( 'portfolio', $templates[0] ) ) {
		$wp_customize->add_section('portfolio_settings', array(
			'title'          => __('Portfolio Page', 'reactor'),
			'priority'       => 65,
			'theme_supports' => 'reactor-page-templates'
		 ) );

			$wp_customize->add_setting('reactor_options[portfolio_post_columns]', array(
				'default'        => '4',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[portfolio_post_columns]', array(
					'label'   => __('Post Columns', 'reactor'),
					'section' => 'portfolio_settings',
					'type'    => 'select',
					'choices' => array(
						'1' => __('1 Column', 'reactor'),
						'2' => __('2 Columns', 'reactor'),
						'3' => __('3 Columns', 'reactor'),
						'4' => __('4 Columns', 'reactor'),
					),
					'priority' => 2,
				 ) );

			$wp_customize->add_setting('reactor_options[portfolio_number_posts]', array(
				'default'        => 20,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[portfolio_number_posts]', array(
					'label'    => __('Number of Posts', 'reactor'),
					'section'  => 'portfolio_settings',
					'type'     => 'text',
					'priority' => 4,
				 ) );

			$wp_customize->add_setting('reactor_options[portfolio_filter_type]', array(
				'default'        => 'jquery',
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[portfolio_filter_type]', array(
					'label'   => __('Filter Type', 'reactor'),
					'section' => 'portfolio_settings',
					'type'    => 'select',
					'choices' => array(
						'jquery' => __('jQuery Filtering', 'reactor'),
						'pages'  => __('Category Pages', 'reactor'),
						 ),
					'priority' => 5,
				 ) );

			$wp_customize->add_setting('reactor_options[portfolio_show_titles]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[portfolio_show_titles]', array(
					'label'    => __('Show Titles', 'reactor'),
					'section'  => 'portfolio_settings',
					'type'     => 'checkbox',
					'priority' => 6,
				 ) );

			$wp_customize->add_setting('reactor_options[portfolio_link_titles]', array(
				'default'        => 1,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => 'reactor-page-templates'
			 ) );
				$wp_customize->add_control('reactor_options[portfolio_link_titles]', array(
					'label'    => __('Link Titles', 'reactor'),
					'section'  => 'portfolio_settings',
					'type'     => 'checkbox',
					'priority' => 7,
				 ) );

			$wp_customize->add_setting('reactor_options[portfolio_post_meta]', array(
				'default'        => 0,
				'type'           => 'option',
				'capability'     => 'manage_options',
				'theme_supports' => array('reactor-page-templates', 'reactor-post-meta'),
			 ) );
				$wp_customize->add_control('reactor_options[portfolio_post_meta]', array(
					'label'    => __('Show Meta', 'reactor'),
					'section'  => 'portfolio_settings',
					'type'     => 'checkbox',
					'priority' => 8,
				 ) );
		}

	}
}
?>
