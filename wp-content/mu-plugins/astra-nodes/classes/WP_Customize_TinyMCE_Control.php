<?php

class WP_Customize_TinyMCE_Control extends WP_Customize_Control {

    public function __construct($manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {
        ?>
        <label>
            <span class="customize-control-title">
                <?php echo esc_html($this->label); ?>
            </span>

            <div class="customize-control-content">
                <textarea id="customize-control-<?= esc_attr($this->id) ?>"
                          class="customize-control-tinymce"
                          <?php $this->link(); ?>>
                    <?= esc_textarea($this->value()) ?>
                </textarea>
            </div>
        </label>
        <?php
    }

    public function enqueue(): void {
        wp_enqueue_script(
            'customizer-tinymce',
            get_site_url() . '/wp-content/mu-plugins/astra-nodes/customizer/js/customizer-tinymce.js',
            ['jquery', 'customize-controls'],
            false,
            true
        );
    }
}
