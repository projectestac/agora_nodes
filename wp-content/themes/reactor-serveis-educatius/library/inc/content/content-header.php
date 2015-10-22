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
        </div>

        <!-- Caixa amb la descripció del centre -->
        <div class="small-3 columns">
            <div class='box-description hide-for-small'>
                <div class='box-content'>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" >
                        <span style="font-size:<?php echo $description_font_size; ?>">
                            <img src="<?php echo reactor_option('logo_image'); ?>">
                            <?php echo esc_url( home_url( '/' ) ); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Imatge/Carrusel -->
        <div class="small-9 columns">
            <div class='box-image hide-for-small'>
                <?php
                if (reactor_option('imatge_capcalera')) : ?>
                    <div class='box-content'>
                        <div class='CoverImage FlexEmbed FlexEmbed--3by1'
                             style="background-image:url(<?php echo reactor_option('imatge_capcalera'); ?>)">
                        </div>
                    </div>
                <?php else:  ?>
                    <div class='box-content-slider'>
                        <?php do_action('slideshow_deploy', reactor_option('carrusel')); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
<?php }

add_action('reactor_header_inside', 'reactor_do_title_logo', 1);
