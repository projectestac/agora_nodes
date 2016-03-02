<?php
/**
 * The sidebar template containing the front page widget area
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>
<?php reactor_sidebar_before();
$frontpage_layout = reactor_option('frontpage_layout');
$pull = $frontpage_layout == '2c-r' || $frontpage_layout == '3c-c' ? -6 : -9;
?>
<div id="sidebar-frontpage" class="sidebar <?php reactor_columns( 3 , true, false, null, $pull); ?>" role="complementary">
    <div id="sidebar-frontpage-inner">
        <?php dynamic_sidebar('sidebar-frontpage'); ?>
    </div>
</div><!-- #sidebar-frontpage -->
<?php reactor_sidebar_after(); ?>
