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

            $select_class = ($value === $current_value) ? 'box-selected' : 'box-unselected';
            echo '<label for="' . $value . '">';
            echo '<div class="astra-nodes-layout-container ' . $select_class . '">';

            // Title.
            echo '<div class="astra-nodes-layout-item">'
                . '<div class="layout-title">' . $label . '</div>'
                . '<input type="radio" id="' . $value . '" name="layout" value="' . $value . '"';
            $this->link();
            checked($current_value, $value);
            echo '>';

            // Separator.
            echo '<hr>';

            // Image.
            echo '<img class="astra-nodes-customizer-layout-images"
                       src="' . WPMU_PLUGIN_URL . '/astra-nodes/images/layout_' . $i . '.png" alt="layout_' . $i . '.png">';
            echo '</div>';
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
                    jQuery('.astra-nodes-layout-container').addClass('box-unselected').removeClass('box-selected');
                    jQuery(this).addClass('box-selected').removeClass('box-unselected');
                });
            });
        ");
    }

}