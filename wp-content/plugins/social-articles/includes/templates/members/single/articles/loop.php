<?php

/**
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */

?>
<?php global $bp, $post, $wpdb, $socialArticles;

      $directWorkflow = isDirectWorkflow();

      $initialCount = $socialArticles->options['post_per_page'];  

      $publishCount = custom_get_user_posts_count('publish');
      $pendingCount = custom_get_user_posts_count('pending');
      $draftCount = custom_get_user_posts_count('draft');

       if($directWorkflow){
           $postCount = $draftCount + $publishCount;
       }else{
           $postCount =  count_user_posts(bp_displayed_user_id());
       }

?>

<section id="articles-container">     
    <?php if($publishCount > 0 || bp_displayed_user_id()==bp_loggedin_user_id()):?>    

    <div class="publish-container">    
        <?php get_articles(0, 'publish');?>
        <div id="more-container-publish">
        </div>    
        <?php
        if($publishCount > $initialCount){ ?>
        <div class="more-articles-button-container">       
            <input type="submit" id="more-articles-button" class="button" onclick ="getMoreArticles('publish'); return false;" value="<?php _e("Load more articles", "social-articles");?>"/>       
            <img id="more-articles-loader" src="<?php echo SA_BASE_URL . '/assets/images/bp-ajax-loader.gif' ; ?>"/>
        </div>       
        <?php
        }
        ?>
    </div>
    <?php else: ?>
    <div id="message" class="messageBox note icon">
        <span><?php _e("This user doesn't have any article.", "social-articles");?></span>
    </div>
    <?php endif;?>
</section>     

<input type="hidden" value="<?php echo $initialCount;?>" id="inicialcount"/>
<input type="hidden" value="<?php echo $postCount;?>" id="postcount"/>
<input type="hidden" value="<?php echo $initialCount;?>" id="offset"/>
<input type="hidden" value="<?php echo "publish";?>" id="current-state"/>