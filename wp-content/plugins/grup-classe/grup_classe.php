<?php
/**
 * Plugin Name: Grup-classe
 * Plugin URI: https://github.com/projectestac/agora_nodes/
 * Description: Afegeix un nou giny que inclou elements útils per un blog d'un
 *              grup-classe (calendari, informació sobre tutoria, formulari de
 *              subscripció, enllaços associats) i dos blocs de text/HTML.
 * Version: 1.2
 * Author: Xavier Meler
 * Author URI: https://github.com/jmeler
 * Copyright 2015 Xavier Meler
 * Email: jmeler@xtec.cat
 * License: GPLv3
 **/

// Register plugin.
add_action('widgets_init', function () {
    register_widget('grup_classe_widget');
});

// Enqueue plugin css.
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('style-grup_classe', plugins_url() . '/grup-classe/css/grup_classe.css');
});

load_plugin_textdomain('grup-classe', false, plugin_basename(__DIR__) . '/languages');

const OPTION_TAG_LITERAL = '<option value="';
const OPTION_TAG_END_LITERAL = '</option>';

class grup_classe_widget extends WP_Widget {

    public function __construct()
    {
        $widget_ops = [
                'description' =>
                        __('Includes a calendar, information about the tutor and two blocks of type text/html. Useful for the class blog.',
                                'grup-classe'),
                'name' => __('Group-class', 'grup-classe'),
        ];

        parent::__construct('grup_classe_widget', __('Group-class', 'grup-classe'), $widget_ops);
    }

    // Show form in admin backend.
    public function form($instance): void
    {
        $defaults = [
                'horari_families' => __('Mon. 00:00-00:00', 'grup-classe'),
        ];

        $instance = wp_parse_args((array)$instance, $defaults);

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $text_open = esc_textarea($instance['text_open'] ?? '');
        $id_calendari = $instance['id_calendari'] ?? '';
        $nom_calendari = $instance['nom_calendari'] ?? '';
        $nom_tutor = $instance['nom_tutor'] ?? '';
        $email_tutor = $instance['email_tutor'] ?? '';
        $horari_families = $instance['horari_families'] ?? '';
        $ig_forms = $instance['ig_forms'] ?? '';
        $nav_menu = $instance['nav_menu'] ?? '';
        $text_close = esc_textarea($instance['text_close'] ?? '');

        ?>

        <p>
            <?php
            _e('Title:', 'grup-classe'); ?><br/>
            <input id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"/>
        </p>

        <!-- Custom html/text block -->
        <div>
            <strong>
                <span class="dashicons dashicons-format-quote"></span>
                <?php _e("Text/HTML", "grup-classe"); ?>
            </strong>
            <textarea class='widefat' rows=5
                      id="<?php echo $this->get_field_id('text_open'); ?>"
                      name="<?php echo $this->get_field_name('text_open'); ?>"
                      ><?php echo $text_open; ?></textarea>
        </div>
        <br/>

        <!-- Calendar / Classroom -->
        <?php
        $args = [
                'posts_per_page' => -1,
                'post_type' => 'calendar',
                'order' => 'ASC',
        ];
        $calendaris = get_posts($args);
        wp_reset_query();
        ?>

        <div>
            <strong>
                <span class="dashicons dashicons-calendar"></span>
                <?php _e("Calendar", "grup-classe"); ?>
            </strong>
            <br/>
            <?php _e("Calendar's title:", "grup-classe"); ?>
            <input id="<?php echo $this->get_field_id('nom_calendari'); ?>"
                   name="<?php echo $this->get_field_name('nom_calendari'); ?>"
                   type="text"
                   value="<?php echo esc_attr($nom_calendari); ?>"/>
            <br/>
            <?php _e("Calendar:", "grup-classe"); ?>
            <br/>
            <select id="<?php echo $this->get_field_id('id_calendari'); ?>"
                    name="<?php echo $this->get_field_name('id_calendari'); ?>">
                <option value="0"></option>
                <?php
                foreach ($calendaris as $cal) {
                    echo OPTION_TAG_LITERAL . $cal->ID . '"'
                            . selected($id_calendari, $cal->ID, false)
                            . '>' . esc_html($cal->post_title) . OPTION_TAG_END_LITERAL;
                }
                ?>
            </select>
            <br/>
        </div>
        <br/>

        <!-- Tutor's information -->
        <div>
            <strong>
                <span class="dashicons dashicons-admin-users"></span>
                <?php _e("Tutor's information", 'grup-classe'); ?>
            </strong>
            <br/>
            <?php _e("Tutor's name:", 'grup-classe'); ?>
            <br/>
            <input name="<?php echo $this->get_field_name('nom_tutor'); ?>"
                   type="text"
                   value="<?php echo esc_attr($nom_tutor); ?>"/>
            <br/>
            <?php _e("Tutor's email:", 'grup-classe'); ?>
            <br/>
            <input name="<?php echo $this->get_field_name('email_tutor'); ?>"
                   type="text"
                   value="<?php echo esc_attr($email_tutor); ?>"/>
            <br/>
            <?php _e("Timetable for attending to families:", 'grup-classe'); ?>
            <br/>
            <input name="<?php echo $this->get_field_name('horari_families'); ?>"
                   type="text"
                   value="<?php echo esc_attr($horari_families); ?>"/>
            <br/>
        </div>
        <br/>

        <!-- Email subscribers -->
        <?php
        global $wpdb;
        $subscription_groups = $wpdb->get_results('SELECT id,name FROM ' . $wpdb->prefix . 'ig_forms');
        ?>
        <div>
            <strong>
                <span class="dashicons dashicons-email-alt"></span>
                <?php _e('Subscription Group:', 'grup-classe'); ?>
            </strong>
            <select id="<?php echo $this->get_field_id('ig_forms'); ?>"
                    name="<?php echo $this->get_field_name('ig_forms'); ?>">
                <option value="0"></option>
                <?php
                foreach ($subscription_groups as $subscription_group) {
                    echo OPTION_TAG_LITERAL . $subscription_group->id . '"'
                            . selected($ig_forms, $subscription_group->id, false)
                            . '>' . esc_html($subscription_group->name) . OPTION_TAG_END_LITERAL;
                }
                ?>
            </select>
        </div>
        <br/>

        <!-- Links -->
        <div>
            <?php
            $menus = wp_get_nav_menus(); ?>
            <strong>
                <span class="dashicons dashicons-admin-links"></span>
                <?php _e('Links (associated menu)', 'grup-classe'); ?>
            </strong>
            <select id="<?php echo $this->get_field_id('nav_menu'); ?>"
                    name="<?php
                    echo $this->get_field_name('nav_menu'); ?>">
                <option value="0"></option>
                <?php
                foreach ($menus as $menu) {
                    echo OPTION_TAG_LITERAL . $menu->term_id . '"'
                            . selected($nav_menu, $menu->term_id, false)
                            . '>' . esc_html($menu->name) . OPTION_TAG_END_LITERAL;
                }
                ?>
            </select>
        </div>
        <br/>

        <!-- Custom html/text block -->
        <div>
            <strong><span class="dashicons dashicons-format-quote"></span>
                <?php _e('Text/HTML', 'grup-classe'); ?>
            </strong>
            <textarea class='widefat' rows=5
                      id="<?php echo $this->get_field_id('text_close'); ?>"
                      name="<?php echo $this->get_field_name('text_close'); ?>"
                      ><?php echo $text_close; ?></textarea>
        </div>
        <br/>
        <?php
    }

    // Save options.
    public function update($new_instance, $old_instance): array
    {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        $instance['id_calendari'] = $new_instance['id_calendari'] ?? '';
        $instance['nom_calendari'] = sanitize_text_field($new_instance['nom_calendari'] ?? '');
        $instance['nom_tutor'] = sanitize_text_field($new_instance['nom_tutor'] ?? '');
        $instance['email_tutor'] = sanitize_text_field($new_instance['email_tutor'] ?? '');
        $instance['horari_families'] = sanitize_text_field($new_instance['horari_families'] ?? '');
        $instance['ig_forms'] = sanitize_text_field($new_instance['ig_forms'] ?? '');
        $instance['nav_menu'] = sanitize_text_field($new_instance['nav_menu'] ?? '');

        if (current_user_can('unfiltered_html')) {
            $instance['text_open'] = $new_instance['text_open'] ?? '';
            $instance['text_close'] = $new_instance['text_close'] ?? '';
        } else {
            $instance['text_open'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text_open'])));
            $instance['text_close'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text_close'])));
        }

        return $instance;
    }

    // Show widget at frontend
    public function widget($args, $instance): void
    {
        echo $args['before_widget'];

        $this->display_title($args, $instance['title'] ?? '');
        $this->display_text_block($instance['text_open'] ?? '');
        $this->display_calendar($args, $instance['id_calendari'] ?? '', $instance['nom_calendari'] ?? '');
        $this->display_tutor_info($args, $instance);
        $this->display_subscription($args, $instance['ig_forms'] ?? '');
        $this->display_links($args, $instance['nav_menu'] ?? '');
        $this->display_text_block($instance['text_close'] ?? '');

        echo $args['after_widget'];
    }

    // Methods for widget function to comply with SonarQube rules.
    private function display_title($args, $title): void
    {
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
    }

    // Custom html/text block.
    private function display_text_block($text): void
    {
        if (trim($text) !== '') {
            the_widget('WP_Widget_Text', "text=$text&filter=true");
        }
    }

    // Calendar.
    private function display_calendar($args, $id_calendari, $nom_calendari): void
    {
        if (!empty($id_calendari)) {
            if (!empty($nom_calendari)) {
                echo $args['before_title'] . esc_html($nom_calendari) . $args['after_title'];
            }
            the_widget('SimpleCalendar\Widgets\Calendar', 'calendar_id=' . $id_calendari);
        }
    }

    // Tutor's info.
    private function display_tutor_info($args, $instance): void
    {
        $nom_tutor = $instance['nom_tutor'] ?? '';
        $email_tutor = $instance['email_tutor'] ?? '';
        $horari = $instance['horari_families'] ?? '';

        if ($nom_tutor || $email_tutor || $horari) {
            echo $args['before_title'] . __("Tutor's info", 'grup-classe') . $args['after_title'];
            echo '<ul>';
            if ($nom_tutor) {
                echo "<li><span class='dashicons dashicons-admin-users'></span>$nom_tutor</li>";
            }

            if ($email_tutor) {
                echo "<li><span class='dashicons dashicons-email-alt'></span>$email_tutor</li>";
            }

            if ($horari) {
                echo "<li><span class='dashicons dashicons-clock'></span>$horari</li>";
            }
            echo '</ul>';
        }
    }

    // Email subscribers
    private function display_subscription($args, $ig_forms): void
    {
        if (!empty($ig_forms)) {
            echo $args['before_title'] . __('Subscription', 'grup-classe') . $args['after_title'];
            echo __('We will notice you', 'grup-classe') . '<br /><br />';
            es_subbox($namefield = 'YES', $desc = '', $group = $ig_forms);
            echo '<br />';
        }
    }

    // Links (Menu)
    private function display_links($args, $nav_menu): void
    {
        if (!empty($nav_menu)) {
            echo $args['before_title'] . __('Links', 'grup-classe') . $args['after_title'];
            the_widget('WP_Nav_Menu_Widget', 'nav_menu=' . $nav_menu);
            echo '<br />';
        }
    }

}
