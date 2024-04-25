<?php

class WP_Customize_Layout_Control extends WP_Customize_Control {

    public function __construct($manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {
        $current_value = $this->value();

        echo '<div class="layout-control">';

        $i = 0;
        foreach ($this->choices as $value => $label) {
            $border = ($value === $current_value) ? '2px solid #000000;' : '1px solid #cccccc;';
            echo '<label for="' . $value . '">';
            echo '<div class="astra-nodes-layout-container" style="border:' . $border . '">';
            echo '<span class="astra-nodes-layout-item">'
            . $label
            . '<input type="radio" id="' . $value . '" name="layout" value="' . $value . '"';
            $this->link();
            checked($current_value, $value);
            echo '>';
            echo '<hr>';
            echo '<img src="'.WPMU_PLUGIN_URL.'/astra-nodes/images/layout_selector/layout_'.$i.'.png" alt="layout_'.$i.'.png">';
            echo '</span>';
            echo '</div>';
            echo '</label>';
            $i++;
        }
        echo '</div>';
    }

    public function enqueue(): void {
        wp_enqueue_style('astra-nodes-layout-css', content_url('mu-plugins/astra-nodes/styles/style.css'));
        wp_register_script('astra-nodes-layout-js', '', ['jquery'], '', true);
        wp_enqueue_script('astra-nodes-layout-js');
        wp_add_inline_script('astra-nodes-layout-js', "
            jQuery(document).ready(function() {
                jQuery('.astra-nodes-layout-container').on('click', function() {
                    jQuery('.astra-nodes-layout-container').css('border', '1px solid #cccccc');
                    jQuery(this).css('border', '2px solid #000000');
                });
            });
        ");
    }
}