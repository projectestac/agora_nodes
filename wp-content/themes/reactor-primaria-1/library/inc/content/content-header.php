<?php
/**
 * Header Content
 * hook in the content for header.php
 *
 * @package Reactor / Nodes
 * @author Xavier Meler <jmeler@xtec.cat>
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Site meta, title, and favicon
 * in header.php
 *
 * @since 1.0.0
 */
function reactor_do_reactor_head()
{ ?>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <title><?php wp_title('|', true, 'right'); ?></title>

    <!-- google chrome frame for ie -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <!-- mobile meta -->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <?php $favicon_uri = reactor_option('favicon_image') ? reactor_option('favicon_image') : get_template_directory_uri() . '/favicon.ico'; ?>
    <link rel="shortcut icon" href="<?php echo $favicon_uri; ?>">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

<?php
}

add_action('wp_head', 'reactor_do_reactor_head', 1);

/**
 * @param $options where the icon info is stored
 * @param $icon_number number of the icon to be used
 */
function show_header_icon($options, $icon_number) {
    $url = parse_url($options['link_icon'.$icon_number]);
    if ( ($url['scheme'] == 'https') || ($url['scheme'] == 'http') ){
        $link = $options['link_icon'.$icon_number];
        $target = set_target($link);
    }else {
        $link = get_home_url()."/".$options['link_icon'.$icon_number];
        $target = "";
    }
    $font_size = get_icon_font_size( $options['title_icon'.$icon_number]);
    $title = $options['title_icon'.$icon_number];
    $icon = $options['icon'.$icon_number];
    echo '<div id="icon-'.$icon_number.'">';
    echo '<a title="'.$title.'" href="'.$link.'" class="dashicons dashicons-'.$icon.'" '.$target.'>';
    echo '<span style="font-size: ' . $font_size . ';" class="text_icon">'.$title.'</span></a></div>';
}

/**
 * Top bar
 * in header.php
 *
 * @since 1.0.0
 */
function reactor_do_top_bar()
{
    if (has_nav_menu('top-bar-l') || has_nav_menu('top-bar-r')) {
        $topbar_args = array(
            'title' => reactor_option('topbar_title', get_bloginfo('name')),
            'title_url' => reactor_option('topbar_title_url', home_url()),
            'fixed' => reactor_option('topbar_fixed', 0),
            'contained' => reactor_option('topbar_contain', 1),
        );
        reactor_top_bar($topbar_args);
    }
}

add_action('reactor_header_before', 'reactor_do_top_bar', 1);

function reactor_do_title_logo()
{
    $description_text = get_description_text();
    $description_font_size = get_description_font_size($description_text);
    $options = get_option('my_option_name');
    ?>

    <!-- Caixa amb el nom del centre -->
    <div class='box-title hide-for-small'>
        <div class='box-content'>
            <div>
                <a style="font-size:<?php echo reactor_option('tamany_font_nom');?>"
                   href="<?php echo home_url();?>">
                    <?php echo nl2br(get_option('nodesbox_name')); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Logo i nom per mobils -->
    <div class='box-titlemobile show-for-small'>
        <div class="box-titlemobile-inner row">
            <div class="box-titlemobile-logo">
                <img src="<?php echo reactor_option('logo_image'); ?>">
            </div>
            <div class="box-titlemobile-schoolName">
                <a href="<?php echo home_url();?>">
                    <span><?php echo esc_attr(get_bloginfo('name', 'display')); ?></span>
                </a><br>
                <?php $addr = explode(" ",reactor_option("cpCentre"),2);?>
                <span id="schoolCity"><?php echo $addr[1];?></span>
            </div>
        </div>
    </div>

    <!-- Caixa amb la descripció del centre -->
    <div class='box-description hide-for-small'>
        <div class='box-content'>
            <div>
            <span style="font-size:<?php echo $description_font_size;?>">
            <?php echo nl2br($description_text); ?>
            </span>
            </div>
        </div>
    </div>

    <div class='box-image hide-for-small'>
        <!-- Imatge/Carrusel -->
        <?php
        /**
         * @nacho: 2015
         *
         * Check if page is a category
         * - If category has an image assigned, load it
         * - If has not been assigned an image, the header can be an image or a carrusel
         * Page is not a category
         * - Header can be an image or a carrusel
         */
        if (is_category()){
            $image = get_category_image();
            if (!empty($image)){ ?>
                <div class='box-content'>
                    <div class='CoverImage FlexEmbed FlexEmbed--3by1'
                         style="background-image:url(<?php echo $image;?>)">
                    </div>
                </div>
                <?php
            }else {
                if(reactor_option('imatge_capcalera')) { ?>
                    <div class='box-content'>
                        <div class='CoverImage FlexEmbed FlexEmbed--3by1'
                             style="background-image:url(<?php echo reactor_option('imatge_capcalera'); ?>)">
                        </div>
                    </div>
                    <?php
                } else { ?>
                    <div class='box-content-slider'>
                        <?php do_action('slideshow_deploy', reactor_option('carrusel')); ?>
                    </div>
                <?php }
            }
        }else {
            if (reactor_option('imatge_capcalera')) { ?>
                <div class='box-content'>
                    <div class='CoverImage FlexEmbed FlexEmbed--3by1'
                         style="background-image:url(<?php echo reactor_option('imatge_capcalera'); ?>)">
                    </div>
                </div>
                <?php
            } else { ?>
                <div class='box-content-slider'>
                    <?php do_action('slideshow_deploy', reactor_option('carrusel')); ?>
                </div>
            <?php }
        }?>
    </div>

    <!-- Graella d'icones -->
    <div id="box-grid" class='box-grid'>
        <div class='box-content-grid'>
            <div id="icon-email" class="topicons show-for-small">
                <a href="mailto:<?php echo reactor_option('emailCentre'); ?>" class="dashicons dashicons-email"></a>
                <span class="text_icon">Correu</span>
            </div>
            <div id="icon-maps" class="topicons show-for-small">
                <a title="Mapa" href="<?php echo reactor_option('googleMaps'); ?>" class="dashicons dashicons-location-alt"></a>
                <span class="text_icon">Mapa</span>
            </div>
            <div id="icon-phone" class="topicons show-for-small">
                <a title="Trucar" href="tel:<?php echo reactor_option('telCentre'); ?>" class="dashicons dashicons-phone"></a>
                <span class="text_icon"><?php echo reactor_option('telCentre'); ?></span>
            </div>
            <?php
            show_header_icon($options, 11);
            show_header_icon($options, 12);
            ?>
            <div id="icon-13">
                <a class="dashicons dashicons-search" title="CERCA" href="javascript:void(0);" onclick='cerca_toggle();'>
                    <span class="text_icon">cerca</span>
                </a>
            </div>
            <div id="search-panel">
                <form role="search" method="get" class="search-form" action="<?php echo get_home_url();?>">
                    <input type="search" class="search-field" placeholder="Cerca i pulsa enter…" value="" name="s" title="Cerca:">
                    <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;">
                </form>
            </div>
            <?php
            show_header_icon($options, 21);
            show_header_icon($options, 22);
            ?>
            <div id="icon-23">
                <a class="dashicons dashicons-menu"
                   title="MENU"
                   href="javascript:void(0);"
                   onclick='menu_toggle();'>
                   <span class="text_icon">menú</span>
                </a>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>

<?php }

add_action('reactor_header_inside', 'reactor_do_title_logo', 1);
