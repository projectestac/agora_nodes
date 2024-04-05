<?php

class WP_Customize_Palette_Control extends WP_Customize_Control {

    public function __construct($manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {

        $astra_color_palettes = get_option('astra-color-palettes');
        $palettes = $astra_color_palettes['palettes'];

        echo '<div class="palette-control">';
        echo '<div class="ast-color-palette-container">';

        foreach ($palettes as $name => $palette) {
            echo '';
            echo '
                 <div class="" style="width:125px; float: left; border: 1px solid #cccccc;">
                    <div class="" style="width:23%; height:48px; display:inline-block; background-color:' . $palette[0] . ';"></div>
                    <div class="" style="width:23%; height:48px; display:inline-block; background-color:' . $palette[1] . ';"></div>
                    <div class="" style="width:23%; height:48px; display:inline-block; background-color:' . $palette[2] . ';"></div>
                    <div class="" style="width:23%; height:48px; display:inline-block; background-color:' . $palette[3] . ';"></div>
                    <span class="ast-palette-label-wrap">'
                    . $name . '<input type="radio" name="palette" value="' . $name . '" style="" data-customize-setting-link="astra-color-palettes[currentPalette]">
                    </span>
                </div>
        ';
        }

        echo '</div>';
        echo '</div>';

    }

    public function enqueue(): void {
//        wp_enqueue_style('astra-css', content_url('wp-content/mu-plugins/astra-nodes/styles/style.css'));
    }

}
