<?php
/**
 * Template Name: Portada
 *
 * @package Reactor
 * @subpackge Page-Templates
 * @since 1.0.0
 * @author Xavier Meler <jmeler@xtec.cat>
 */
?>
<?php // get the options
function has_left_bar($frontpage_layout) {
    return $frontpage_layout == '2c-l' || $frontpage_layout == '3c-c';
}

function has_right_bar($frontpage_layout) {
    return $frontpage_layout == '2c-r' || $frontpage_layout == '3c-c';
}

function get_columns($frontpage_layout) {
    if ($frontpage_layout == '2c-l') {
        return 9;
    }
    if ($frontpage_layout == '2c-r') {
        return 10;
    }
    if ($frontpage_layout == '3c-c') {
        return 7;
    }
    return 12;
}

global $number_posts;
global $categoria;
global $posts_per_fila1;
global $posts_per_fila2;
global $posts_per_filan;
$categoria        = reactor_option('frontpage_post_category', '1');
$number_posts     = reactor_option('frontpage_number_posts', 10);
$frontpage_layout = reactor_option('frontpage_layout');
get_header();

?>
<div id="primary" class="site-content">
<?php reactor_content_before(); ?>
<div id="content" role="main">
    <div class="row">
        <?php
        $columns = get_columns($frontpage_layout);
        $push_columns = has_left_bar($frontpage_layout)? 3 : 0;
        if ($columns < 12) { ?>
            <div class="show-for-small">
                <span style="float:left; margin:1em;"><a href="#sidebars_a"><span class='dashicons dashicons-arrow-down-alt2'></span> Ginys</a></span>
            </div>
        <?php } ?>
        <!-- Contingut central -->
        <div id="contingut_central_frontpage" class="articles <?php reactor_columns($columns, true, false, null, $push_columns); ?>" >
        <?php reactor_inner_content_before(); ?>
        <?php
            // Consulta per obtenir el contingut de la pagina principal
            $args = array(
                'post_type'           => 'page',
                'page_id'             => reactor_option('frontpage_page'));

            $wp_frontpage = new WP_Query( $args );
            $wp_frontpage->the_post();

            if (strlen(trim(get_the_content()))) {
                get_template_part('post-formats/format', 'page');
            }
            wp_reset_postdata();
            $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
            //Es necessari utilitzar $wp_query per tenir paginació
            $temp = $wp_query;

            //Articles. No es pot establir la categoria directament perquè perdem els stickys
            $args = array(
                'post_type'           => 'post',
                'posts_per_page'      => $number_posts,
                'paged'               => $paged );
            $wp_query = new WP_Query( $args );

            //action: filter_by_categoria
            $posts_per_filan = reactor_option('frontpage_posts_per_fila_n', 2);
            if ($paged == 1){
                $posts_per_fila1 = reactor_option('frontpage_posts_per_fila_1', 2);
                $posts_per_fila2 = reactor_option('frontpage_posts_per_fila_2', 2);
            } else{
                $posts_per_fila1 = $posts_per_fila2 = $posts_per_filan;
            }
            reactor_loop_before();
            get_template_part('loops/loop', 'taxonomy');
            reactor_loop_after();
            wp_reset_postdata();
            $wp_query = $temp;
            reactor_inner_content_after();
        ?>
        </div><!-- .columns --><!--Contingut central -->
        <?php
            if ($columns < 12) {
                echo '<span id="sidebars_a"></span>';
                // Barra esquerra si aplica
                if (has_left_bar($frontpage_layout)) {
                    get_sidebar('frontpage');
                }
                // Barra dreta si aplica
                if (has_right_bar($frontpage_layout)) {
                    get_sidebar('frontpage-2');
                }
            }
        ?>
    </div><!-- .row -->
</div><!-- #content -->
<?php reactor_content_after(); ?>
</div><!-- #primary -->
<?php get_footer(); ?>
