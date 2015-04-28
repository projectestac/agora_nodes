<?php
/*
Plugin Name: WordPress phpinfo()
Plugin URI: http://thisismyurl.com/downloads/wordpress-phpinfo/
Description:  This simple plugin adds an option to an adminstrator's Tools menu which displays standard phpinfo() feedback details to the user.
Author: Christopher Ross
Version: 14.12
Author URI: http://thisismyurl.com/
*/


/**
 * WordPress phpinfo() core file
 *
 * This file contains all the logic required for the plugin
 *
 * @link		http://wordpress.org/extend/plugins/wordpress-phpinfo/
 *
 * @package 	WordPress phpinfo()
 * @copyright	Copyright (c) 2008, Chrsitopher Ross
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 *
 * @since 		WordPress phpinfo() 1.0
 *
 *
 */




/**
 * Creates the Class for WordPress phpinfo()
 *
 * @author     Christopher Ross <info@thisismyurl.com>
 * @version    Release: @14.11@
 * @see        wp_enqueue_scripts()
 * @since      Class available since Release 14.11
 */
class thissimyurl_WPPHPInfo {

	 /**
	  * Standard Constructor
	  *
      * @access public
      * @static
	  * @uses http://codex.wordpress.org/Function_Reference/add_action
      * @since Method available since Release 14.11
	  */
    public function __construct() {

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
     	add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }


	/**
	  * admin_enqueue_scripts
	  *
      * @access public
      * @static
	  * @uses http://codex.wordpress.org/Function_Reference/wp_enqueue_style
      * @since Method available since Release 14.12
      *
	  */
	function admin_enqueue_scripts() {

		if ( isset( $_GET['page'] ) ) {

			if ( 'thisismyurl_wpphpinfo' != $_GET['page'] )
		        return;

			wp_register_style( 'thisismyurl-wpphpinfo', plugin_dir_url( __FILE__ ) . 'css/thisismyurl-admin.css', false, '14.12' );
		    wp_enqueue_style( 'thisismyurl-wpphpinfo' );

		}

	}

	/**
	  * admin_menu
	  *
      * @access public
      * @static
	  * @uses http://codex.wordpress.org/Function_Reference/add_options_page
      * @since Method available since Release 14.12
      *
	  */
	function admin_menu() {
		//XTEC ************ MODIFICAT - Made plugin only visible for xtecadmin.
		//2015.04.09 @vsaavedr
		if( is_xtecadmin() ) {
			//************ FI
			add_options_page( __( 'WordPress phpinfo()', 'thisismyurl_wpphpinfo' ), __( 'WordPress phpinfo()', 'thisismyurl_wpphpinfo' ), 'manage_options', 'thisismyurl_wpphpinfo', array( $this, 'thisismyurl_wpphpinfo_page' ) );
		}
	}


	function thisismyurl_wpphpinfo_page() {
		?>
		<div class="wrap">
			<div class="thisismyurl-icon32"><br /></div>
			<h2><?php _e( 'WordPress phpinfo()', 'thisismyurl_wpphpinfo' ); ?></h2>
			<p><?php _e( 'It is important for a non technical administrator to be able to diagnose server related problems in WordPress.', 'thisismyurl_wpphpinfo' ); ?></p>

			<h3><?php _e( 'General Settings', 'thisismyurl_wpphpinfo' ); ?></h3>
			<p><?php printf( __( 'The plugin has no settings, once activated it will work automattically. For further details, please view the <a href="%sreadme.txt">readme.txt</a> file included with this release.', 'thisismyurl_wpphpinfo' ), plugin_dir_url( __FILE__ ) ); ?></p>
			<?php $this->phpinfo_output(); ?>
		</div>
		<?php
	}

	function phpinfo_output() {

		ob_start();
		phpinfo(-1);
		$phpinfo_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $phpinfo_content ) )
			$phpinfo_array = explode( '<table', $phpinfo_content );

		if ( ! empty( $phpinfo_array ) ) {
			unset( $phpinfo_array[0] );
			foreach ( $phpinfo_array as $phpinfo_element ) {

				$phpinfo_element = str_replace( '<tr', '<tr valign="top"', $phpinfo_element );

				echo '<table class="phpinfo" ' . $phpinfo_element;
				echo '<div style="clear:both"></div>';
			}

		}
	}


}

$thissimyurl_WPPHPInfo = new thissimyurl_WPPHPInfo;










/**
  * plugin_action_links
  *
  * @access public
  * @static
  * @since Method available since Release 14.12
  * @todo why can't this be called within the class?
  *
  */
function thisismyurl_wpphpinfo_plugin_action_links( $links, $file ) {

	static $this_plugin;

	if( ! $this_plugin )
		$this_plugin = plugin_basename( __FILE__ );

	if( $file == $this_plugin ){
		$links[] = '<a href="options-general.php?page=thisismyurl_wpphpinfo">' . __( 'phpinfo()', 'thisismyurl_wpphpinfo' ) . '</a>';
		$links[] = '<a href="http://thisismyurl.com/downloads/wordpress-phpinfo/">' . __( 'Author', 'thisismyurl_wpphpinfo' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_action_links', 'thisismyurl_wpphpinfo_plugin_action_links', 10, 2 );



function wordpressphpinfo(){
	return $thissimyurl_WPPHPInfo->phpinfo_output();
}