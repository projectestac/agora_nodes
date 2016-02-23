<?php 
/**
 * Reactor Columns
 * a function to set grid columns based on selected layout
 * can also pass a set number of columns to the function
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

function reactor_columns( $columns = '', $echo = true, $sidebar = false, $sidebar_id = null, $push_pull = 0 ) {

	// add push/pull columns
	$pushpull = '';
	if ( $push_pull > 0 ) {
		$pushpull = ' push-' . intval( $push_pull );
	} elseif ( $push_pull < 0 ) {
		$pushpull = ' pull-' . intval( abs( $push_pull ) );
	}

	// if array of 2 numbers is passed to the function
	if ( $columns && is_array( $columns ) ) {
		echo 'large-' . intval( $columns[0] ) . ' small-' . intval( $columns[1] ) . $pushpull . ' columns';
		return;
	}
	// if just a number is passed to the function
	elseif ( $columns ) {
		echo 'large-' . intval( $columns ) . ' small-12' . $pushpull . ' columns';
		return;
	}

	
	// get the template layout from meta
	$default = reactor_option('page_layout', '2c-l');
	$layout = reactor_option('', $default, '_template_layout');
	
	if ( is_page_template('page-templates/side-menu.php') ) {
		$layout = 'side-menu';
	}
	
	// check if tumblog icons are used in blog
	$tumblog = reactor_option('tumblog_icons', false);
		
	// else check if columns are for a sidebar
	if ( true == $sidebar ) {

		// sidebar columns based on layout
		switch ( $layout ) {
			case '1c': 
				$classes[] = '';
				break;
			case 'side-menu':
				if ( 'accordion' == reactor_option('side_nav_type', 'accordion') ) {
					$classes[] = 'large-3 small-12 pull-9';
				} elseif ( 'side_nav' == reactor_option('side_nav_type', 'accordion') ) {
					$classes[] = 'large-2 small-12 push-10';
				}
				break;
			default:

                // XTEC ************ MODIFICAT - Customization
                // 2015.12.28 @aginard - Change by jmeler

                $classes[] = 'large-3 small-12 pull-9';

                //************ ORIGINAL
                /*
    			// 4 is the default number of columns for 1 sidebar
                $classes[] = 'large-4';
                */
                //************ FI
                
                break;
		}
			

	// else apply columns based on template layout or meta
	} else {

		// number of columns for main content based on layout		
		switch ( $layout ) {
			case '1c':
				// subtract 1 and offset by 1 if using tumblog icons
				if ( $tumblog && is_home() ) {
					$classes[] = 'large-11';
					$classes[] = 'large-offset-1';
				} else {
					$classes[] = 'large-12';
				}
				break;
			case 'side-menu':
				if ( 'accordion' == reactor_option('side_nav_type', 'accordion') ) {
					$classes[] = 'large-9 small-12 push-3';
				} elseif ( 'side_nav' == reactor_option('side_nav_type', 'accordion') ) {
					$classes[] = 'large-10 small-12  push-2';
				}
				break;
			default:
				/* 8 is the default number of columns for a page with 1 sidebar
				subtract 1 and offset by 1 if using tumblog icons */
				if ( $tumblog && is_home() ) {
					$classes[] = 'large-7 small-11  push-4';
					$classes[] = 'large-offset-1';
				} else {

                    // XTEC ************ MODIFICAT - Customization
                    // 2015.12.28 @aginard - Change by jmeler

                    $classes[] = 'large-9 small-12 push-3';

                    //************ ORIGINAL
                    /*
                    $classes[] = 'large-8';
                    */
                    //************ FI

                }
				break;
		}
	}
	
	//always add the columns class
	$classes[] = 'columns';
	
	// remove empty values
	$classes = array_filter( $classes );
		
	// add spaces
	$columns = implode( ' ', array_map( 'esc_attr', $classes ) );
	
	// echo classes unless echo false
	if ( false == $echo ) {
		return apply_filters('reactor_content_cols', $columns);
	} else {
		echo apply_filters('reactor_content_cols', $columns);
	}
}