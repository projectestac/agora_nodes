<?php

/*
 Plugin Name: Grup-classe
 Plugin URI: http://agora.xtec.cat/nodes/plugins/grup-classe
 Description: Afegeix un nou giny que inclou elements útils per un blog d'un grup classe (calendari, informació sobre tutoria, enllaços associats) i dos blocs de text/HTML
 Version: 1.0
 Author: Xavier Meler
 Author URI: https://github.com/jmeler
 License: GPLv2
 */

/*  Copyright 2015 Xavier Meler (email: jmeler@xtec.cat)

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 
 */

/* Load textdomain */
add_action( 'init', 'grup_classe_load_textdomain' );

function grup_classe_load_textdomain() {
  load_plugin_textdomain( 'grup-classe', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}

/* Enqueve plugin css */
add_action( 'wp_enqueue_scripts', 'grup_classe_css' );

function grup_classe_css() {
    wp_enqueue_style( 'style-grup_classe', plugins_url().'/grup-classe/css/grup_classe.css' );
}	

// Register plugin
add_action('widgets_init','grup_classe_register_widgets');

function grup_classe_register_widgets(){
    register_widget('grup_classe_widget');
}

// The plugin
class Grup_classe_Widget extends WP_Widget {
    
    // Constructor
    function Grup_classe_Widget(){
        $widget_ops =array (
            'classname' => 'grup_classe_widget_class',
            //Catalan, default language here. Gettext doesn't work in description :S
            'description' => __("Inclou un calendari, informació sobre el tutor/a i dos blocs de tipus text/html. Util per al blog de classe.",'grup-classe')
             );
        $this->WP_Widget('grup_classe_widget', 'Grup-classe', $widget_ops);
    }
    
    // Show form in admin back-end
    function form ($instance){
        $defaults = array (
            'horari_families' => __("Mon. 00:00-00:00","grup-classe")
        );
        
        $instance       = wp_parse_args((array) $instance, $defaults);
        
        $title          = !empty($instance['title'] ) ? $instance['title'] : '';
        $text_open      = esc_textarea($instance['text_open']);
        $id_calendari   = $instance ['id_calendari'];
        $nom_calendari  = $instance ['nom_calendari'];
        $calendari_grid = $instance ['calendari_grid'];
        $calendari_list = $instance ['calendari_list'];
        $nom_tutor      = $instance ['nom_tutor'];
        $email_tutor    = $instance ['email_tutor'];
        $horari_families= $instance ['horari_families'];
        $nav_menu       = $instance ['nav_menu'];
        $text_close     = esc_textarea($instance['text_close']);
        
        ?>
    
        <p><?php _e("Title:","grup-classe");?><br>
        <input id=  "<?php echo $this->get_field_id( 'title' ); ?>" 
               name="<?php echo $this->get_field_name( 'title' ); ?>" 
               type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <!-- Custom html/text block -->
        <div>
            <strong><span class="dashicons dashicons-format-quote"></span> <?php _e("Text/HTML","grup-classe");?> </strong> 
            <textarea class='widefat' rows=5 id="<?php echo $this->get_field_id('text_open'); ?>" name="<?php echo $this->get_field_name('text_open'); ?>"><?php echo $text_open; ?></textarea>
        </div>
        <br>        
        <!-- Calendar / Classroom -->
        <?php 
         $args = array( 'posts_per_page' => -1, 'post_type' => 'gce_feed', 'order' => 'ASC');
         $calendaris = get_posts($args);
         wp_reset_query();
        ?>
        <div>
            <strong><span class="dashicons dashicons-calendar"></span> <?php _e("Calendar","grup-classe");?> </strong><br>
            <?php _e("Calendar's title:","grup-classe");?>
            <input id="<?php echo $this->get_field_id( 'nom_calendari' ); ?>" 
                   name="<?php echo $this->get_field_name( 'nom_calendari' ); ?>" 
                   type="text" value="<?php echo esc_attr( $nom_calendari ); ?>">
            <br>
            <?php _e("Calendar:","grup-classe");?><br>
            <select id="<?php echo $this->get_field_id('id_calendari'); ?>" 
                    name="<?php echo $this->get_field_name('id_calendari'); ?>">
            <option value="0"></option>
            <?php
                foreach ( $calendaris as $cal ) {
                    echo '<option value="' . $cal->ID . '"'
                            . selected( $id_calendari, $cal->ID, false )
                            . '>'. esc_html( $cal->post_title ) . '</option>';
                }
            ?>
            </select>
            <br>
            <?php _e("Show:","grup-classe");?>
            <br><input id="<?php echo $this->get_field_id('calendari_grid'); ?>" name="<?php echo $this->get_field_name('calendari_grid'); ?>" type="checkbox" <?php checked(isset($instance['calendari_grid']) ? $instance['calendari_grid'] : 0); ?> /> <?php _e("Grid","grup-classe");?> </label>
            <br><input id="<?php echo $this->get_field_id('calendari_list'); ?>" name="<?php echo $this->get_field_name('calendari_list'); ?>" type="checkbox" <?php checked(isset($instance['calendari_list']) ? $instance['calendari_list'] : 0); ?> /> <?php _e("List","grup-classe");?> </label>        
        </div>
        <br>          
        <!-- Tutor's information -->
        <div>
            <strong><span class="dashicons dashicons-admin-users"></span><?php _e("Tutor's information","grup-classe");?></strong><br>
                <?php _e("Tutor's name:","grup-classe");?><br>
                <input name="<?php echo $this->get_field_name('nom_tutor');?>"
                       type="text" 
                       value="<?php echo esc_attr( $nom_tutor ); ?>" /></br>
                <?php _e("Tutor's email:","grup-classe");?><br>
                <input name="<?php echo $this->get_field_name('email_tutor');?>"
                       type="text" 
                       value="<?php echo esc_attr( $email_tutor ); ?>" /><br>
                <?php _e("Timetable for attending to families:","grup-classe");?><br>
                <input name="<?php echo $this->get_field_name('horari_families');?>"
                       type="text" 
                       value="<?php echo esc_attr( $horari_families ); ?>" /></br>    
        </div>
        <br>      
        <!-- Links -->
        <div>
            <?php
            $nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
            $menus = wp_get_nav_menus();
            ?>
            <strong><span class="dashicons dashicons-admin-links"></span> <?php _e("Links (associated menu)","grup-classe");?></strong> 
            <select id="<?php echo $this->get_field_id('nav_menu'); ?>" 
                    name="<?php echo $this->get_field_name('nav_menu'); ?>">
                <option value="0"></option>
            <?php
                foreach ( $menus as $menu ) {
                    echo '<option value="' . $menu->term_id . '"'
                        . selected( $nav_menu, $menu->term_id, false )
                        . '>'. esc_html( $menu->name ) . '</option>';
                }
            ?>
            </select>
        </div>
        <br>
        <!-- Custom html/text block -->
        <div>
            <strong><span class="dashicons dashicons-format-quote"></span> <?php _e("Text/HTML","grup-classe");?> </strong> 
            <textarea class='widefat' rows=5 id="<?php echo $this->get_field_id('text_close'); ?>" name="<?php echo $this->get_field_name('text_close'); ?>"><?php echo $text_close; ?></textarea>
        </div>
        <br>
<?php        
    }
    
    //Save options
    function update($new_instance, $old_instance){
        $instance=$old_instance;
        
        $instance['title']           = sanitize_text_field($new_instance['title']);
        $instance['id_calendari']    = $new_instance['id_calendari'];
        $instance['nom_calendari']   = sanitize_text_field($new_instance['nom_calendari']);
        $instance['calendari_grid']  = isset($new_instance['calendari_grid']);
        $instance['calendari_list']  = isset($new_instance['calendari_list']);
        $instance['nom_tutor']       = sanitize_text_field($new_instance['nom_tutor']); 
        $instance['email_tutor']     = sanitize_text_field($new_instance['email_tutor']); 
        $instance['horari_families'] = sanitize_text_field($new_instance['horari_families']);
        $instance['nav_menu']        = sanitize_text_field($new_instance['nav_menu']);
        
        if ( current_user_can('unfiltered_html') ){
            $instance['text_open']  =  $new_instance['text_open'];
            $instance['text_close'] =  $new_instance['text_close'];
        } else{
            $instance['text_open']  = stripslashes( wp_filter_post_kses( addslashes($new_instance['text_open']) ) ); 
            $instance['text_close'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text_close']) ) ); 
        }
        
        return $instance;
    }
    
    // Show widget at frontend
    function widget ($args, $instance){
        extract( $args );
        echo $before_widget;
        
        $title          = apply_filters( 'widget_title', $instance['title']);
        $nom_tutor      = (empty($instance['nom_tutor']))?'':$instance['nom_tutor'];
        $email_tutor    = (empty($instance['email_tutor']))?'':$instance['email_tutor'];
        $horari_families= (empty($instance['horari_families']))?'':$instance['horari_families'];
        $id_calendari   = (empty($instance['id_calendari']))?'':$instance['id_calendari'];
        $nom_calendari  = (empty($instance['nom_calendari']))?'':$instance['nom_calendari'];
        $calendari_grid = (empty($instance['calendari_grid']))?'':$instance['calendari_grid'];
        $calendari_list = (empty($instance['calendari_list']))?'':$instance['calendari_list'];
        $nav_menu       = (empty($instance['nav_menu']))?'':$instance['nav_menu'];
        $text_open      = (empty($instance['text_open']))?'':$instance['text_open'];
        $text_close     = (empty($instance['text_close']))?'':$instance['text_close'];
        
        if (!empty($title)){
            echo $before_title . esc_html($title) . $after_title;
        }
        //  Custom html/text block 
        if (strlen(trim($text_open))>0){
            the_widget( 'WP_Widget_Text',"text=$text_open&filter=true");
        }
        // Calendar
        if (!empty($id_calendari)){
            if (!empty($nom_calendari))
                echo $before_title . $nom_calendari .$after_title;
            if ($calendari_grid){
                the_widget('GCE_Widget','id='.$id_calendari.'&display_type=grid&paging=true');
                echo "<br>";
            }
            if ($calendari_list){
                the_widget('GCE_Widget','id='.$id_calendari.'&display_type=list&paging=true');
                echo "<br>";
            }
        }
        // Tutor's info
        if (!empty($nom_tutor) || !empty($email_tutor) || !empty($horari_families)){
            echo $before_title . __("Tutor's info","grup-classe") . $after_title;
            echo "<ul>";
            if (!empty($nom_tutor))
              echo "<li><span class='dashicons dashicons-admin-users'></span> $nom_tutor";
            if (!empty($email_tutor))
              echo "<li><span class='dashicons dashicons-email-alt'></span> $email_tutor";
            if (!empty($horari_families))
              echo "<li><span class='dashicons dashicons-clock'></span> $horari_families";
            echo "</ul>";
        }
        // Links (Menu)
        if (!empty($nav_menu)){
            echo $before_title . __("Links","grup-classe") . $after_title;
            the_widget('WP_Nav_Menu_Widget','nav_menu='.$nav_menu);
            echo "<br>";
        }
        // Custom html/text block 
        if (strlen(trim($text_close))>0) {
            the_widget( 'WP_Widget_Text',"text=$text_close&filter=true");
        }
        echo $after_widget;
    }
   
}

