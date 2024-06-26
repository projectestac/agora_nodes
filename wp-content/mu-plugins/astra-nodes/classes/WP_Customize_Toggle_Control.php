<?php

class WP_Customize_Toggle_Control extends WP_Customize_Control {

    public $type = 'toggle_checkbox';

    public function render_content(): void {

        $id = esc_attr($this->id);
        ?>
        <div class="checkbox_switch">
            <div class="onoffswitch">
                <input type="checkbox" class="onoffswitch-checkbox" id="<?= $id ?>" name="<?= $id ?>"
                       value="<?= esc_attr($this->value()) ?>" <?php $this->link(); checked($this->value()); ?>>
                <label class="onoffswitch-label" for="<?= $id ?>"></label>
            </div>
            <span class="customize-control-title onoffswitch_label"><?= esc_html($this->label) ?></span>
        </div>
        <?php
    }

}
