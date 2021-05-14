<?php
/**
 * Plugin Name: SocialMedia
 * Plugin URI: https://agora.xtec.cat/nodes/plugins/grup-classe
 * Description: Giny d'enllaços socials (facebook, twitter, youtube, vimeo, pinterest...). Facilita la incorporació d'enllaços habituals a les pàgines web dels centres educatius.
 * Version: 1.1
 * Author: Xavier Meler
 * Author URI: https://github.com/jmeler
 * License: GPLv2
 */

class SocialMedia_Widget extends WP_Widget {

    public $socialmedia = [
        'twitter' => ['nom' => 'Twitter', 'url' => '', 'img' => 'twitter-square'],
        'facebook' => ['nom' => 'Facebook', 'url' => '', 'img' => 'facebook-square'],
        'google-plus' => ['nom' => 'Google Plus', 'url' => '', 'img' => 'google-plus-square'],
        'youtube' => ['nom' => 'Youtube', 'url' => '', 'img' => 'youtube-square'],
        'vimeo' => ['nom' => 'Vimeo', 'url' => '', 'img' => 'vimeo-square'],
        'picasa' => ['nom' => 'Picasa', 'url' => '', 'img' => 'camera'],
        'flickr' => ['nom' => 'Flickr', 'url' => '', 'img' => 'flickr'],
        'pinterest' => ['nom' => 'Pinterest', 'url' => '', 'img' => 'pinterest-square'],
        'instagram' => ['nom' => 'Instagram', 'url' => '', 'img' => 'instagram'],
        'tumblr' => ['nom' => 'Tumblr', 'url' => '', 'img' => 'tumblr-square'],
        'soundcloud' => ['nom' => 'Soundcloud', 'url' => '', 'img' => 'soundcloud'],
        'dropbox' => ['nom' => 'Dropbox', 'url' => '', 'img' => 'dropbox'],
        'rss' => ['nom' => 'rss', 'url' => '', 'img' => 'rss-square'],
        'email' => ['nom' => 'Correu', 'url' => '', 'img' => 'envelope-square'],
        'moodle' => ['nom' => 'Moodle', 'url' => '', 'img' => 'graduation-cap'],
        'xarxanodes' => ['nom' => 'Xarxa Nodes', 'url' => '', 'img' => 'comments'],
        'docs' => ['nom' => 'Documents', 'url' => '', 'img' => 'folder-open'],
        'fotos' => ['nom' => 'Fotos', 'url' => '', 'img' => 'photo'],
        'video' => ['nom' => 'Videos', 'url' => '', 'img' => 'caret-square-o-right']
    ];

    // Constructor
    public function __construct() {
        parent::__construct(
            'socialmedia_widget',
            'Enllaços social media',
            ['description' => 'Enllaços a les vostres xarxes socials i canals multimèdia',]
        );
        $this->socialmedia['xarxanodes']['url'] = get_home_url() . '/activitat';
    }

    // Back-end form of the Widget
    public function form($instance) {
        $title = $instance['title'] ?? 'Segueix-nos!';
        $mida = $instance['mida'] ?? 'fa-2x';

        foreach ($this->socialmedia as $idSocialMedia => $nomSocialMedia) {
            if (isset($instance[$idSocialMedia . '_url'])) {
                $this->socialmedia[$idSocialMedia]['url'] = $instance[$idSocialMedia . '_url'];
            }
        }
        ?>
        <p>
            <label for="<?php
            echo $this->get_field_id('title'); ?>">Títol:</label>
            <input class="widefat"
                   id="<?php
                   echo $this->get_field_id('title'); ?>"
                   name="<?php
                   echo $this->get_field_name('title'); ?>"
                   type="text"
                   value="<?php
                   echo esc_attr($title); ?>"
            >
        </p>

        <p>
            <label for="<?php
            echo $this->get_field_id('mida'); ?>">Mida de les icones:</label>
            <br/>
            <select id="<?php
            echo $this->get_field_id('mida'); ?>"
                    name="<?php
                    echo $this->get_field_name('mida'); ?>"
            >
                <option value="fa-2x" <?php
                echo($mida == 'fa-2x' ? 'selected' : ''); ?>>Petites
                </option>
                <option value="fa-2-5x" <?php
                echo($mida == 'fa-2-5x' ? 'selected' : ''); ?>>Mitjanes
                </option>
                <option value="fa-3x" <?php
                echo($mida == 'fa-3x' ? 'selected' : ''); ?>>Grans
                </option>
            </select>
        </p>

        <label>Defineix les teves xarxes i canals multimèdia:</label>
        <br/>

        <?php
        foreach ($this->socialmedia as $idSocialMedia => $nomSocialMedia) { ?>
            <p>
                <label for="<?php
                echo $this->get_field_id($idSocialMedia); ?>">
                    <?php
                    echo esc_attr($nomSocialMedia['nom']); ?>
                    <br/>
                    <input class="widefat"
                           id="<?php
                           echo $this->get_field_id($idSocialMedia); ?>_url"
                           name="<?php
                           echo $this->get_field_name($idSocialMedia . "_url"); ?>"
                           type="text"
                           value="<?php
                           echo esc_attr($nomSocialMedia['url']); ?>"
                    />
                </label>
            </p>
            <?php
        }
    }

    // Sanitize and return the safe form values
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['mida'] = (!empty($new_instance['mida'])) ? sanitize_text_field($new_instance['mida']) : '';

        foreach ($this->socialmedia as $idSocialMedia => $nomSocialMedia) {
            $instance[$idSocialMedia . '_url'] = (!empty($new_instance[$idSocialMedia . '_url'])) ? sanitize_text_field($new_instance[$idSocialMedia . '_url']) : '';
        }

        return $instance;
    }

    // Front-End Display of the Widget
    public function widget($args, $instance) {

        extract($args);

        echo $before_widget;

        $title = $instance['title'];
        $mida = $instance['mida'];

        // Display title
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }

        foreach ($this->socialmedia as $idSocialMedia => $nomSocialMedia) {
            if (!empty($instance[$idSocialMedia . '_url'])) {
                if ($idSocialMedia == 'email') {
                    echo "<a class=\"fa fa-" . $this->socialmedia[$idSocialMedia]['img'] . ' ' . $mida . "\" href=\"mailto:" . esc_attr($instance[$idSocialMedia . '_url']) . "\" title=\"" . esc_attr($this->socialmedia[$idSocialMedia]['nom']) . "\"></a>";
                } else {
                    echo "<a class=\"fa fa-" . $this->socialmedia[$idSocialMedia]['img'] . ' ' . $mida . "\" href=\"" . esc_attr($instance[$idSocialMedia . '_url']) . "\" title=\"" . esc_attr($this->socialmedia[$idSocialMedia]['nom']) . "\" target=\"_blank\"></a>";
                }
            }
        }

        echo $after_widget;
    }
}

// Register widget
add_action('widgets_init', function () {
    register_widget('socialmedia_widget');
});
