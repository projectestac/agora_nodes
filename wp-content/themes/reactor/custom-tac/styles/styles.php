<style>
<?php
    global $colors_nodes;

    $paleta = reactor_option('paleta_colors','blaus');

    $color_calendar  = isset($colors_nodes[$paleta]["calendar"])?$colors_nodes[$paleta]["calendar"]:$colors_nodes[$paleta]["secondary"];
?>

    /* ESTILS WIDGET CALENDAR */
    .widget .simcal-events-dots b{
        color: <?php echo $color_calendar ?> !important;
    }

    .widget .simcal-today > div > span.simcal-day-label.simcal-day-number{
        border: 2px solid <?php echo $color_calendar ?> !important;
    }
    /* FI ESTILS WIDGET CALENDAR */
</style>