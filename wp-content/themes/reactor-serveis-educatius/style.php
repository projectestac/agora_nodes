<style>
<?php
    
    global $colors_nodes;
    
    $paleta = reactor_option('paleta_colors','blaus');

    function darken_color($rgb, $darker=2) {
        $hash = (strpos($rgb, '#') !== false) ? '#' : '';
        $rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
        if(strlen($rgb) != 6) return $hash.'000000';
        $darker = ($darker > 1) ? $darker : 1;

        list($R16,$G16,$B16) = str_split($rgb,2);

        $R = sprintf("%02X", floor(hexdec($R16)/$darker));
        $G = sprintf("%02X", floor(hexdec($G16)/$darker));
        $B = sprintf("%02X", floor(hexdec($B16)/$darker));

        return $hash.$R.$G.$B;
    }

    $color_primary   = $colors_nodes[$paleta]["primary"];
    $color_secondary = $colors_nodes[$paleta]["secondary"];
    $color_tertiary = isset($colors_nodes[$paleta]["tertiary"]) ? $colors_nodes[$paleta]["tertiary"] : darken_color($color_secondary, 1.35);
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

   #icon-1, #icon-4 {
       /*background-color: #00688B;*/
       background-color: <?php echo $color_tertiary;?>;
   }

    #icon-2, #icon-5 {
        background-color: <?php echo $color_primary;?>;
    }

    .box-content-grid #icon-3 > a{
        color: <?php echo $color_tertiary;?> !important;
    }

    .box-image {
        background-color: <?php echo $color_secondary;?>;
    }
    
    .menu-item-depth-0:hover {
        background-color: <?php echo $color_tertiary;?>;
    }

    .menu-item-depth-0:hover ul.menu-depth-1 {
        /*background-color: #00688B;*/
        background-color: <?php echo $color_tertiary;?>;
    }

    h1, h2, h3, h4, h5, h6, a, .dropDown.dashicons{
        color: <?php echo $color_link;?>  !important;
    }

/*    #menu-panel {*/
/*            background-color: */<?php //echo $color_secondary;?>/*;*/
/*            color: white;*/
/*    }*/

    #menu-panel .menu-header {
        background-color: <?php echo $color_secondary;?>;
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
/*            color: */<?php //echo $color_secondary;?>/* !important;*/
            color: white !important;
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
        .box-titlemobile {
            background-color: <?php echo $color_secondary;?>;
        }
    }

    @media screen and (max-width: 782px) {
        html.js {
            margin-top: 32px !important;
        }
    }

    @media screen and (max-width: 767px) {
        #main #menu-panel .open-menu-principal li {
            background-color: <?php echo $color_tertiary;?>;
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

    .widget .simcal-current-month, .simcal-current-year{
        text-transform: capitalize;
        font-size: 0.8em;
        color: #59544E !important;
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
        margin-top: -2px;
        width: 24px !important;
        padding: 3px !important;
        padding-bottom: 3px !important;
    }


    .widget .simcal-default-calendar-grid .simcal-day-number{
        padding: 0px !important;
    }
    /* FI ESTILS WIDGET CALENDAR */

    <?php echo get_option( 'common_css', '' );?>
    <?php echo get_option( 'school_css', '' );?>
    
</style>
