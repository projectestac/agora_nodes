<?php

class WP_Customize_Font_Icon_Picker_Control extends WP_Customize_Control {

    public $type = 'fonticonpicker';
    private $i;

    public function __construct($manager, $id, $args = []) {
        $this->i = $args['i'];
        parent::__construct($manager, $id, $args);
    }

    public function render_content(): void {
        echo '
            <script>
                var uip = new UniversalIconPicker("#_customize-input-astra_nodes_customizer_header_button_' . $this->i . '", {
                    iconLibraries: [
                        "' . content_url('mu-plugins/astra-nodes/lib/universal-icon-picker/assets/icons-libraries/font-awesome-solid.min.json') . '"
                    ],
                    iconLibrariesCss: [
                        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
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
        wp_enqueue_script('universal-icon-picker', content_url('mu-plugins/astra-nodes/lib/universal-icon-picker/assets/js/universal-icon-picker.js'),
            [], null, true);

        wp_register_style('universal-icon-picker', '', [], '', 'all');
        wp_enqueue_style('universal-icon-picker');
        wp_add_inline_style('universal-icon-picker', '
            .customize-control .universal-icon-picker-button {
                float: right;
            }
            .astra-nodes-customizer-theme-icon {
                font-size: 20px;
            }
            /* Hide sidebar */
            .uip-modal--sidebar {
                display: none;
            }
            /* Use the space left by the sidebar */
            .uip-modal .uip-modal--content {
                max-width: 900px !important;
            }
            .uip-modal .uip-modal--content .uip-modal--body .uip-modal--icon-preview-wrap {
                width: 100% !important;
            }
            /* Set color to black */
            .uip-modal .uip-modal--content .uip-modal--body .uip-modal--icon-preview-wrap .uip-modal--icon-preview-inner .uip-modal--icon-preview .uip-icon-item .uip-icon-item-inner i,
            .uip-modal .uip-modal--content .uip-modal--body .uip-modal--icon-preview-wrap .uip-modal--icon-preview-inner .uip-modal--icon-preview .uip-icon-item .uip-icon-item-inner .uip-icon-item-name{
                color: inherit !important;
            }
        ');
    }

}