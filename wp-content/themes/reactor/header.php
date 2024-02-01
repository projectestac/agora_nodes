<?php
/**
 * The template for displaying the header
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <!-- Add common styles to all themes -->
    <?php
        include_once get_theme_root() . '/reactor/custom-tac/styles/styles.php';
        include_once get_stylesheet_directory() . '/style.php';
    ?>

    <link href="https://fonts.googleapis.com/css?family=Oswald:400,300" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" type="text/css" />

    <?php
        wp_head();
        reactor_head();
    ?>

</head>

<body <?php body_class(); ?>>
    <?php reactor_body_inside(); ?>
    <div id="page" class="hfeed site">
        <?php reactor_header_before(); ?>

        <header id="header" class="site-header" role="banner">
            <div class="row">
                <div class="<?php reactor_columns( 12 ); ?>">

                    <?php reactor_header_inside(); ?>

                </div><!-- .columns -->
            </div><!-- .row -->
        </header><!-- #header -->

        <?php reactor_header_after(); ?>

        <div id="main" class="wrapper">
