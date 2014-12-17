<?php 
/**
 * Tumblog Icons
 *
 * must be used in the loop
 * gets the post format and outputs the appropriate icon
 * can be hidden within customizer
 * uses Foundation Icon Fonts 
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://www.zurb.com/playground/foundation-icons
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

if ( !function_exists('reactor_tumblog_icon') ) { 
	function reactor_tumblog_icon( $args = '' ) {
		
                $icon = '';
                $title='';
           
                $icon_type=get_post_meta( get_the_ID(), '_entry_icon', true );
           
		switch( $icon_type ) {
                    case 'carrusel' : 
                        $icon .= '<i class="fi-camera"></i>'; 
                        $title="Inclou fotografies";
                        break;
                    case 'video' : 
                        $icon .= '<i class="fi-play-video"></i>'; 
                        $title="Inclou vídeo";
                        break;
                    case 'alerta' : 
                        $icon .= '<i style="color:red !important" class="fi-alert"></i>';
                        $title="Avís Important";
                        break;
                    case 'musica' : 
                        $icon .= '<i class="fi-music"></i>';
                        $title="Inclou música";
                        break;
                    case 'podcast' : 
                        $icon .= '<i class="fi-microphone"></i>';
                        $title="Inclou podcast";
                        break;
                    case 'document' : 
                        $icon .= '<i class="fi-page"></i>';
                        $title="Inclou documents";
                        break;
                    default:
                        $icon .= '';
                        
                       
		}
		$output = '<div class="entry-icon">';  
                $output .= '<a href="' . get_permalink( get_the_ID() ) . '" title="' .$title. '" rel="bookmark">';
                $output .= $icon;
                $output .= '</a></div>';
		
		if ( $args['echo'] ) {
			echo apply_filters('reactor_tumblog_icon', $output);
		} else {
			return apply_filters('reactor_tumblog_icon', $output);
		}
	}
}
