<?php

/* Hide default welcome dashboard message and create a custom one
 *
 * @access      public
 * @since       1.0 
 * @return      void
 */
function rc_my_welcome_panel() {
    ?>

    <script type="text/javascript">
        /* Hide default welcome message */
        jQuery(document).ready(function ($) {
            $('div.welcome-panel-content').hide();
        });
    </script>

    <style>
        .welcome-panel .welcome-panel-close::before {
            color: #646970;
        }
        .welcome-panel .welcome-panel-close {
            color: #0073aa;
            top: -4px;
        }
        .welcome-panel .welcome-panel-close:hover::before {
            color: red;
        }
        .welcome-panel .welcome-panel-close:hover {
            color: #0073aa;
        }

        .custom-welcome-panel-content {
            background-color: white;
            margin: 0;
            padding: 23px 10px 0;
            border: 1px solid #c3c4c7;
        }

        .custom-welcome-panel-content .welcome-panel-header {
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .custom-welcome-panel-content .welcome-panel-header h2 {
            color: #1d2327;
            margin: 0;
            font-size: 21px;
            font-weight: 400;
            line-height: 1.2;
        }

        .custom-welcome-panel-content .welcome-panel-header p {
            color: #646970;
            margin: 0;
            font-size: 16px;
        }

        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container {
            margin: 0;
            padding: 0;
        }

        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-column,
        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-last {
            display: block;
            padding: 23px 10px 0;
            min-width: 200px;
            width: 32%;
        }

        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-last {
            width: 36%;
        }

        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-last h3,
        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-column h3 {
            color: #1d2327;
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-column ul li {
            color: #646970;
        }

        .custom-welcome-panel-content .welcome-panel-header .welcome-panel-column-container .welcome-panel-column ul li a {
            color: #0073aa;
        }
    </style>

    <div class="custom-welcome-panel-content">
        <div class="welcome-panel-header">
            <h2><?php _e('Welcome to NODES', 'reactor'); ?></h2>
            <p><?php _e('Project for the new website from Departament d\'Ensenyament', 'reactor'); ?></p>

            <div class="welcome-panel-column-container">

                <div class="welcome-panel-column">
                    <h3><?php
                        _e('Actions', 'reactor'); ?></h3>
                    <ul>
                        <li><?php
                            printf('<a href="%s" class="welcome-icon welcome-view-site">' . __('Customize', 'reactor') . '</a>', 'customize.php'); ?></li>
                        <?php
                        if ('page' === get_option('show_on_front') && !get_option('page_for_posts')) : ?>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-edit-page">' . __('Edit Home Page', 'reactor') . '</a>', get_edit_post_link(get_option('page_on_front'))); ?></li>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-add-page">' . __('Header icons', 'reactor') . '</a>', admin_url('themes.php?page=my-setting-admin')); ?></li>
                        <?php
                        elseif ('page' === get_option('show_on_front')) : ?>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-edit-page">' . __('Edit Home Page', 'reactor') . '</a>', get_edit_post_link(get_option('page_on_front'))); ?></li>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-add-page">' . __('Add additional pages', 'reactor') . '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-write-blog">' . __('Add a blog post', 'reactor') . '</a>', admin_url('post-new.php')); ?></li>
                        <?php
                        else : ?>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-write-blog">' . __('Write your first blog post', 'reactor') . '</a>', admin_url('post-new.php')); ?></li>
                            <li><?php
                                printf('<a href="%s" class="welcome-icon welcome-add-page">' . __('Add an About page', 'reactor') . '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                        <?php
                        endif; ?>
                    </ul>
                </div>

                <div class="welcome-panel-column">
                    <h3><?php _e('More Actions', 'reactor'); ?></h3>
                    <ul>
                        <li><?php
                            printf('<div class="welcome-icon welcome-widgets-menus">' . __('Manage <a href="%1$s">widgets</a> or <a href="%2$s">menus</a>', 'reactor') . '</div>', admin_url('widgets.php'), admin_url('nav-menus.php')); ?></li>
                        <li><?php
                            printf('<a href="%s" class="welcome-icon welcome-add-page">' . __('Bookings', 'reactor') . '</a>', 'edit.php?post_type=calendar_booking'); ?></li>
                        <li><?php
                            printf('<a href="%s" class="welcome-icon welcome-add-page">' . __('BuddyPress', 'reactor') . '</a>', 'admin.php?page=xtec-bp-options'); ?></li>
                    </ul>
                </div>

                <div style="margin-bottom:1.5em" class="welcome-panel-last">
                    <h3><?php _e('Do you need help?', 'reactor'); ?></h3>
                    <a class="button button-primary button-hero"
                       target="_blank"
                       href="https://agora.xtec.cat/moodle/moodle/mod/glossary/view.php?id=1302"><?php
                        _e('FAQ', 'reactor'); ?></a>
                    <a class="button button-primary button-hero"
                       target="_blank"
                       href="https://agora.xtec.cat/moodle/moodle/mod/forum/view.php?id=1721"><?php
                        _e('Support', 'reactor'); ?></a>
                </div>

            </div>
        </div>
    </div>

    <?php
}
