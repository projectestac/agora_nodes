<style>
<?php

    global $colors_nodes;

    $paleta = reactor_option('paleta_colors','blaus');

    $color_primary   = $colors_nodes[$paleta]["primary"];
    $color_secondary = $colors_nodes[$paleta]["secondary"];
    $color_footer    = isset($colors_nodes[$paleta]["footer"])?$colors_nodes[$paleta]["footer"]:$color_secondary;
    $color_link      = isset($colors_nodes[$paleta]["link"])?$colors_nodes[$paleta]["link"]:$color_secondary;
    $color_icon22    = isset($colors_nodes[$paleta]["icon22"])?$colors_nodes[$paleta]["icon22"]:$color_secondary;
    $color_calendar  = isset($colors_nodes[$paleta]["calendar"])?$colors_nodes[$paleta]["calendar"]:$color_secondary;
    $color_mobile    = isset($colors_nodes[$paleta]["mobile"])?$colors_nodes[$paleta]["mobile"]:$color_secondary;

    ?>
    .box-title{
        background-color:<?php echo $color_primary;?>
    }
    .box-description{
        background-color:<?php echo $color_secondary; ?>
    }
    #icon-11, #icon-23{
        background-color:<?php echo $color_secondary;?>
    }
    #icon-21, #icon-13{
        background-color:<?php echo $color_primary;?>
    }
    button#icon-22 {
        color:<?php echo $color_icon22;?> !important;
    }
    /** 2015.11.13 @nacho: Display correct color for arrows on SideMenuWalker Menu**/
    h1, h2, h3, h4, h5, h6, a, .dropDown.dashicons {
        color: <?php echo $color_link;?>  !important;
    }
    #menu-panel {
            border-bottom: 2px solid <?php echo $color_secondary;?>
    }

    .entry-comments,
    .entry-categories>a,
    .entry-tags >a {
        color: <?php echo $color_secondary;?>  !important;
    }
    .entry-comments:before,
    .entry-categories:before,
    .entry-tags:before{
            color: <?php echo $color_secondary;?>
     }
    .menu-link, .sub-menu-link {
            color: <?php echo $color_secondary;?> !important;
    }
    .gce-today span.gce-day-number{
        border: 3px solid <?php echo $color_calendar;?>!important;
    }
    .gce-widget-grid .gce-calendar th abbr,
    .simcal-week-day {
        color: <?php echo $color_calendar;?>
    }
    .button {
        color: <?php echo $color_primary;?> !important;
    }
    .button:hover {
        background-color:<?php echo $color_primary;?> !important;
        color:white !important;
    }

    #footer {
        background-color: <?php echo $color_footer;?>
    }
   <?php

    $options = get_option('my_option_name');
    if ($options['show_text_icon']!="si"){
        echo ".text_icon{
                    display:none !important;
             }";
    }
    ?>

    @media screen and (max-width: 48.063em) {
        #icon-email{
            background-color:<?php echo $color_mobile?>;
            opacity: 1;
        }
        #icon-maps{
           background-color:<?php echo $color_mobile?>;
            opacity: 0.8;
        }
        #icon-phone{
           background-color:<?php echo $color_mobile?>;
            opacity: 0.5;
        }
        #icon-11{
            background-color:<?php echo $color_mobile?>;
            opacity: 0.8;
        }
        #icon-12{
           background-color:<?php echo $color_mobile?> !important;
            opacity: 0.5;
        }
        #icon-13{
           background-color:<?php echo $color_mobile?>;
            opacity: 1;
        }
        #icon-21{
            background-color:<?php echo $color_mobile?> !important;
            opacity: 0.5;
        }
        #icon-22{
           background-color:<?php echo $color_mobile?> !important;
           opacity: 1;
        }
        button#icon-22{
            color:white !important;
        }
        #icon-23{
           background-color:<?php echo $color_mobile?>;
            opacity: 0.8;
        }
    }

    /* ESTILS WIDGET CALENDAR */
    .widget span.simcal-no-events, .simcal-events-dots b{
        display: none !important;
    }

    .widget div.simcal-calendar table.simcal-calendar-grid tr.simcal-week td div span.simcal-events-dots b, .widget .simcal-default-calendar-grid .simcal-events-dots{
        display: inline !important;
    }

    .widget .simcal-events-dots b{
        color: <?php echo $color_calendar ?> !important;
    }

    .widget .simcal-day-label.simcal-day-number{
        background-color: #e7e7e7 !important;
        color: #59544E !important;
    }

    .widget tr.simcal-week{
        background-color: #e7e7e7;
    }

    .widget tr.simcal-week td{
        border: 1px solid white !important;
    }

    .widget .simcal-day-void{
        background-color: #f5f5f5 !important;
    }

    .widget tr.simcal-week div{
        padding-top: 5px !important;
        background-color: #e7e7e7;

    }

    .widget .simcal-current-month, 
    .widget .simcal-current-year{
        text-transform: capitalize;
        font-size: 0.7em;
        color: #59544E !important;
        font-weight: bold;
        line-height: 1em;
        margin-top: -7px !important;
    }

    #sidebar-frontpage-2 .widget .simcal-current-month, 
    #sidebar-frontpage-2 .widget .simcal-current-year{
        font-size: 0.6em !important;

    }

    .widget .simcal-today > div{
        border: none !important;
    }

    .widget .simcal-today > div > span.simcal-day-label.simcal-day-number{
        border: none !important;
        background-color: transparent !important;
    }

    .widget .simcal-today > div > span.simcal-day-label.simcal-day-number{
        border: 2px solid <?php echo $color_calendar ?> !important;
        border-radius: 15px;
        margin: 0 auto;
        width: 23px !important;
        padding: 2px !important;
        padding-bottom: 3px !important;
    }

    .widget .simcal-default-calendar-grid .simcal-day-number{
        padding: 0px !important;
    }


    .widget .simcal-default-calendar-grid .simcal-calendar-head .simcal-nav{
        padding: 5px !important;
    }

    #sidebar-frontpage-2 .widget .simcal-default-calendar-grid .simcal-calendar-head .simcal-nav{
        padding: 2px !important;
    }

    #sidebar-frontpage-2 .simcal-calendar-head h3,
    #sidebar-frontpage .simcal-calendar-head h3{
        margin-top: -7px;
    }


    /* FI ESTILS WIDGET CALENDAR */

    <?php echo get_option( 'common_css', '' );?>
    <?php echo get_option( 'school_css', '' );?>

</style>
