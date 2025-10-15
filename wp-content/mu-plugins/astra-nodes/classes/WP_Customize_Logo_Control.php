<?php

class WP_Customize_Logo_Control extends WP_Customize_Control {

    public function __construct($manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {
        $current_value = $this->value();

        echo '<div class="logo-control">';
        echo '<span class="customize-control-title">' . $this->label . '</span>';

        foreach ($this->choices as $value => $label) {
            $image_name = ($value === 'department') ? 'logo_defp_transparent.png' : 'logo_ceb.png';
            $select_class = ($value === $current_value) ? 'box-selected' : 'box-unselected';
            echo '<label for="' . $value . '">';
            echo '<div class="astra-nodes-logo-select-option ' . $select_class . '">';
            echo '<span class="astra-nodes-logo-select-item">'
                . '<input type="radio" id="' . $value . '" name="logo" value="' . $value . '"';
            $this->link();
            checked($current_value, $value);
            echo '>';
            echo '<img src="' . WPMU_PLUGIN_URL . '/astra-nodes/images/' . $image_name . '" alt="">';
            echo '</span>';
            echo '</div>';
            echo '</label>';
        }
        echo '</div>';
    }

    public function enqueue(): void {
        wp_register_script('astra-nodes-layout-js', '', ['jquery'], '', true);
        wp_enqueue_script('astra-nodes-layout-js');
        wp_add_inline_script('astra-nodes-layout-js', "
            jQuery(document).ready(function() {
                jQuery('.astra-nodes-logo-select-option').on('click', function() {
                    jQuery('.astra-nodes-logo-select-option').addClass('box-unselected').removeClass('box-selected');
                    jQuery(this).addClass('box-selected').removeClass('box-unselected');
                });
            });
        ");
    }
}