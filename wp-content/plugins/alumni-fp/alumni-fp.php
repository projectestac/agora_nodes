<?php
/**
 * @package Nodes_AlumniFP
 *
 * Plugin Name: Nodes: AlumniFP
 * Plugin URI: https://agora.xtec.cat/nodes/alumni-fp
 * Description: Extensió que genera automàticament el giny d'Alumni FP
 * Author: Xavier Meler
 * Version: 1.0.1
 * Author URI: https://dossier.xtec.cat/jmeler/
 */

add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'extensions_dashboard_widget_alumni_fp',
        'Alumni FP',
        'extensions_dashboard_widget_alumni_fp'
    );
});

function extensions_dashboard_widget_alumni_fp() {

    if (current_user_can('activate_plugins')) {
        echo '
            <div style="text-align:center">
                <img height="150px"
                     src="https://projectes.xtec.cat/impulsfp/wp-content/uploads/usu2078/2023/03/AlumniFP.png"
                     alt="" />
            </div>
            <p>
                <strong>Alumni FP</strong> és un projecte per ajudar als centres d\'FP a crear comunitats
                 d\'Alumni amb la finalitat de mantenir els lligams entre el centre educatiu i l\'exalumnat.
            </p>
            <div style="text-align:center">
                <a class="button" href="https://projectes.xtec.cat/impulsfp/alumnifp/">Més informació</a>
            </div>
            <p>Per importar un espai Alumini FP en el Nodes del vostre centre seguiu aquests passos:</p>
            1. <a target="_blank" href="https://ja.cat/alumnifp-export">Descarregueu</a> el fitxer que conté
            les pàgines de l\'espai Alumni. És un fitxer amb extensió xml.<br /><br />
            2. <a target="_blank" href="admin.php?import=wordpress">Importeu</a> el fitxer descarregat
            marcant "Descarregar i importar adjunts". Es crearan un conjunt de pàgines relacionades amb
            Alumni FP.<br /><br />
            3. Afegiu la <a target="_blank" href="' . home_url() . '/lexalumnat-4/">pàgina inicial d\'Alumni</a>
            al menú del vostre web.
        ';
    }

}
