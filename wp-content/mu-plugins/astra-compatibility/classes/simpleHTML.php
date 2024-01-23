<?php

class simpleHTML extends WP_Customize_Control {
    public $type = 'simpleHTML';

    public function render_content() {
        ?>
        <label>
            <span class="customize-control-title">
                <?php
                echo esc_html($this->label); ?>
            </span>
            <a target="_blank" href="themes.php?page=my-setting-admin"> Aparença->Icones de capçalera </a>
        </label>
        <?php
    }
}

