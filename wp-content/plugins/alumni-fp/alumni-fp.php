<?php
/**
 * @package Nodes_AlumniFP
 * @version 1.0.0
 */
/**
 * Plugin Name: Nodes: AlumniFP
 * Plugin URI: https://agora.xtec.cat/nodes/alumni-fp
 * Description: Extensió que genera automàticament les pàgines d'Alumni FP
 * Author: Xavier Meler
 * Version: 1.0.0
 * Author URI: https://dossier.xtec.cat/jmeler/
 */

/*
 * Ginys al tauler d'Alumni FP
 */
function extensions_dashboard_widgets_alumni_fp() {
    wp_add_dashboard_widget('extensions_dashboard_widget_alumni_fp', 'Alumni FP', 'extensions_dashboard_widget_alumni_fp');
}

add_action('wp_dashboard_setup', 'extensions_dashboard_widgets_alumni_fp');

function extensions_dashboard_widget_alumni_fp() {
    $page = get_page_by_path('alumnifp');

    if (current_user_can('activate_plugins')) {
        echo '
            <div id="dashboard_box_alumni_fp">
            <div style="text-align:center">
                <img height="150px" src="https://projectes.xtec.cat/impulsfp/wp-content/uploads/usu2078/2023/03/AlumniFP.png">
            </div>
            <p>
                <strong>Alumni FP</strong> és un projecte per ajudar als centres d\'FP a crear comunitats d’Alumni amb la finalitat de mantenir els lligams entre el centre educatiu i l’exalumnat.
            </p>
            <div style="text-align:center">
                <a class="button" href="https://projectes.xtec.cat/impulsfp/alumnifp/">Més informació</a>
            </div>
            <p>Per importar un espai Alumini FP en el Nodes del vostre centre segueix aquests passos:</p>
            1. <a target="_blank" href="https://ja.cat/alumnifp-export">Descarrega</a> el fitxer que conté les pàgines de l\'espai Alumni. És un fitxer amb extensió xml.<br /><br />
            2. <a target="_blank" href="admin.php?import=wordpress">Importa</a> el fitxer descarregat. Es crearan un conjunt de pàgines relacionades amb Alumni FP.<br /><br />
            3. <a target="_blank" href="' . get_permalink($page->ID) . '">Visita</a> l\'espai AlumniFP en el teu Nodes.
            </div>
        ';
    }
}
