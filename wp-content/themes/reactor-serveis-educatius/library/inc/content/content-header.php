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
    <div class="row">
        <!-- Logo i nom per mobils -->
        <div class='box-titlemobile show-for-small'>
            <a href="javascript:void" class="menu-responsive dashicons dashicons-menu"></a>
            <div class="box-titlemobile-inner row">
                <div class="box-titlemobile-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" >
                        <img src="<?php echo reactor_option('logo_image'); ?>">
                    </a>
                </div>
            </div>
            <div class="responsive-search-form">
                <span class="dashicons dashicons-search"></span>
                <form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>">
                        <input type="text" value="<?php get_search_query(); ?>" name="s" id="s" placeholder="" />
                        <input class="button prefix" type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search', 'reactor'); ?>" />
                </form>
            </div>
        </div>

        <!-- Caixa amb la descripció del centre -->
        <div class="small-3 columns">
            <div class='box-description hide-for-small'>
                <div class='box-content'>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" >
                        <span style="font-size:<?php echo $description_font_size; ?>">
                            <img src="<?php echo reactor_option('logo_image'); ?>">
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Imatge/Carrusel -->
        <div class="small-9 columns">
            <div class='box-image hide-for-small'>
                <!-- Imatge/Carrusel -->
                <?php
                /**
                 * @author: Nacho Abejaro
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
        </div>

        <div style="clear:both"></div>
    </div>
<?php }

add_action('reactor_header_inside', 'reactor_do_title_logo', 1);
