<?php
/**
 * The template for displaying archive results
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="articles <?php reactor_columns(); ?>">
                
                <?php reactor_inner_content_before(); ?>
                
                <?php if ( have_posts() ) : ?>
                       
                        <?php reactor_loop_before(); ?>

                         <?php get_template_part('loops/loop', 'taxonomy'); ?>

                         <?php reactor_loop_after(); ?>

                         <?php // if no posts are found
                         else : reactor_loop_else(); ?>
				
			<?php endif; // end have_posts() check ?> 
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
