<?php
/**
 * The template for displaying post content
 *
 * @package Reactor
 * @subpackage Post-Formats
 * @since 1.0.0
 */
 global $layout;
 global $card_bgcolor;
 global $amplada;
 
 switch ($layout) {
    case 1: $amplada="large-12";            
            break;
    case 66:$amplada="large-8";		
            break;
    case 2: $amplada="large-6";		
            break;
    case 33:$amplada="large-4";		
            break;
    case 3: $amplada="large-4";		
            break;
    case 4: $amplada="large-3";		
            break;
    default:	
            $amplada="large-6";		 
 }
?>
  		
<?php if ($amplada!="large-12") { ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class("$amplada columns $card_bgcolor"); ?>>
            <div class="entry-body">
                <?php echo reactor_tumblog_icon(); ?>
                <header class="entry-header">
                    <?php reactor_post_header(); ?>
                </header>
                <div class="entry-summary">
                    <?php (get_post_meta( get_the_ID(), '_bloc_html', true )!="on")? the_excerpt(): the_content();?>
                </div>
                <footer class="entry-footer">
                    <?php  reactor_post_footer();?>
                </footer>
             </div><!-- .entry-body -->
 	 </article><!-- #post -->
   <?php } else {   
            // Targeta ocupa tota l'amplada
            if (get_post_meta( get_the_ID(), '_bloc_html', true )=="on"){
                $bloc_html=true;
                $ample="large-12";
            }else {
                $bloc_html=false;
                $ample="large-8";
            }
    ?> 
          <article id="post-<?php the_ID(); ?>" <?php post_class("$amplada columns $card_bgcolor"); ?>>
                <div class="entry-body row">
                    <?php echo reactor_tumblog_icon(); ?>
                    <div class="entry-summary <?php echo $ample;?> columns">
                        <?php reactor_do_standard_header_titles(); ?>
                        <?php reactor_do_meta_autor_date(); ?>
                        <?php ($bloc_html)? the_content() : the_excerpt(); ?>
                    </div>

                    <?php if (!$bloc_html) { ?>
                        <div class="large-3 columns">   
                            <header class="entry-header">
                                <?php reactor_do_standard_thumbnail(); ?>
                            </header>	
                        </div> 
                    <?php } ?>
                    <div>    
                    <footer style="padding:0.8em" class="entry-footer">
                        <?php  reactor_post_footer();?>
                    </footer>
                    </div>    
                </div><!-- .entry-body -->
           </article><!-- #post -->
<?php } ?>
