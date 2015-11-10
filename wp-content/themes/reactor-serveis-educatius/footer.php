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

    var Module = (function() {

        var isMobileDevice = function () {
            return jQuery("html").hasClass("touch") ? true : false;
        };

        var MenuResponsive = {
            is_mobile: false,
            menu: null,
            menu_header: null,
            list: {},
            list_items: {},
            list_items_anchor: {},
            img_header: null,
            html: null,
            init: function() {

                MenuResponsive.is_mobile = isMobileDevice(),
                    MenuResponsive.menu = jQuery(".menu-responsive.dashicons.dashicons-menu"),
                    MenuResponsive.menu_header = jQuery("#menu-panel").find(".menu-header"),
                    MenuResponsive.list = MenuResponsive.menu_header.find("ul.menu-principal"),
                    MenuResponsive.list_items = MenuResponsive.list.find("li.menu-item-depth-0"),
                    MenuResponsive.list_items_anchor = MenuResponsive.list.find("li.menu-item-depth-0 a"),
                    MenuResponsive.img_header = jQuery(".box-titlemobile-logo").find("img"),
                    MenuResponsive.html = jQuery('html');

                MenuResponsive.checkClassToNavigateListItemsAnchor();
                MenuResponsive.addEventListeners();
            },
            checkClassToNavigateListItemsAnchor: function() {
                MenuResponsive.list_items_anchor.each(function(index, value){
                    var self = jQuery(this),
                        href = self.attr('href');

                    if (href.indexOf("http") == -1) {
                        self.addClass("no-anchor");
                    }
                });
            },
            addEventListeners: function() {
                MenuResponsive.menu.on("click", MenuResponsive.handleVisibility);
                MenuResponsive.list_items.on("click", MenuResponsive.handleVisibilityMenuItems);
                MenuResponsive.list_items_anchor.on("click", MenuResponsive.handleMenuItemsAnchor);
            },
            handleVisibility: function(event) {
                event.preventDefault();
                event.stopPropagation();

                var toggleClass = MenuResponsive.menu_header.hasClass("open-menu-header") ? "removeClass" : "addClass";

                MenuResponsive.menu_header[toggleClass]("open-menu-header");
                MenuResponsive.list[toggleClass]("open-menu-principal");
                MenuResponsive.img_header[toggleClass]("invisible");
                MenuResponsive.html[toggleClass]("fixed");
            },
            handleVisibilityMenuItems: function(event) {
                event.preventDefault();
                event.stopPropagation();

                MenuResponsive.list_items.removeClass("list-opened"); // clear all arrow up icons

                var self = jQuery(this),
                    $ul = self.find("> ul"),
                    toggleVisibility = $ul.hasClass("visible") ? "removeClass" : "addClass";

                $ul[toggleVisibility]("visible");
                self[toggleVisibility]("list-opened");
            },
            handleMenuItemsAnchor: function(event) {
                event.preventDefault();
                event.stopPropagation();

                var self = jQuery(this),
                    href = self.attr("href");

                if(href) {
                    window.location.href = href;
                }

                return false;
            }
        };

        var SearchResponsive = {
            div_form : null,
            search_icon : null,

            init: function() {
                SearchResponsive.div_form = jQuery(".responsive-search-form ");
                SearchResponsive.search_icon = jQuery(".dashicons.dashicons-search ");
                SearchResponsive.addEventListeners();
            },
            addEventListeners: function() {
                SearchResponsive.search_icon.on("click",SearchResponsive.handleSearchBtn)
            },
            handleSearchBtn: function () {
                var  toggleVisibility = SearchResponsive.div_form.hasClass("focused") ? "removeClass" : "addClass";
                SearchResponsive.div_form[toggleVisibility]("focused");
            }
        };

        return {
            MenuResponsiveInit :  MenuResponsive.init,
            SearchResponsiveInit :  SearchResponsive.init,
            isMobileDevice :  isMobileDevice
        };

    })();

    Module.MenuResponsiveInit();
    Module.SearchResponsiveInit();
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
