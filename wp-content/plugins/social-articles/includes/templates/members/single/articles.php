<?php

/**
 * BuddyPress - Users Home
 *
 * @package BuddyPress
 * @subpackage bp-default
 */
global $bp;

if( bp_sa_is_bp_default() ):

get_header( 'buddypress' ); ?>

    <div id="content" class="social-articles-content">
        <div class="padder">

            <?php do_action( 'bp_before_member_home_content' ); ?>

            <div id="item-header" role="complementary">
                <?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
            </div>

            <div id="item-nav">
                <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
                    <ul>
                        <?php bp_get_displayed_user_nav(); ?>
                        <?php do_action( 'bp_member_options_nav' ); ?>
                    </ul>
                </div>
            </div>
            <div id="item-body">
                <?php if(bp_displayed_user_id()==bp_loggedin_user_id()):?>
                <div class="item-list-tabs no-ajax" id="subnav" role="navigation">
                    <ul class="nav nav-tabs">
                        <?php bp_get_options_nav(); ?>
                    </ul>
                </div>
                <?php endif;?>

                <?php do_action( 'bp_before_member_body' ); ?>

                <div id="articles-dir-list" class="articles">
                <?php

                switch($bp->current_action){
                    case 'new':
                        social_articles_load_sub_template(array('members/single/articles/new.php'));
                        break;
                    case 'articles':
                        social_articles_load_sub_template(array('members/single/articles/loop.php'));;
                        break;
                    case 'draft':
                        social_articles_load_sub_template(array('members/single/articles/draft.php'));
                        break;
                    case 'under-review':
                        social_articles_load_sub_template(array('members/single/articles/pending.php'));
                        break;
                }
                ?>
                </div>
                <?php do_action( 'bp_after_member_body' ); ?>
            </div>
            <?php do_action( 'bp_after_member_home_content' ); ?>
        </div>
    </div>
<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>
<?php
else :

    ?>
    <div id="buddypress">
        <?php do_action( 'template_notices' ); ?>
        <div class="social-articles-main" role="main">
            <div id="articles-dir-list" class="articles dir-list">
                <?php
                switch($bp->current_action){
                    case 'new':
                        social_articles_load_sub_template( 'members/single/articles/new' );
                        break;
                    case 'articles':
                        social_articles_load_sub_template( 'members/single/articles/loop' );;
                        break;
                    case 'draft':
                        social_articles_load_sub_template( 'members/single/articles/draft' );
                        break;
                    case 'under-review':
                        social_articles_load_sub_template( 'members/single/articles/pending' );
                        break;
                }
                ?>
            </div>
        </div>

    </div>
<?php
endif;
?>