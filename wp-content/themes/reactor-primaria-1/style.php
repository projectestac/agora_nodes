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
    #icon-22 a {
        color:<?php echo $color_icon22;?> !important;
    }
    h1, h2, h3, h4, h5, h6, a {    
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
    .gce-widget-grid .gce-calendar th abbr {
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
        #icon-22 a{
            color:white !important;
        }
        #icon-23{
           background-color:<?php echo $color_mobile?>;
            opacity: 0.8;
        }
    }
    
    <?php echo get_option( 'common_css', '' );?>
    <?php echo get_option( 'school_css', '' );?>
    
</style>
