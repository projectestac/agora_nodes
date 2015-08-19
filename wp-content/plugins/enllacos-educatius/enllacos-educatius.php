<?php
/*
  Plugin Name: Enllaços educatius
  Plugin URI: http://agora.xtec.cat/nodes/plugins/enllaços-educatius
  Description: Giny d'enllaços educatius del Departament d'Ensenyament i la xarxa XTEC.
  Version: 1.0
  Author: Xavier Meler
  Author URI: https://github.com/jmeler
  License: GPLv2
 */

// Register widget
add_action('widgets_init', function() {
    register_widget('xtec_widget');
});

class XTEC_Widget extends WP_Widget {

    public $recursos = [
        'ensenyament' => ['nom' => "Dep.Ensenyament", 'url' => 'http://www20.gencat.cat/portal/site/ensenyament', 'img' => 'ensenyament-icon.png', 'desc' => 'Pàgina del Departament d\'ensenyament'],
        'xtec' => ['nom' => "XTEC", 'url' => 'http://xtec.cat', 'img' => 'xtec-icon.png', 'desc' => 'Recursos educatius'],
        'edu365' => ['nom' => "Edu365", 'url' => 'http://edu365.cat', 'img' => 'edu365-icon.png', 'desc' => 'Recursos educatius'],
        'edu3' => ['nom' => "Edu3", 'url' => 'http://www.edu3.cat', 'img' => 'edu3-icon.png', 'desc' => 'Videos educatius'],
        'xarxa-docent' => ['nom' => "Xarxa Docent", 'url' => 'http://educat.xtec.cat', 'img' => 'xarxa-docent-icon.png', 'desc' => 'Xarxa de support amb més de 10.000 docents inscrits'],
        'ateneu' => ['nom' => "Ateneu", 'url' => 'http://ateneu.xtec.cat/wikiform/wikiexport/cursos/index', 'img' => 'ateneu-icon.png', 'desc' => 'Materials i recursos per la formació'],
        'alexandria' => ['nom' => "Alexandria", 'url' => 'http://alexandria.xtec.cat', 'img' => 'alexandria-icon.png', 'desc' => 'Cursos moodle i activitats PDI per descarregar'],
        'linkat' => ['nom' => "Linkat", 'url' => 'http://linkat.xtec.cat/', 'img' => 'linkat-icon.png', 'desc' => 'Linux pels centres educatius'],
        'jclic' => ['nom' => "JClic", 'url' => 'http://clic.xtec.cat/ca/jclic/', 'img' => 'jclic-icon.png', 'desc' => 'Activitats jClic'],
        'merli' => ['nom' => "Merlí", 'url' => 'http://aplitic.xtec.cat/merli', 'img' => 'merli-icon.png', 'desc' => 'Catàleg de recursos XTEC'],
        'arc' => ['nom' => "ARC", 'url' => 'http://apliense.xtec.cat/arc/', 'img' => 'arc-icon.png', 'desc' => 'Aplicació de recursos al Currículum'],
        'odissea' => ['nom' => "Odissea", 'url' => 'http://odissea.xtec.cat', 'img' => 'odissea-icon.png', 'desc' => 'Entorn virtual de formació per a docents'],
        'ampa' => ['nom' => "AMPA", 'url' => '', 'img' => 'ampa-icon.png', 'desc' => 'La associació de Pares d\'alumnes del centre'],
        'escola-verda' => ['nom' => "Escola verda", 'url' => 'http://mediambient.gencat.cat/ca/05_ambits_dactuacio/educacio_i_sostenibilitat/educacio_per_a_la_sostenibilitat/escoles_verdes', 'img' => 'escola-verda-icon.png', 'desc' => 'Escola verda'],
        'atri' => ['nom' => "ATRI", 'url' => 'https://atri.gencat.cat', 'img' => 'atri-icon.png', 'desc' => 'Portal ATRI'],
        'saga' => ['nom' => "SAGA", 'url' => 'https://saga.xtec.cat/entrada', 'img' => 'saga-icon.png', 'desc' => 'Aplicatiu SAGA'],
        'familia-escola' => ['nom' => "Familia i escola", 'url' => 'http://www20.gencat.cat/portal/site/familiaescola/', 'img' => 'familiaescola-icon.png', 'desc' => 'Pàgina amb consells i recursos per les famílies'],
        'internet-segura' => ['nom' => "Internet Segura", 'url' => 'http://www.xtec.cat/web/recursos/tecinformacio/internet_segura', 'img' => 'internet-segura-icon.png', 'desc' => 'Recursos per utilitzar Internet de manera segura'],
        'moodle' => array('nom' => "MOODLE", 'url' => '', 'img' => 'moodle-icon.png', 'desc' => 'Enllaç al moodle del centre'),
        'portalcentre' => ['nom' => "Portal de centre", 'url' => 'http://educacio.gencat.cat/portal/page/portal/EducacioIntranet/Benvinguda', 'img' => 'portalcentre-icon.png', 'desc' => 'Enllaç al portal de centre'],
        'intraweb' => ['nom' => "Intraweb", 'url' => '', 'img' => 'intraweb-icon.png', 'desc' => 'Enllaç a la intraweb'],
        'epergam' => ['nom' => "ePergam", 'url' => '', 'img' => 'epergam-icon.png', 'desc' => 'Aplicatiu de la biblioteca escolar'],
        'lamevaxtec' => ['nom' => "La meva XTEC", 'url' => 'https://sites.google.com/a/xtec.cat/aplicacionsxtec/', 'img' => 'lamevaxtec-icon.png', 'desc' => 'Enllaç a l\'espai d\'usuari XTEC'],
    ];
    
    public $recursosXtec = ['ensenyament', 'xtec', 'edu365', 'edu3', 'xarxa-docent', 'ateneu', 'alexandria',
        'linkat', 'jclic', 'merli', 'arc', 'odissea', 'atri', 'saga', 'familia-escola',
        'internet-segura', 'portalcentre', 'intraweb', 'epergam', 'lamevaxtec'];

    // Create widget
    public function __construct() {
        parent::__construct(
                'xtec_widget', // Base ID
                'Enllaços Educatius', // Name
                array('description' => 'Enllaços a portals, recursos i serveis de la Xarxa Telemàtica Educativa de Catalunya (XTEC)')
        );
        $this->recursos['moodle']['url'] = get_home_url() . "/moodle";
        $this->recursos['ampa']['url'] = get_home_url() . "/ampa";
        $this->recursos['intraweb']['url'] = get_home_url() . "/intranet";
        $this->recursos['epergam']['url'] = "http://aplitic.xtec.cat/epergam/web/biblioteca.jsp?codi=" . SCHOOL_CODE;
    }


    // Front-End Display of the Widget
    public function widget($args, $instance) {

        extract($args);
        // Saved widget options
        $title = $instance['title'];

        if (trim($instance['ampa_url']) != '') {
            $this->recursos["ampa"]["url"] = $instance['ampa_url'];
        }

        if (trim($instance['escola-verda_url']) != '') {
            $this->recursos["escola-verda"]["url"] = $instance['escola-verda_url'];
        }

        if (trim($instance['moodle_url']) != '') {
            $this->recursos["moodle"]["url"] = $instance['moodle_url'];
        }

        // Display information
        echo $before_widget;
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }
        echo "<div class='grid-icon'>";
        foreach ($this->recursos as $idRecurs => $nomRecurs) {
            $idRecurs = $instance[$idRecurs];
            if (!empty($idRecurs)) {
                echo "<a target='_blank' title=\"" . $nomRecurs['nom'] . "\" href=\"" . esc_url($nomRecurs['url']) . "\"><img class=\"iconedu\" src=\"" . get_stylesheet_directory_uri() . "/custom-tac/imatges/" . $nomRecurs['img'] . "\"></a>";
            }
        }
        echo "</div>";
        echo $after_widget;
    }


    // Back-end form of the Widget
    public function form($instance) {
        // Check for values
        $title = isset($instance['title']) ? $instance['title'] : "Enllaços educatius";
        $this->recursos["ampa"]["url"] = $instance['ampa_url'];
        $this->recursos["escola-verda"]["url"] = $instance['escola-verda_url'];
        $this->recursos["moodle"]["url"] = $instance['moodle_url'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Títol:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <label>Tria enllaços:</label><br> 
        <?php foreach ($this->recursos as $idRecurs => $nomRecurs) { ?>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance[$idRecurs], 'on'); ?> id="<?php echo $this->get_field_id($idRecurs); ?>" name="<?php echo $this->get_field_name($idRecurs); ?>" /> 
                <label for="<?php echo $this->get_field_id($idRecurs); ?>"><?php echo "<strong>" . $nomRecurs['nom'] . "</strong> (" . $nomRecurs['desc'] . ") <a target='_blank' href=\"" . esc_url($nomRecurs['url']) . "\">>></a>"; ?><br>
            <?php if (!in_array($idRecurs, $this->recursosXtec)) { ?>
                        Adreça web:
                        <input class="widefat" id="<?php echo $this->get_field_id($idRecurs); ?>_url" name="<?php echo $this->get_field_name($idRecurs . "_url"); ?>" type="text" value="<?php echo esc_attr($nomRecurs['url']); ?>">  
                    <?php } ?>
                </label>
            </p>
        <?php } ?>
        <?php
    }


    // Sanitize and return the safe form values
    public function update($new_instance, $old_instance) {

        $instance = array();

        $instance['title'] = (!empty($new_instance['title']) ) ? sanitize_text_field($new_instance['title']) : '';

        foreach ($this->recursos as $idRecurs => $nomRecurs) {
            $instance[$idRecurs] = (!empty($new_instance[$idRecurs]) ) ? sanitize_text_field($new_instance[$idRecurs]) : '';
        }

        $instance['ampa_url'] = (!empty($new_instance['ampa_url']) ) ? sanitize_text_field($new_instance['ampa_url']) : get_home_url() . "/ampa";
        $instance['escola-verda_url'] = (!empty($new_instance['escola-verda_url']) ) ? sanitize_text_field($new_instance['escola-verda_url']) : 'http://www.gencat.cat/mediamb/escolesverdes/';
        $instance['moodle_url'] = (!empty($new_instance['moodle_url']) ) ? sanitize_text_field($new_instance['moodle_url']) : get_home_url() . "/moodle";

        return $instance;
    }
}
