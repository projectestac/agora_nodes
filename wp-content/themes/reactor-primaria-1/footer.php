<?php
/**
 * The template for displaying the footer
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>
        <?php reactor_footer_before(); ?>
        
        <footer id="footer" class="site-footer" role="contentinfo">
        
        	<?php reactor_footer_inside(); ?>
        
        </footer><!-- #footer -->
        <?php reactor_footer_after(); ?>
        
    </div><!-- #main -->
</div><!-- #page -->

<?php wp_footer(); reactor_foot(); ?>

<script>
    
function menu_toggle(){
    $icon_menu=document.getElementById("icon-menu");
    $menu_panel=document.getElementById("menu-panel");

    if ($menu_panel.style.display=="inline-block") {
      $menu_panel.style.display="none";
      $icon_menu.setAttribute("class", "dashicons dashicons-menu");
    } else {
      $menu_panel.style.display="inline-block";
      $icon_menu.setAttribute("class", "dashicons dashicons-no-alt");	
      document.getElementById("icon-23").setAttribute("backgroundColor", "yellow");
    }
}

function cerca_toggle(){
    $icon_search  = document.getElementById("icon-search");
    $search_panel = document.getElementById("search-panel");

    if ($search_panel.style.display=="inline-block") {
      $search_panel.style.display="none";
      $icon_search.setAttribute("class", "dashicons dashicons-search");
    } else {
      $search_panel.style.display="inline-block";
      $icon_search.setAttribute("class", "dashicons dashicons-no-alt");	
      document.getElementById("icon-13").setAttribute("backgroundColor", "yellow");
    }
}

</script>

 
<?php 
    //TODO: refactoring, wp_is_mobile includes tablets
    if (!wp_is_mobile()){ ?>

    <script type="text/javascript">
        equalize_cards();
    </script>
    
<?php } ?>

<?php
// XTEC ************ AFEGIT - Added for SQL debug. To use it, uncomment the code and set SAVEQUERIES to true in wp-config.php
// 2014.11.05 @aginard
/*
if ( current_user_can( 'administrator' ) ) {
global $wpdb;
echo "<pre>";
print_r( $wpdb->queries );
echo "</pre>";
}
*/
//************ FI
?>

</body>
</html>
