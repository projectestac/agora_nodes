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
                    // Get the sticky posts on top
                    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
                    $tempquery = $wp_query;

                    $args = array(
                       'post_type'      => 'post',
                       'posts_per_page' => 10,
                       'paged' => $paged
                    );
                    if (get_query_var('category__and')){
                        $args['category__and'] = get_query_var('category__and');
                    }
                    if (get_query_var('category__in')){
                        $args['category__in'] = get_query_var('category__in');
                    }

                    $wp_query = new WP_Query( $args );

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

                    wp_reset_postdata();
                    $wp_query = $tempquery;
                    ?>

                    <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->

                <?php get_sidebar("category"); ?>
                                
            </div><!-- .row -->

        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>