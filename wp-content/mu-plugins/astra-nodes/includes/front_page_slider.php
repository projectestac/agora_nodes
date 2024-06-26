<?php

function extract_slider_params($astra_nodes_options): array {

    $params = [
        'minHeight' => $astra_nodes_options['front_page_slider_min_height'] ?? 200,
        'autoplay' => $astra_nodes_options['front_page_slider_autoplay'] ? 'true' : 'false',
        'arrows' => $astra_nodes_options['front_page_slider_arrows'] ?? 'yes',
        'dots' => $astra_nodes_options['front_page_slider_dots'] ?? 'yes',
    ];

    // Minimum height.
    if ($params['minHeight'] < 200) {
        $params['minHeight'] = 200;
    }

    switch ($params['arrows']) {
        case 'yes':
            $params['class_arrows'] = 'has-arrows-inside';
            break;
        case 'no':
            $params['class_arrows'] = 'has-arrows-none';
            break;
        default:
            $params['class_arrows'] = 'has-arrows-inside';
    }

    switch ($params['dots']) {
        case 'yes':
            $params['class_dots'] = 'has-dots-inside';
            break;
        case 'no':
            $params['class_dots'] = 'has-dots-none';
            break;
        default:
            $params['class_dots'] = 'has-dots-inside';
    }

    for ($i = 1, $count = 0; $i <= 5; $i++) {

        if (empty($astra_nodes_options['front_page_slider_image_' . $i]) &&
            empty($astra_nodes_options['front_page_slider_heading_' . $i]) &&
            empty($astra_nodes_options['front_page_slider_text_' . $i])) {
            continue;
        }

        $count++;

        $params['image_' . $count] = $astra_nodes_options['front_page_slider_image_' . $i] ?? '';
        $params['image_' . $count . '_id'] = (!empty($params['image_' . $count])) ?
            (attachment_url_to_postid($params['image_' . $count]) ?? 0) : 0;
        $params['heading_' . $count] = $astra_nodes_options['front_page_slider_heading_' . $i] ?? '';
        $params['text_' . $count] = $astra_nodes_options['front_page_slider_text_' . $i] ?? '';
        $params['url_' . $count] = $astra_nodes_options['front_page_slider_link_' . $i] ?? '';

    }

    $params['slideCount'] = $count;

    return $params;

}

function get_front_page_slider($astra_nodes_options): string {

    $params = extract_slider_params($astra_nodes_options);

    for ($i = 1, $data_labels_array = []; $i <= $params['slideCount']; $i++) {
        $data_labels_array[] = '&quot;' . __('Slide', 'astra-nodes') . ' ' . $i . '&quot;';
    }

    $data_labels = '[' . implode(',', $data_labels_array) . ']';

    $slider = '<!-- wp:getwid/media-text-slider {"slideCount":' . $params['slideCount'] . ',"minHeight":"' . $params['minHeight'] . 'px","sliderAutoplay":' . $params['autoplay'] . ',"sliderArrows":"none","sliderDots":"inside"} -->
    <div id="slider-height-container" class="wp-block-getwid-media-text-slider wp-block-getwid-media-text-slider--current-slide-1 ' . $params['class_arrows'] . ' ' . $params['class_dots'] . '"
         data-labels="' . $data_labels . '"
         data-animation="fadeIn" data-duration="1500ms" data-delay="0ms">
        <div class="wp-block-getwid-media-text-slider__slides-wrapper">
            <div class="wp-block-getwid-media-text-slider__content" data-slide-autoplay="' . $params['autoplay'] . '" data-slide-pause-on-hover="true" data-slide-autoplay-speed="5000" data-slide-speed="1000" data-infinite="true">';

    for ($i = 1; $i <= $params['slideCount']; $i++) {
        $slider .= '
            <!-- wp:getwid/media-text-slider-slide {"slideId":' . $i . ',"outerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
            <div style="height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide wp-block-getwid-media-text-slider-slide__content-wrapper slide-' . $i . '">
                <div style="height:' . $params['minHeight'] . 'px" class="wp-block-getwid-media-text-slider-slide__content">
                    <!-- wp:getwid/media-text-slider-slide-content {"mediaId":' . $params['image_' . $i . '_id'] . ',"mediaType":"image","innerParent":{"attributes":{"minHeight":"' . $params['minHeight'] . 'px","overlayOpacity":"30","imageSize":"full"}}} -->
                    <div class="wp-block-getwid-media-text-slider-slide-content">
                        <a id="slider-link-' . $i . '" href="' . $params['url_' . $i] . '" target="_blank">
                            <figure class="wp-block-getwid-media-text-slider-slide-content__media">
                                <img id="slider-image-' . $i . '" src="' . $params['image_' . $i] . '" alt="" class="wp-block-getwid-media-text-slider-slide-content__image wp-image-' . $params['image_' . $i . '_id'] . '"/>
                                <div class="wp-block-getwid-media-text-slider-slide-content__media-overlay" style="opacity:0.3">
                                </div>
                            </figure>
                        </a>
                        <div class="wp-block-getwid-media-text-slider-slide-content__content">
                            <div class="wp-block-getwid-media-text-slider-slide-content__content-wrapper">
                                <!-- wp:heading {"placeholder":"' . __('Heading', 'astra-nodes') . '"} -->
                                <h2 id="slider-heading-' . $i . '" class="wp-block-heading">' . $params['heading_' . $i] . '</h2>
                                <!-- /wp:heading -->
                                <!-- wp:paragraph {"placeholder":"' . __('Text', 'astra-nodes') . '"} -->
                                <p id="slider-text-' . $i . '">' . $params['text_' . $i] . '</p>
                                <!-- /wp:paragraph -->
                            </div>
                        </div>
                    </div>
                    <!-- /wp:getwid/media-text-slider-slide-content -->
                </div>
            </div>
            <!-- /wp:getwid/media-text-slider-slide -->
            ';
    }

    $slider .= '</div>
            </div>
        </div>
        <!-- /wp:getwid/media-text-slider -->';

    return $slider;

}
