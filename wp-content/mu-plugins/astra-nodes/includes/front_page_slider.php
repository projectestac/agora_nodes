<?php

function extract_slider_params($astra_nodes_options): array {

    return [
        'minHeight' => $astra_nodes_options['front_page_slider_min_height'],
        'autoplay' => $astra_nodes_options['front_page_slider_autoplay'] ? 'true' : 'false',
        'arrows' => $astra_nodes_options['front_page_slider_arrows'] ?? 'inside',
        'dots' => $astra_nodes_options['front_page_slider_dots'] ?? 'inside',
        'image_1' => $astra_nodes_options['front_page_slider_image_1'],
        'image_1_id' => attachment_url_to_postid($astra_nodes_options['front_page_slider_image_1']),
        'heading_1' => $astra_nodes_options['front_page_slider_heading_1'],
        'text_1' => $astra_nodes_options['front_page_slider_text_1'],
        'image_2' => $astra_nodes_options['front_page_slider_image_2'],
        'image_2_id' => attachment_url_to_postid($astra_nodes_options['front_page_slider_image_2']),
        'heading_2' => $astra_nodes_options['front_page_slider_heading_2'],
        'text_2' => $astra_nodes_options['front_page_slider_text_2'],
    ];

}

function get_front_page_slider($astra_nodes_options): string {

    $params = extract_slider_params($astra_nodes_options);

    return '<!-- wp:getwid/media-text-slider {"slideCount":4,"minHeight":"' . $params['minHeight'] . 'px","sliderAutoplay":' . 
        $params['autoplay'] . ',"sliderArrows":"inside","sliderDots":"inside"} -->
<div class="wp-block-getwid-media-text-slider wp-block-getwid-media-text-slider--current-slide-1 has-arrows-inside has-dots-inside" data-labels="[&quot;Slide 1&quot;,&quot;Slide 2&quot;,&quot;Slide 3&quot;,&quot;Slide 4&quot;]" data-animation="fadeIn" data-duration="1500ms" data-delay="0ms">
    <div class="wp-block-getwid-media-text-slider__slides-wrapper">

        <div class="wp-block-getwid-media-text-slider__content" data-slide-autoplay="true" data-slide-pause-on-hover="true" data-slide-autoplay-speed="5000" data-slide-speed="1000" data-infinite="true">
            <!-- wp:getwid/media-text-slider-slide {"outerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
            <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide wp-block-getwid-media-text-slider-slide__content-wrapper slide-1">
                <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide__content">
                    <!-- wp:getwid/media-text-slider-slide-content {"mediaId":8539,"mediaType":"image","innerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
                    <div class="wp-block-getwid-media-text-slider-slide-content">
                        <figure class="wp-block-getwid-media-text-slider-slide-content__media">
                            <img src="' . $params['image_1'] . '" alt="" class="wp-block-getwid-media-text-slider-slide-content__image wp-image-' . $params['image_1_id'] . '"/>
                            <div class="wp-block-getwid-media-text-slider-slide-content__media-overlay" style="opacity:0.3">
                            </div>
                        </figure>
                        <div class="wp-block-getwid-media-text-slider-slide-content__content">
                            <div class="wp-block-getwid-media-text-slider-slide-content__content-wrapper">
                                <!-- wp:heading {"placeholder":"Write heading…"} -->
                                <h2 class="wp-block-heading">' . $params['heading_1'] . '</h2>
                                <!-- /wp:heading -->
                                <!-- wp:paragraph {"placeholder":"Write text…"} -->
                                <p>' . $params['text_1'] . '</p>
                                <!-- /wp:paragraph -->
                            </div>
                        </div>
                    </div>
                    <!-- /wp:getwid/media-text-slider-slide-content -->
                </div>
            </div>
            <!-- /wp:getwid/media-text-slider-slide -->

            <!-- wp:getwid/media-text-slider-slide {"slideId":2,"outerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
            <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide wp-block-getwid-media-text-slider-slide__content-wrapper slide-2">
                <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide__content">
                    <!-- wp:getwid/media-text-slider-slide-content {"mediaId":8536,"mediaType":"image","innerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
                    <div class="wp-block-getwid-media-text-slider-slide-content">
                        <figure class="wp-block-getwid-media-text-slider-slide-content__media">
                            <img src="' . WP_SITEURL . 'wp-content/uploads/usu9/2024/03/erol-ahmed-305920-unsplash.jpg" alt="" class="wp-block-getwid-media-text-slider-slide-content__image wp-image-8536"/>
                            <div class="wp-block-getwid-media-text-slider-slide-content__media-overlay" style="opacity:0.3">
                            </div>
                        </figure>
                        <div class="wp-block-getwid-media-text-slider-slide-content__content">
                            <div class="wp-block-getwid-media-text-slider-slide-content__content-wrapper">
                                <!-- wp:heading {"placeholder":"Write heading…"} -->
                                <h2 class="wp-block-heading">Alimentació...</h2>
                                <!-- /wp:heading -->
                                <!-- wp:paragraph {"placeholder":"Write text…"} -->
                                <p></p>
                                <!-- /wp:paragraph -->
                            </div>
                        </div>
                    </div>
                    <!-- /wp:getwid/media-text-slider-slide-content -->
                </div>
            </div>
            <!-- /wp:getwid/media-text-slider-slide -->

            <!-- wp:getwid/media-text-slider-slide {"slideId":3,"outerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
            <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide wp-block-getwid-media-text-slider-slide__content-wrapper slide-3">
                <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide__content">
                    <!-- wp:getwid/media-text-slider-slide-content {"mediaId":8537,"mediaType":"image","innerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
                    <div class="wp-block-getwid-media-text-slider-slide-content">
                        <figure class="wp-block-getwid-media-text-slider-slide-content__media">
                            <img src="' . WP_SITEURL . 'wp-content/uploads/usu9/2024/03/etienne-beauregard-riverin-48305-unsplash.jpg" alt="" class="wp-block-getwid-media-text-slider-slide-content__image wp-image-8537"/>
                            <div class="wp-block-getwid-media-text-slider-slide-content__media-overlay" style="opacity:0.3">
                            </div>
                        </figure>
                        <div class="wp-block-getwid-media-text-slider-slide-content__content">
                            <div class="wp-block-getwid-media-text-slider-slide-content__content-wrapper">
                                <!-- wp:heading {"placeholder":"Write heading…"} -->
                                <h2 class="wp-block-heading">Arquitectura...</h2>
                                <!-- /wp:heading -->
                                <!-- wp:paragraph {"placeholder":"Write text…"} -->
                                <p></p>
                                <!-- /wp:paragraph -->
                            </div>
                        </div>
                    </div>
                    <!-- /wp:getwid/media-text-slider-slide-content -->
                </div>
            </div>
            <!-- /wp:getwid/media-text-slider-slide -->

            <!-- wp:getwid/media-text-slider-slide {"slideId":4,"outerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
            <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide wp-block-getwid-media-text-slider-slide__content-wrapper slide-4">
                <div style="min-height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide__content">
                    <!-- wp:getwid/media-text-slider-slide-content {"mediaId":8538,"mediaType":"image","innerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
                    <div class="wp-block-getwid-media-text-slider-slide-content">
                        <figure class="wp-block-getwid-media-text-slider-slide-content__media">
                            <img src="' . WP_SITEURL . 'wp-content/uploads/usu9/2024/03/michael-discenza-unsplash.jpg" alt="" class="wp-block-getwid-media-text-slider-slide-content__image wp-image-8538"/>
                            <div class="wp-block-getwid-media-text-slider-slide-content__media-overlay" style="opacity:0.3">
                            </div>
                        </figure>
                        <div class="wp-block-getwid-media-text-slider-slide-content__content">
                            <div class="wp-block-getwid-media-text-slider-slide-content__content-wrapper">
                                <!-- wp:heading {"placeholder":"Write heading…"} -->
                                <h2 class="wp-block-heading">Urbanisme...</h2>
                                <!-- /wp:heading -->
                                <!-- wp:paragraph {"placeholder":"Write text…"} -->
                                <p></p>
                                <!-- /wp:paragraph -->
                            </div>
                        </div>
                    </div>
                    <!-- /wp:getwid/media-text-slider-slide-content -->
                </div>
            </div>
            <!-- /wp:getwid/media-text-slider-slide -->
        </div>

    </div>
</div>
<!-- /wp:getwid/media-text-slider -->';

}
