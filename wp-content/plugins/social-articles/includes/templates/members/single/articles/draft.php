<?php global $bp, $post, $wpdb, $socialArticles;
?>
<section id="articles-container">
    <?php if(bp_displayed_user_id()==bp_loggedin_user_id()):?>
        <div class="articles-container">
            <?php get_articles(0, 'draft', true);?>
        </div>
    <?php endif;?>
</section>
