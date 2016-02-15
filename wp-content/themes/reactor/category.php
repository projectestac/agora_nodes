<?php
/**
 * The template for displaying posts by category
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>
<?php
    global $categoria;
    $categoria=  get_query_var('cat');
?>

<?php get_header(); ?>

	<div id="primary" class="site-content cat">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
		
		<?php get_sidebar("category"); ?>                    

        <div class="articles <?php reactor_columns(); ?>">
        
                <?php reactor_inner_content_before(); ?>

                    <?php // show an optional category description 
                    if ( category_description() ) : ?>
                        <header class="archive-header">
                       <div class="archive-meta">
                       <?php echo category_description(); ?>
                       </div>
                       </header><!-- .archive-header -->

                    <?php endif; ?>

                    <?php
                        // TODO: get values from tag settings
                        $cat_meta = get_option( "category_$categoria");
                        if (!isset($cat_meta ['articles_fila']) ||
                            $cat_meta ['articles_fila'] < 1 ||
                            $cat_meta ['articles_fila'] > 4) {
                            $posts_per_fila = 2;
                        } else {
                            $posts_per_fila=$cat_meta ['articles_fila'];
                        }

                        $posts_per_fila1 = $posts_per_fila2 = $posts_per_filan = $posts_per_fila;

                        reactor_loop_before();
                        get_template_part('loops/loop', 'taxonomy');
                        reactor_loop_after();
                    ?>

                    <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>