<?php
class themeslug_walker_nav_menu extends Walker_Nav_Menu {

    // add classes to ul sub-menus
    function start_lvl( &$output, $depth = 0, $args = array() ) {

        // depth dependent classes
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
        $display_depth = ( $depth + 1); // because it counts the first submenu as 0
        $classes = array(
            'sub-menu',
            ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
            ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
            'menu-depth-' . $display_depth
            );
        $class_names = implode( ' ', $classes );

        // build html
        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
    }


    // add main/sub classes to li's and links
     function start_el(  &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

        global $wp_query;

        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

        // depth dependent classes
        $depth_classes = array(
            ( $depth ==0 ? 'main-menu-item' : 'sub-menu-item' ),
            ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        // passed classes
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        // build html
        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

        // link attributes
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ';
        $attributes .= ($item->url=='#' ? 'no-link ' : '');
        $attributes .= ($depth > 0 ? 'sub-menu-link' : 'main-menu-link') . '"';

        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
            $args->before,
            $attributes,
            $args->link_before,
            apply_filters( 'the_title', $item->title, $item->ID ),
            $args->link_after,
            $args->after
        );

        // build html
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
function menu_principal(){
	$defaults = array(
	    'theme_location' => "main-menu",
	    'echo'            => true,
	    'container'       => FALSE,
	'container_id'    => FALSE,
	'menu_class'      => 'menu-principal',
	'menu_id'         => FALSE,
	'depth'           => 4,
	'walker'          => new themeslug_walker_nav_menu
	);

    echo '<input type="checkbox" id="check-menu-responive" name="check-menu-responive" class="hide"/>';
    echo "<div id='menu-panel' class='row'>";
        echo '<div class="etiquetes-header large-3 small-12 columns">';
            menu_etiquetes();
        echo "</div>";
        echo '<div class="menu-header large-9 small-0 columns">';
            wp_nav_menu( $defaults);
        echo "</div>";
	echo "</div>";
}


function menu_etiquetes() {
    $description_text = get_description_text();
    $options = get_option('my_option_name');
?>
<div id="sidebar-frontpage" class="" role="complementary">
    <div id="sidebar-frontpage-inner">
        <!-- Graella d'icones -->
            <div class='box-content-grid'>
                <?php for($i = 1;$i <= 5; $i++): ?>
                    <div id="icon-<?php echo $i; ?>">
                        <a title="<?php echo $options['title_icon'.$i];?>"
                           href="<?php echo $options['link_icon'.$i];?>"
                           class="dashicons dashicons-<?php echo $options['icon'.$i];?>"
                            <?php echo set_target($options['link_icon'.$i]);?>>
                            <span style="font-size:0.8em !important;" class="text_icon"><?php echo $options['title_icon'.$i];?> </span>
                        </a>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
</div><!-- #sidebar-frontpage -->
<?php
}