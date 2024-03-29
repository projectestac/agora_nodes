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
    $url = parse_url($options['link_icon' . $icon_number]);

    // Check if link is a mail direction and assign target
    $result = xtec_mail_direction_into_header_icons($options, $icon_number);

    // Assign link and target
    $link = $result['link'];
    $target = $result['target'];

    $font_size = get_icon_font_size($options['title_icon' . $icon_number]);
    $title = $options['title_icon' . $icon_number];
    $icon = $options['icon' . $icon_number];

    echo '<div class="topicons small-3 large-4 columns">';
    echo '<button id="icon-' . $icon_number . '" title="' . $title . '" onclick="window.open(\'' . $link . '\', \'' . $target . '\')" class="dashicons dashicons-' . $icon . '" ' . $target . '>';
    echo '<span style="font-size: ' . $font_size . ';" class="text_icon">' . $title . '</span></button></div>';
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
    <div class="hide-for-small large-3 columns">
        <div class="row">
            <div class="box-title large-6 columns">
                <div class="box-content">
                    <div>
                        <a style="font-size:<?php echo reactor_option('tamany_font_nom'); ?>"
                           href="<?php echo home_url();?>">
                            <?php echo nl2br(get_option('nodesbox_name')); ?>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Caixa amb la descripció del centre -->
            <div class="box-description large-6 columns">
                <div class="box-content">
                    <div>
                        <?php if (reactor_option('blogdescription_link')) { ?>
                            <a style="font-size:<?php echo reactor_option('tamany_font_nom'); ?>"
                               href="<?php echo reactor_option('blogdescription_link'); ?>" >
                                <?php echo nl2br($description_text); ?>
                            </a>
                        <?php } else { ?>
                            <span style="font-size:<?php echo reactor_option('tamany_font_nom'); ?>">
                        <?php echo nl2br($description_text); ?>
                        </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box-image hide-for-small large-7 columns">
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
                <div class="box-content">
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

    <!-- Logo i nom per mobils -->
    <div class="small-12 columns box-titlemobile show-for-small">
        <div class="box-titlemobile-inner row">
            <div class="box-titlemobile-logo">
                <img src="<?php echo reactor_option('logo_image'); ?>">
            </div>
            <div class="box-titlemobile-schoolName">
                <a href="<?php echo home_url();?>">
                    <span><?php echo esc_attr(get_bloginfo('name', 'display')); ?></span>
                </a><br>
                <?php $addr = explode(" ", reactor_option("cpCentre"), 2);?>
                <span id="schoolCity"><?php echo $addr[1];?></span>
            </div>
        </div>
    </div>

    <!-- Graella d'icones -->
    <div id="box-grid" class="box-grid large-2 small-12 columns">
        <div class="box-content-grid row icon-box">
            <div class="topicons large-4 small-3 columns show-for-small">
                <?php

                // Get type contact by modify behavior
                $contacte_mobile = reactor_option('correuCentre');
                $correu_centre_enabled = false;
                if ( empty($contacte_mobile) ){
                    $contacte_mobile = reactor_option('contacteCentre');
                } else if( $contacte_mobile != '' ) {
                    $contacte_mobile = "mailto:" . $contacte_mobile;
                    $correu_centre_enabled = true;
                }

                // Get home url
                $currentDomain = get_home_url();
                $searchDomain = array('http://','https://');
                $currentDomain = str_replace($searchDomain,'',$currentDomain);
                $contacteDomain = str_replace($searchDomain,'',$contacte_mobile);

                if( $correu_centre_enabled === true ) {
                    ?>
                    <button id="icon-email" onclick="window.location.href='<?php echo $contacte_mobile; ?>'" class="dashicons dashicons-email">
                    <?php
                } elseif ( ! empty($contacte_mobile) ) {
                    if ( strpos($contacteDomain,$currentDomain) !== false ) {
                        ?>
                        <button id="icon-email" onclick="window.location.href='<?php echo $contacte_mobile; ?>'" class="dashicons dashicons-email">
                        <?php
                    } elseif ( strpos($contacte_mobile,'http') === false ) {
                        if ( strpos($contacte_mobile,'.') !== false ) {
                            ?>
                            <button id="icon-email" onclick="window.open('<?php echo "http://" . $contacte_mobile; ?>','_blank')" class="dashicons dashicons-email">
                            <?php
                        } else {
                            ?>
                            <button id="icon-email" onclick="window.location.href='<?php echo $currentDomain . $contacte_mobile; ?>'" class="dashicons dashicons-email">
                            <?php
                        }
                    } else {
                        ?>
                        <button id="icon-email" onclick="window.open('<?php echo $contacte_mobile; ?>','_blank')" class="dashicons dashicons-email">
                        <?php
                    }
                    ?>
                    <?php
                } else {
                    ?>
                    <button id="icon-email" class="dashicons dashicons-email">
                    <?php
                }
                ?>
                <span class="text_icon">Correu</span>
                </button>
            </div>

            <div class="topicons large-4 small-3 columns show-for-small">
                <button id="icon-phone" title="Trucar" onclick="window.location.href='tel:<?php echo reactor_option('telCentre'); ?>'" class="dashicons dashicons-phone">
                    <span class="text_icon"><?php echo reactor_option('telCentre'); ?></span>
                </button>
            </div>

            <?php
            show_header_icon($options, 11);
            show_header_icon($options, 12);
            ?>

            <div class="topicons small-3 large-4 columns">
                <button id="icon-13" class="dashicons dashicons-search" title="CERCA" onclick="cerca_toggle();">
                    <span class="text_icon">cerca</span>
                </button>
            </div>

            <div id="search-panel" class="small-12 large-12 columns">
                <form role="search" method="get" class="search-form" action="<?php echo get_home_url();?>">
                    <input type="search" class="search-field" placeholder="Cerca i pulsa enter…" value="" name="s" title="Cerca:">
                    <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;">
                </form>
            </div>

            <div class="topicons large-4 small-3 columns show-for-small">
                <?php
                // Get if GoogleMaps is empty or not by modify behavior
                $emptyMaps = reactor_option('googleMaps');
                if (!empty($emptyMaps)) {
                if (strpos(reactor_option('googleMaps'), $currentDomain) !== false) {
                ?>
                <button id="icon-maps" title="Mapa" onclick="window.location.href='<?php echo reactor_option('googleMaps'); ?>'"
                        class="dashicons dashicons-location-alt">
                    <?php
                    } else if (strpos(reactor_option('googleMaps'), 'http') === false) {
                    if (strpos(reactor_option('googleMaps'), '.') !== false) {
                    ?>
                    <button id="icon-maps" title="Mapa"
                            onclick="window.open('<?php echo "https://" . reactor_option('googleMaps'); ?>','_blank')"
                            class="dashicons dashicons-location-alt">
                        <?php
                        } else {
                        ?>
                        <button id="icon-maps" title="Mapa" onclick="window.location.href='<?php echo $currentDomain .
                                reactor_option('googleMaps'); ?>','_blank')" class="dashicons dashicons-location-alt">
                            <?php
                            }
                            } else {
                            ?>
                            <button id="icon-maps" title="Mapa"
                                    onclick="window.open('<?php echo reactor_option('googleMaps'); ?>','_blank')"
                                    class="dashicons dashicons-location-alt">
                                <?php
                                }
                                } else {
                                ?>
                                <button id="icon-maps" title="Mapa" class="dashicons dashicons-location-alt">
                                    <?php
                                    }
                                    ?>
                                    <span class="text_icon">Mapa</span>
                                </button>
            </div>
            <?php
            show_header_icon($options, 21);
            show_header_icon($options, 22);
            ?>

            <div class="topicons small-3 large-4 columns">
                <button id="icon-23" class="dashicons dashicons-menu"
                   title="MENU"
                   onclick="menu_toggle();">
                   <span class="text_icon">menú</span>
                </button>
            </div>
        </div>
    </div>

<?php }

add_action('reactor_header_inside', 'reactor_do_title_logo', 1);
