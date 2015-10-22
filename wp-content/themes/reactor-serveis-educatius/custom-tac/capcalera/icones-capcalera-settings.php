<?php

/*************************************************************
 * Capçalera settings form
 **************************************************************/
class graellaIcones
{
    const FIRST_ITEM_INDEX = 1;
    const MAX_ITEM_INDEX = 5;
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_theme_page(
            'Settings Admin',
            'Icones de capçalera',
            'manage_options',
            'my-setting-admin',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {


        // Set class property
        $this->options = get_option('my_option_name');
        ?>
        <div class="wrap row">
            <div style="float:left;margin-right:30px;">
                <?php screen_icon(); ?>
                <h2>Icones de capçalera</h2>

                <form method="post" action="options.php">

                    <?php
                    // This prints out all hidden setting fields
                    settings_fields('my_option_group');
                    do_settings_sections('my-setting-admin');
                    submit_button();
                    ?>
                </form>
            </div>

            <div style="float:right;padding:10px;border-left:1px solid silver;">
                <h3>Icones disponibles</h3>

                <p>Copia el nom de la icona i enganxa'l a la posició desitjada.</p>

                <div style="float:left;margin-right:10px">

                    <?php
                    // $dashicons1 =array (icones...), dashicons2, dashicons3
                    include "dashicons.php";
                    ?>

                    <?php
                    foreach ($dashicons1 as $icon) {
                        echo "<div> <span class=\"dashicons $icon\"></span> " . str_replace("dashicons-", "", $icon) . " </div>";
                    }
                    ?>
                </div>

                <div style="float:left;margin-right:10px">
                    <?php
                    foreach ($dashicons2 as $icon) {
                        echo "<div> <span class=\"dashicons $icon\"></span> " . str_replace("dashicons-", "", $icon) . " </div>";
                    }
                    ?>
                </div>

                <div style="float:left;margin-right:10px">
                    <?php
                    foreach ($dashicons3 as $icon) {
                        echo "<div> <span class=\"dashicons $icon\"></span> " . str_replace("dashicons-", "", $icon) . " </div>";
                    }
                    ?>
                </div>

            </div>

        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array($this, 'sanitize') // Sanitize
        );

        // Fila1
        add_settings_section(
            'setting_icons_fila', // ID
            '', // Title
            array($this, 'print_section_info'), // Callback
            'my-setting-admin' // Page
        );

        /*
         * Generació d'icones
         */

        for ($i = self::FIRST_ITEM_INDEX; $i <= self::MAX_ITEM_INDEX; $i++) {

            add_settings_field(
                'title_icon' . $i, // ID
                'Element ' . $i . ':', // Title
                array($this, 'title_icon_callback'), // Callback
                'my-setting-admin', // Page
                'setting_icons_fila', // Section
                array('index' => $i) // Args
            );

            add_settings_field(
                'icon' . $i, // ID
                'Icona:', // Title
                array($this, 'icon_callback'), // Callback
                'my-setting-admin', // Page
                'setting_icons_fila', // Section
                array('index' => $i) // Args
            );
            add_settings_field(
                'link_icon' . $i, // ID
                'Enllaç:', // Title
                array($this, 'link_icon_callback'), // Callback
                'my-setting-admin', // Page
                'setting_icons_fila', // Section
                array('index' => $i) // Args
            );

            add_settings_field(
                'separador11', // ID
                '<hr>', // Title
                array($this, 'sep_callback'), // Callback
                'my-setting-admin', // Page
                'setting_icons_fila' // Section
            );
        }

        add_settings_section(
            'setting_general', // ID
            'General', // Title
            array($this, 'print_section_info'), // Callback
            'my-setting-admin' // Page
        );

        // Mostrar text sota els icones 
        add_settings_field(
            'show_text_icon', // ID
            'Mostra text sota la icona:', // Title 
            array($this, 'show_text_icon_callback'), // Callback
            'my-setting-admin', // Page
            'setting_general' // Section           
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();

        for ($i = self::FIRST_ITEM_INDEX; $i <= self::MAX_ITEM_INDEX; $i++) {
            if (isset($input['icon' . $i]))
                $new_input['icon' . $i] = sanitize_text_field($input['icon' . $i]);
            if (isset($input['link_icon' . $i]))
                $new_input['link_icon' . $i] = sanitize_text_field($input['link_icon' . $i]);
            if (isset($input['title_icon' . $i]))
                $new_input['title_icon' . $i] = sanitize_text_field($input['title_icon' . $i]);
        }

        $new_input['show_text_icon'] = isset($input['show_text_icon']) ? sanitize_text_field($input['show_text_icon']) : null;

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_separador()
    {
        print '<hr>';
    }


    public function print_section_info()
    {
        print '<hr>';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function sep_callback()
    {
        //Nothing
    }


    public function icon_callback($args)
    {
        $index = $args['index'];
        printf(
            '<input type="text" id="icon' . $index . '" name="my_option_name[icon' . $index . ']" value="%s" /><div class="dashicons dashicons-%s"></div>',
            isset($this->options['icon' . $index]) ? esc_attr($this->options['icon' . $index]) : '', isset($this->options['icon' . $index]) ? esc_attr($this->options['icon' . $index]) : ''
        );
    }

    public function link_icon_callback($args)
    {
        $index = $args['index'];
        printf(
            '<input type="text" id="link_icon' . $index . '" name="my_option_name[link_icon' . $index . ']" value="%s" />',
            isset($this->options['link_icon' . $index]) ? esc_attr($this->options['link_icon' . $index]) : ''
        );
    }

    public function title_icon_callback($args)
    {
        $index = $args['index'];
        printf(
            '<input type="text" id="title_icon' . $index . '" name="my_option_name[title_icon' . $index . ']" value="%s" />',
            isset($this->options['title_icon' . $index]) ? esc_attr($this->options['title_icon' . $index]) : ''
        );
    }

    public function show_text_icon_callback()
    {
        printf(
            '<input type="checkbox" id="show_text_icon" name="my_option_name[show_text_icon]" value="%s" %s />',
            isset($this->options['show_text_icon']) ? "si" : 'no', isset($this->options['show_text_icon']) ? "checked=\"checked\"" : ''
        );
    }

}

if (is_admin())
    $my_settings_page = new graellaIcones();

?>
