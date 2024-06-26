<?php

class WP_Customize_Separator_Control extends WP_Customize_Control {

    public $type = 'separator';

    public function __construct($manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {
        echo '<br><hr><br>';
    }

}
