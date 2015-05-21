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
                
                <header class="entry-header">
                    <?php echo reactor_tumblog_icon(); ?>
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
            $ample="large-12";
            if (get_post_meta( get_the_ID(), '_bloc_html', true )=="on" ){
                $bloc_html=true;
            }else {
                $bloc_html=false;
                if (has_post_thumbnail()){
                 $ample="large-8";
                }
            }
         ?> 
          <article id="post-<?php the_ID(); ?>" <?php post_class("$amplada columns $card_bgcolor"); ?>>
              <div class="row entry-body">
              <div class="<?php echo $ample;?> columns">
                   <header class="entry-header fullwidth">
                       <?php echo reactor_tumblog_icon(); ?>
                       <?php reactor_do_standard_header_titles(); ?>
                       <?php reactor_do_meta_autor_date(); ?>
                   </header>
                    <div class="entry-summary">
                    <?php (get_post_meta( get_the_ID(), '_bloc_html', true )!="on")? the_excerpt(): the_content();?>
                    </div>
              </div>
              
              <?php if (!$bloc_html && has_post_thumbnail() ) { ?>
                        <div class="large-3 columns">   
                            <?php reactor_do_standard_thumbnail(); ?>
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
