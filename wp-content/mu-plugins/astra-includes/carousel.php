<!-- IMPORTANT: Project need /wp-admin/post.php?post=9&action=edit to have a carousel slider (Carrusel d'imatges) -->

<script>
    // Function to check the presence of the target element and move it
    function checkAndMoveElement() {
        const elementToMove = document.querySelector("#post-9 > div");
        const targetContainer = document.querySelector("#ast-desktop-header > div.ast-below-header-wrap");

        if (elementToMove && targetContainer) {
            // Move the element
            targetContainer.appendChild(elementToMove);
            // Stop checking periodically once the element is moved
            clearInterval(checkInterval);
        }
    }

    // Check periodically for the presence of the target element
    const checkInterval = setInterval(checkAndMoveElement, 100); // Check every 100 ms (1 second)
</script>

<style>
    #ast-desktop-header > div.ast-below-header-wrap
    {
        margin-top: -1px;
    }
    
    #ast-desktop-header > div.ast-below-header-wrap > div.entry-content.clear > div.wp-block-getwid-images-slider.has-arrows-inside.has-dots-inside.has-images-center.has-cropped-images.has-fixed-height.getwid-init > div
    {
        max-width: none;
    }

    #ast-desktop-header > div.ast-below-header-wrap > div.entry-content.clear > div.wp-block-getwid-images-slider.has-arrows-inside.has-dots-inside.has-images-center.has-cropped-images.has-fixed-height.getwid-init > div > div,
    #ast-desktop-header > div.ast-below-header-wrap > div.entry-content.clear > div.wp-block-getwid-images-slider.has-arrows-inside.has-dots-inside.has-images-center.has-cropped-images.has-fixed-height.getwid-init > div > div > div > div:nth-child(1),
    div[id^="slick-slide"]
    {
        height: 500px !important;
    }

    #ast-desktop-header > div.ast-below-header-wrap > div.entry-content.clear > div.wp-block-getwid-images-slider.has-arrows-inside.has-dots-inside.has-images-center.has-cropped-images.has-fixed-height.getwid-init > div > div
    {
        border-radius: 0 0 40px 40px;
    }

    #ast-desktop-header > div.ast-below-header-wrap > div.ast-below-header-bar.ast-below-header.site-header-focus-item
    {
        position: absolute;
        top: 231px;
        z-index: 100;
        width: calc(100% - 2 * 25px);
        margin: 25px;
    }

    #ast-desktop-header > div.ast-main-header-wrap.main-header-bar-wrap > div > div
    {
        margin-left: 0;
        margin-right: 0;
        max-width: none;
        width: calc(100% - 36px);
    }
</style>