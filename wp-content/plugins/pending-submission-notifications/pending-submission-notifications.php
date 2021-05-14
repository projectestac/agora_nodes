<?php
/**
 * @package Pending_Submission_Notification
 * @version 1.0
 *
 * Plugin Name: Pending Submission Notification
 * Plugin URI: http://lifeofadesigner.com
 * Description: Send email notifications to the admin whenever a new article is submitted for review by a contributor
 * Author: Razvan Horeanga
 * Author mods: Xavi Meler
 * Version: 1.0
 * Author URI: http://lifeofadesigner.com
 */

add_action('transition_post_status', 'pending_submission_send_email', 10, 3);

function pending_submission_send_email($new_status, $old_status, $post) {

    if ($new_status == 'pending' && user_can($post->post_author, 'edit_posts') && !user_can($post->post_author, 'publish_posts')) {

        // Notify Admin that Contributor has written a post
        $admins = get_option('admin_email');
        $edit_link = get_edit_post_link($post->ID, '');
        $preview_link = get_permalink($post->ID) . '&preview=true';
        $username = get_userdata($post->post_author);
        $subject = 'Nou article pendent: "' . $post->post_title . '"';

        $message = 'Un nou article per revisar.';
        $message .= "\r\n\r\n";
        $message .= "Autor/a: $username->user_login\r\n";
        $message .= "Títol: $post->post_title";
        $message .= "\r\n\r\n";
        $message .= "Edita: $edit_link\r\n";
        $message .= "Visualitza: $preview_link";

        wp_mail($admins, $subject, $message);

    } else if ($old_status == 'pending' && $new_status == 'publish' && user_can($post->post_author, 'edit_posts') && !user_can($post->post_author, 'publish_posts')) {

        // Notify Contributor that Admin has published their post
        $username = get_userdata($post->post_author);
        $url = get_permalink($post->ID);
        $subject = 'El vostre article ha estat publicat: ' . $post->post_title;
        $message = '"' . $post->post_title . '"' . " ha estat aprovat i publicat. \r\n\n" . $url;
 
        wp_mail($username->user_email, $subject, $message);

    }
}
