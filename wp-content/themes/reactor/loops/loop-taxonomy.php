<?php
/**
 * The loop for displaying posts on the category
 *
 * @package Reactor
 * @subpackage loops
 * @since 1.0.0
 */
?>

<?php
global $card_bgcolor;
global $column_amplada;
global $posts_per_fila1;
global $posts_per_fila2;
global $posts_per_filan;

$card_colors = array("card_bgcolor1", "card_bgcolor2", "card_bgcolor3");
$rows = array ($posts_per_fila1, $posts_per_fila2, $posts_per_filan);
$aLayout = array(array(), array(), array());

foreach ($rows as $row => $posts_per_fila) {
    switch ($posts_per_fila) {
        case 1:
            $aLayout[$row] = array(12);
            break;
        case 3:
            $aLayout[$row] = array(4, 4, 4);
            break;
        case 4:
            $aLayout[$row] = array(3, 3, 3, 3);
            break;
        case 33:
            $aLayout[$row] = array(4, 8);
            break;
        case 66:
            $aLayout[$row] = array(8, 4);
            break;
        case 2:
        default:
            $aLayout[$row] = array(6, 6);
    }
}

$row = 0;
$num_posts = $wp_query->post_count;
while (have_posts()):
    echo '<div class="row">';
    $fila = ($row > 2) ? 2 : $row;
    if ($num_posts < count($aLayout[$fila])) {
        switch ($num_posts) {
            case 1:
                $columns = array(12);
                break;
            case 2:
                $columns = array(6, 6);
                break;
            case 3:
                $columns = array(4, 4, 4);
                break;
            case 4:
                $columns = array(3, 3, 3, 3);
                break;
        }
    } else {
        $columns = $aLayout[$fila];
    }
    foreach ($columns as $idcard => $column_amplada) {
        $card_bgcolor = $card_colors[((($row + 2) % 3) + $idcard + 1) % 3];
        the_post();
        $num_posts--;

        // Check if really there are a post
        if (!get_the_ID()) {
            break;
        }
        reactor_post_before();
        get_template_part('post-formats/format', "tac");
        reactor_post_after();
    }
    $row++;
    echo "</div>";
endwhile;
