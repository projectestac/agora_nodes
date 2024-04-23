<?php

class WP_Customize_Font_Icon_Picker_Control extends WP_Customize_Control {

    public $type = 'fonticonpicker';
    private $i;

    public function __construct($manager, $id, $args = []) {
        $this->i = $args['i'];
        parent::__construct($manager, $id, $args);
    }

    public function render_content() {
        echo '
            <script>
                var uip = new UniversalIconPicker("#_customize-input-astra_nodes_customizer_header_button_' . $this->i . '", {
                    iconLibraries: [
                        "' . content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/icons-libraries/font-awesome-solid.min.json') . '"
                    ],
                    iconLibrariesCss: [
                        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
                    ],
                    onSelect: function(jsonIconData) {
                        var iconField = document.querySelector("#_customize-input-astra_nodes_customizer_header_icon_' . $this->i . '_classes");
    
                        // Changing field value
                        iconField.value = jsonIconData.iconClass;
    
                        // Simulating a user changing the field, to trigger WordPress "Publish" button.
                        iconField.dispatchEvent(new Event("change"));

                        // Updating UI
                        var iconImage = document.querySelector("#customize-control-icon_preview_' . $this->i . ' > i");
                        iconImage.setAttribute("class", jsonIconData.iconClass);
                    },
                    onReset: function() {
                        // Do something on reset if needed
                    }
                });
            </script>
            ';

        // Add a separator line.
        echo '<hr>';
    }

    public function enqueue(): void {
        wp_enqueue_style('font-awesome', content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/stylesheets/universal-icon-picker.min.css'));
        wp_enqueue_script('universal-icon-picker', content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/js/universal-icon-picker.js'),
            [], null, true);
    }

}