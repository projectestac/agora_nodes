<?php

function extract_news_params($astra_nodes_options): array {

    return [
        'postsToShow' => $astra_nodes_options['front_page_news_num_posts'] ?? 20,
        'category' => $astra_nodes_options['front_page_news_category'] ?? 29,
    ];

}

function get_front_page_news($astra_nodes_options, $category_link): string {

    $params = extract_news_params($astra_nodes_options);

    return '<!-- wp:getwid/post-carousel {"postsToShow":' . $params['postsToShow'] . ',"taxonomy":["category"],"terms":["category[' . $params['category'] . ']"],"sliderSlidesToShowDesktop":"3","sliderSlidesToScroll":"3","sliderInfinite":false} /-->
            <!-- wp:paragraph {"align":"center"} -->
               <p class="has-text-align-center">
                   <a href="' . $category_link . '">' . __('All the news', 'astra-nodes') . '</a>
               </p>
            <!-- /wp:paragraph -->';

}
