<?php

class WP_Customize_Toggle_Control extends WP_Customize_Control {

    public $type = 'toggle_checkbox';

    public function render_content(): void {
        ?>
        <div class="checkbox_switch">
            <div class="onoffswitch">
                <input type="checkbox"
                       id="<?= esc_attr($this->id) ?>"
                       name="<?= esc_attr($this->id) ?>"
                       class="onoffswitch-checkbox" 
                       value="<?= esc_attr($this->value()) ?>" <?php $this->link(); checked($this->value()); ?>>
                <label class="onoffswitch-label" for="<?= esc_attr($this->id) ?>"></label>
            </div>
            <span class="customize-control-title onoffswitch_label"><?= esc_html($this->label) ?></span>
        </div>
        <?php
    }

    public function enqueue(): void {
        wp_register_style('astra-nodes-custom-toggle', '', [], '', 'all');
        wp_enqueue_style('astra-nodes-custom-toggle');
        wp_add_inline_style('astra-nodes-custom-toggle', '
            .customize-control-checkbox label {
                margin-left: 0;
                padding-top: 0;
                padding-bottom: 0;
                line-height: 28px;
            }

            .onoffswitch_label {
                display: inline-block;
                vertical-align: top;
                margin-top: -1px;
                width: 200px;
            }

            .onoffswitch {
                position: relative;
                width: 40px;
                display: inline-block;
                float: right;
                -webkit-user-select:none;
                -moz-user-select:none;
                -ms-user-select: none;
            }

            .onoffswitch-checkbox {
                display: none!important;
            }

            .onoffswitch-label {
                display: block;
                overflow: hidden;
                cursor: pointer;
                height: 18px;
                padding: 0;
                line-height: 18px;
                border: 2px solid #9E9E9E;
                border-radius: 18px;
                background-color: #9E9E9E;
                transition: background-color 0.2s ease-in;
            }

            .onoffswitch-label:before {
                content: "";
                display: block;
                width: 18px;
                margin: 0;
                background: #EBEBEB;
                position: absolute;
                top: 0;
                bottom: 0;
                right: 20px;
                border: 2px solid #9E9E9E;
                border-radius: 18px;
                transition: all 0.2s ease-in 0s;
            }

            .onoffswitch-checkbox:checked + .onoffswitch-label {
                background-color: #42A5F5;
            }

            .onoffswitch-checkbox:checked + .onoffswitch-label,
            .onoffswitch-checkbox:checked + .onoffswitch-label:before {
                border-color: #42A5F5;
            }

            .onoffswitch-checkbox:checked + .onoffswitch-label:before {
                right: 0;
            }
        ');
    }

}
