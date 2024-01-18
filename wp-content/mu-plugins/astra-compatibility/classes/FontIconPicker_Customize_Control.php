<?php

class FontIconPicker_Customize_Control extends WP_Customize_Control {

    public $type = 'fonticonpicker';

    public function render_content(): void {
        ?>

 
 
         <?php
    }

    public function enqueue(): void {
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
        wp_enqueue_script('universal-icon-picker', WPMU_PLUGIN_URL . '/astra-lib/universal-icon-picker/assets/js/universal-icon-picker.min.js',
            [], null, true);

    }

}