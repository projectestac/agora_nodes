<?php

class WP_Customize_TinyMCE_Control extends WP_Customize_Control
{

    public function __construct($manager, $id, $args = [])
    {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void
    {
        // Generate a clean ID, without brackets, so that TinyMCE doesn't break.
        $safe_id = 'tinymce_' . md5($this->id);
        ?>
        <label>
            <span class="customize-control-title">
                <?= esc_html($this->label) ?>
            </span>

            <span class="customize-control-content">
                <textarea id="<?= esc_attr($safe_id) ?>"
                          class="customize-control-tinymce"
                          <?php $this->link(); ?>
                          ><?= esc_textarea($this->value()) ?></textarea>
            </span>
        </label>
        <?php
    }

    public function enqueue(): void
    {
        wp_enqueue_editor();

        wp_enqueue_script(
            'customizer-tinymce',
            get_site_url() . '/wp-content/mu-plugins/astra-nodes/customizer/js/customizer-tinymce.js',
            ['jquery', 'customize-controls'],
            false,
            true
        );

        // These styles force the TinyMCE modal window to go past the Customizer.
        wp_add_inline_style(
                'customize-controls',
                '.mce-window, .mce-window-backdrop, .tox-dialog-wrap { z-index: 500000 !important; }'
        );
    }
}
