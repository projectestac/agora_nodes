<?php
/**
 * Post Content
 * hook in the content for post formats
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Post header
 * in format-standard
 * 
 * @since 1.0.0
 */
 
function reactor_do_standard_header_titles() {
	
    if (!is_single() && get_post_meta(get_the_ID(), '_amaga_titol', true) == "on") {
        edit_post_link(__('Edit'), '<div class="edit-link"><span>', '</span></div>');
        return;
    }
   
    if (is_single()) { ?>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
<?php } else { ?>
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('%s', 'reactor'), the_title_attribute('echo=0'))); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<?php } 
    
    edit_post_link(__('Edit'), '<div class="edit-link"><span>', '</span></div>');
}

add_action('reactor_post_header', 'reactor_do_standard_header_titles', 1);

function reactor_do_meta_autor_date() {
    if (is_single() || (get_post_meta( get_the_ID(), '_amaga_metadata', true )!="on")) { 
        echo "<span class='entry-author'><a href='".get_author_posts_url( get_the_author_meta( 'ID' ) )."'>".get_the_author()."</a></span>";
        echo '<span class="entry-date">'.get_the_date('d/m/y' ).'&nbsp;&nbsp;</span>';
    }
}

add_action('reactor_post_header', 'reactor_do_meta_autor_date', 2);

/**
 * Post thumbnail
 * in format-standard
 * 
 * @since 1.0.0
 */
function reactor_do_standard_thumbnail() { 

    if (get_post_meta( get_the_ID(), '_bloc_html', true )=="on")
            return;
    
    if (has_post_thumbnail() && !is_single()) { 
        $image_data   = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), "full");
        $image_height = $image_data[2];
        
        
        if ((get_post_meta(get_the_ID(), '_original_size', true) == "on") 
           ||($image_height<=200)) {
            // a little image or original size option selected, show original size image
            $thumb_src    = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
            echo "<div class='entry-original-featured-image'><img src='" . $thumb_src . "'>";
        } else {
            // a big image, show thumbnail
            list($thumb_src) = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),"large");
            echo "<div class='entry-thumbnail' style='background-image:url(" . $thumb_src .")'>";
        }
        echo "</div>";
        
    }
}

add_action('reactor_post_header', 'reactor_do_standard_thumbnail', 3);
           
/**
 * Post footer meta
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_meta() {
    
    if (!is_single() && get_post_meta( get_the_ID(), '_amaga_metadata', true )=="on") {   
            return;
    }

    $categories = get_the_category();
    $output = '<span class="entry-categories">';

    if($categories){
        foreach($categories as $category) {
                $output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '">'.$category->cat_name.'</a>'.", ";
        }
        echo trim($output, ", ");
        echo "</span>";
        $string_tags=trim(get_the_tag_list("",", "),",");

        if (strlen($string_tags)) 
                echo ' <span class="entry-tags">'.$string_tags.'</span>';

        echo ' <a href="'.get_comments_link().'"><span class="entry-comments">'.get_comments_number().'</span></a>';

    }
}

add_action('reactor_post_footer', 'reactor_do_post_footer_meta', 1);

/**
 * Comments 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_post_comments() {      
    // If comments are open or we have at least one comment, load up the comment template
    if ( is_single() && ( comments_open() || '0' != get_comments_number() ) ) {
            comments_template('', true);
    }
}
add_action('reactor_post_after', 'reactor_do_post_comments', 1);

/**
 * No posts format
 * loop else in page templates
 * 
 * @since 1.0.0
 */
function reactor_do_loop_else() {
	get_template_part('post-formats/format', 'none');
}
add_action('reactor_loop_else', 'reactor_do_loop_else', 1);

?>