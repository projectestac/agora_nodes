<?php

class WP_Customize_Custom_Palette_Control extends WP_Customize_Control {

    public function __construct($manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {

        $key_to_preserve = 'Personalitzada';

        $astra_color_palettes = get_option('astra-color-palettes');
        $palettes = $astra_color_palettes['palettes'];
        $currentPalette = $astra_color_palettes['currentPalette'];

        foreach ($palettes as $name => $palette) {
            if ($name !== $key_to_preserve) {
                unset($palettes[$name]);
            }
        }

        echo '<div class="palette-control">';
        echo '<div class="ast-color-palette-container">';

        foreach ($palettes as $name => $palette) {
            $select_class = ($name === $currentPalette) ? 'box-selected' : 'box-unselected';
            echo '<label for="' . $name . '">';
            echo '<div class="astra-nodes-palette-container ' . $select_class . '">';
            echo '<div class="astra-nodes-palette-item" style="background-color:' . $palette[0] . ';"></div>';
            echo '<div class="astra-nodes-palette-item" style="background-color:' . $palette[1] . ';"></div>';
            echo '<span class="astra-nodes-palette-footer">'
                . $name
                . '<input type="radio" id="' . $name . '" name="palette" value="' . $name . '" data-customize-setting-link="astra-color-palettes[currentPalette]">';
            echo '</span>';
            echo '</div>';
            echo '</label>';
        }

        echo '</div>';
        echo '</div>';

    }
}
