<?php

class WP_Customize_Raw_HTML_Control extends WP_Customize_Control {
    public $type = 'html';
    private $content;

    public function __construct($manager, $id, $args = []) {
        $this->content = $args['content'];
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {
        echo $this->content;
    }

}
