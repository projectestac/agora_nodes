<!-- Display the table of links to the pages -->

<?php
function get_top_level_parent($page_id) {

    $parent_id = wp_get_post_parent_id($page_id);
    return $parent_id ? get_top_level_parent($parent_id) : $page_id;

}

function render_page($page, $skip_print = false): void {

    $children = get_pages([
        'parent' => $page->ID,
        'sort_column' => 'menu_order',
        'sort_order' => 'ASC',
    ]);

    if (count($children) > 0) {
        if (!$skip_print) {
            echo '<li>';
            echo '<a href="' . get_permalink($page->ID) . '" class="accordion-toggle parent">' . $page->post_title . ' <i class="fa-solid fa-angle-down"></i></a>';
            echo '<ul class="submenu">';
        }
        foreach ($children as $child) {
            render_page($child);
        }
        if (!$skip_print) {
            echo '</ul>';
            echo '</li>';
        }
    } else if (!$skip_print) {
        echo '<li>';
        echo '<a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a>';
        echo '</li>';
    }

}

?>

<ul class="accordion">

    <?php
    $current_page_id = get_the_ID();
    $top_level_parent = get_top_level_parent($current_page_id);

    $pages = get_pages([
        'post_type' => 'page',
        'parent' => 0,
        'sort_column' => 'menu_order',
        'sort_order' => 'ASC',
    ]);

    foreach ($pages as $page) {
        if ($page->ID === $top_level_parent) {
            render_page($page, true);
        }
    }

    ?>

</ul>

<script>

    jQuery(document).ready(function () {
        jQuery('.accordion-toggle i').click(function (e) {
            e.preventDefault();
            let icon = jQuery(this);
            let accordionToggle = icon.closest('.accordion-toggle');

            accordionToggle.parent().siblings().find('> .submenu').slideUp();
            accordionToggle.parent().siblings().find('> .accordion-toggle').removeClass('active');
            icon.toggleClass('fa-angle-down fa-angle-up');
            accordionToggle.toggleClass('active').next('.submenu').slideToggle();
        });

        // Close submenus by default
        jQuery('.submenu').hide();

        // Add the "current-menu-item" class to the link of the current page
        jQuery('ul.accordion a').each(function () {
            if (jQuery(this).attr('href') === window.location.href) {
                jQuery(this).closest('.submenu').show();
                jQuery(this).parent().addClass('current-menu-item');
                console.log(jQuery(this).closest('.submenu').prev());
                jQuery(this).closest('.submenu').prev().find('i').removeClass('fa-angle-down').addClass('fa-angle-up');

                // Open all parent submenus
                jQuery(this).parents('.submenu').each(function () {
                    jQuery(this).show();
                    jQuery(this).prev().find('i').removeClass('fa-angle-down').addClass('fa-angle-up');
                    jQuery(this).prev().addClass('active');
                });
            }
        });
    });

</script>

<style>

    .accordion {
        list-style-type: none;
        padding: 0;
        margin: 0;
        font-family: sans-serif;
    }

    .accordion li {
        color: #444;
        border-bottom: 1px solid #DBDBDB;
    }

    .accordion li ul li {
        border-bottom: 1px solid #ededed;
    }

    .accordion li a {
        display: block;
        color: #1EA19B;
        text-decoration: none;
        padding: 15px;
        position: relative;
        font-weight: 500;
    }

    .accordion li a.active {
        background-color: #f4f4f4;
    }

    .accordion li a .fa-angle-up {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%) rotate(0deg);
        width: 15px;
        height: 15px;
    }

    .accordion li a.active .fa-angle-down {
        transform: translateY(-50%) rotate(0deg);
    }

    .accordion li ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
        display: none;
    }

    .accordion li ul li a {
        padding-left: 20px;
        font-weight: 300;
        color: #444;
    }

    .accordion li ul li ul li a {
        padding-left: 40px;
        color: gray;
        font-size: 90%;
    }

    .accordion li:last-of-type {
        border-bottom: 0 solid #DBDBDB;
    }

    div.widget-area {
        margin-top: 25px !important;
    }

    div.sidebar-main {
        background-color: #f4f4f4;
        padding: 10px;
        border-radius: 25px;
    }

    div.sidebar-main li {
        list-style-type: none;
        margin: 0;
        padding: 5px;
        cursor: pointer;
    }

    div.sidebar-main li.current-menu-item {
        background-color: #e7e7e7;
        border-right: 3px solid #1EA19B;
    }

    div.sidebar-main ul.accordion {
        margin: 0;
        padding: 0;
    }

    div.sidebar-main li:hover {
        background-color: #ededed;
    }

    div.sidebar-main a:hover {
        text-decoration: underline;
    }

    /* Breadcrumb styles */
    span[itemprop="name"] {
        color: #1EA19B;
        text-transform: uppercase;
    }

    .trail-items li::after {
        padding: 0 0.3em;
        content: "\/";
    }

    .accordion li a {
        padding: 5px 15px;
    }

    .accordion li ul.submenu a {
        color: #606060;
    }

    .accordion li ul li {
        border-bottom: 1px solid #e3e3e3;
    }

</style>
