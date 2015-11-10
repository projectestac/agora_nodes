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

    function isMobileDevice(){
        return jQuery("html").hasClass("touch") ? true : false;
    }

//    jQuery(".menu-responsive.dashicons.dashicons-menu").on("click", function(event) {
//        event.preventDefault();
//        event.stopPropagation();
//
//        var $menuHeader = jQuery("#menu-panel").find(".menu-header"),
//            $list = $menuHeader.find("ul.menu-principal"),
//            $listItems = $list.find("li.menu-item-depth-0"),
//            $imgCentre = jQuery(".box-titlemobile-logo").find("img");
//
//        var toggleClass = $menuHeader.hasClass("open-menu-header") ? "removeClass" : "addClass";
//
//        $menuHeader[toggleClass]("open-menu-header");
//        $list[toggleClass]("open-menu-principal");
//        $imgCentre[toggleClass]("invisible");
//
//        if(isMobileDevice()) {
//            console.info("mobile device");
////            jQuery(".menu-item-depth-0").unbind('mouseenter mouseleave');
////            jQuery(".menu-item-depth-0").off('hover');
//        } else {
//            console.info("mobile device");
//        }
//
//    });

    var MenuResponsive = {
        is_mobile: false,
        list_items: {},
        init: function() {
            MenuResponsive.is_mobile = isMobileDevice();
            MenuResponsive.addEventListeners();
        },
        addEventListeners: function() {
            jQuery(".menu-responsive.dashicons.dashicons-menu").on("click", function(event) {
                event.preventDefault();
                event.stopPropagation();

                var $menuHeader = jQuery("#menu-panel").find(".menu-header"),
                    $list = $menuHeader.find("ul.menu-principal"),
                    $listItems = $list.find("li.menu-item-depth-0"),
                    $imgCentre = jQuery(".box-titlemobile-logo").find("img");

                $listItems.on("click", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    var self = jQuery(this),
                    $ul = self.find("> ul"),
                    toggleVisibility = $ul.hasClass("visible") ? "removeClass" : "addClass";
                    $ul[toggleVisibility]("visible");
                });


                var toggleClass = $menuHeader.hasClass("open-menu-header") ? "removeClass" : "addClass";

                $menuHeader[toggleClass]("open-menu-header");
                $list[toggleClass]("open-menu-principal");
                $imgCentre[toggleClass]("invisible");
            });
        }
    };


    MenuResponsive.init();


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
        //http://www.feedthebot.com/pagespeed/defer-loading-javascript.html
        function downloadJSAtOnload() {
            var element = document.createElement("script");
            element.src = "<?php echo get_stylesheet_directory_uri().'/'?>equalize-cards.js";
            document.body.appendChild(element);
        }

        if (window.addEventListener)
            window.addEventListener("load", downloadJSAtOnload, false);
        else if (window.attachEvent)
            window.attachEvent("onload", downloadJSAtOnload);
        else window.onload = downloadJSAtOnload;
        
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
