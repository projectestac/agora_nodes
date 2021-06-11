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
        'ensenyament' => ['nom' => "Dep.Educació", 'url' => 'http://educacio.gencat.cat/ca/inici/', 'img' => 'educacio-icon.png', 'desc' => 'Pàgina del Departament d\'Educació'],
        'xtec' => ['nom' => "XTEC", 'url' => 'http://xtec.gencat.cat', 'img' => 'xtec-icon.png', 'desc' => 'Recursos educatius'],
        'edu365' => ['nom' => "Edu365", 'url' => 'http://edu365.cat', 'img' => 'edu365-icon.png', 'desc' => 'Recursos educatius'],
        'edu3' => ['nom' => "Edu3", 'url' => 'http://www.edu3.cat', 'img' => 'edu3-icon.png', 'desc' => 'Videos educatius'],
        'ateneu' => ['nom' => "Ateneu", 'url' => 'http://ateneu.xtec.cat', 'img' => 'ateneu-icon.png', 'desc' => 'Materials i recursos per la formació'],
        'alexandria' => ['nom' => "Alexandria", 'url' => 'http://alexandria.xtec.cat', 'img' => 'alexandria-icon.png', 'desc' => 'Cursos moodle i activitats PDI per descarregar'],
        'linkat' => ['nom' => "Linkat", 'url' => 'http://linkat.xtec.cat/', 'img' => 'linkat-icon.png', 'desc' => 'Linux pels centres educatius'],
        'jclic' => ['nom' => "JClic", 'url' => 'http://clic.xtec.cat/ca/jclic/', 'img' => 'jclic-icon.png', 'desc' => 'Activitats jClic'],
        'merli' => ['nom' => "Merlí", 'url' => 'http://merli.xtec.cat', 'img' => 'merli-icon.png', 'desc' => 'Catàleg de recursos XTEC'],
        'arc' => ['nom' => "ARC", 'url' => 'http://apliense.xtec.cat/arc/', 'img' => 'arc-icon.png', 'desc' => 'Aplicació de recursos al Currículum'],
        'odissea' => ['nom' => "Odissea", 'url' => 'http://odissea.xtec.cat', 'img' => 'odissea-icon.png', 'desc' => 'Entorn virtual de formació per a docents'],
        'ampa' => ['nom' => "AMPA", 'url' => '', 'img' => 'ampa-icon.png', 'desc' => 'La associació de Pares d\'alumnes del centre'],
        'escola-verda' => ['nom' => "Escola verda", 'url' => 'http://mediambient.gencat.cat/ca/05_ambits_dactuacio/educacio_i_sostenibilitat/educacio_per_a_la_sostenibilitat/escoles_verdes', 'img' => 'escola-verda-icon.png', 'desc' => 'Escola verda'],
        'atri' => ['nom' => "ATRI", 'url' => 'https://atriportal.gencat.cat', 'img' => 'atri-icon.png', 'desc' => 'Portal ATRI'],
        'saga' => ['nom' => "SAGA", 'url' => 'https://saga.xtec.cat/entrada', 'img' => 'saga-icon.png', 'desc' => 'Aplicatiu SAGA'],
        'familia-escola' => ['nom' => "Familia i escola", 'url' => 'http://www20.gencat.cat/portal/site/familiaescola/', 'img' => 'familiaescola-icon.png', 'desc' => 'Pàgina amb consells i recursos per les famílies'],
        'internet-segura' => ['nom' => "Internet Segura", 'url' => 'http://www.xtec.cat/web/recursos/tecinformacio/internet_segura', 'img' => 'internet-segura-icon.png', 'desc' => 'Recursos per utilitzar Internet de manera segura'],
        'moodle' => ['nom' => "MOODLE", 'url' => '', 'img' => 'moodle-icon.png', 'desc' => 'Enllaç al moodle del centre'],
        'portalcentre' => ['nom' => "Portal de centre", 'url' => 'http://educacio.gencat.cat/portal/page/portal/EducacioIntranet/Benvinguda', 'img' => 'portalcentre-icon.png', 'desc' => 'Enllaç al portal de centre'],
        'epergam' => ['nom' => "ePergam", 'url' => '', 'img' => 'epergam-icon.png', 'desc' => 'Aplicatiu de la biblioteca escolar'],
        'lamevaxtec' => ['nom' => "La meva XTEC", 'url' => 'http://xtec.gencat.cat/ca/la-meva-xtec/', 'img' => 'lamevaxtec-icon.png', 'desc' => 'Enllaç a l\'espai d\'usuari XTEC'],
        'esfera' => ['nom' => "Esfer@", 'url' => 'https://bfgh.aplicacions.ensenyament.gencat.cat/bfgh/', 'img' => 'esfera-icon.png', 'desc' => "Enllaç al portal Esfer@"],
        'evalisa' => ['nom' => "eValisa", 'url' => 'http://idpeacat.gencat.cat/group/1/valisa', 'img' => 'evalisa-icon.png', 'desc' => "Enllaç al portal eValisa"],
        'ioc' => ['nom' => "Institut Obert de Catalunya", 'url' => 'http://ioc.xtec.cat/', 'img' => 'ioc-icon.png', 'desc' => "Enllaç al portal de l'Institut Obert de Catalunya"],
        'sinapsi' => ['nom' => "Sinapsi", 'url' => 'http://sinapsi.xtec.cat', 'img' => 'sinapsi-icon.png', 'desc' => "Enllaç al portal Sinapsi"],
        'serveiseducatius' => ['nom' => "Serveis educatius", 'url' => '', 'img' => 'serveis-icon.png', 'desc' => 'Enllaç al vostre servei educatiu'],
        'classroom' => ['nom' => "Google Classroom", 'url' => 'https://classroom.google.com/', 'img' => 'google_classroom-icon.png', 'desc' => 'Enllaç al vostre Google Classroom'],
    ];

    public $recursosXtec = ['ensenyament', 'xtec', 'edu365', 'edu3', 'ateneu', 'alexandria',
        'linkat', 'jclic', 'merli', 'arc', 'odissea', 'atri', 'saga', 'familia-escola',
        'internet-segura', 'portalcentre', 'epergam', 'lamevaxtec', 'esfera', 'evalisa', 'ioc', 'sinapsi'];

    // Create widget
    public function __construct() {

        parent::__construct(
                'xtec_widget', // Base ID
                'Enllaços Educatius', // Name
                array('description' => 'Enllaços a portals, recursos i serveis de la Xarxa Telemàtica Educativa de Catalunya (XTEC)')
        );

        $this->recursos['moodle']['url'] = get_home_url() . "/moodle";
        $this->recursos['ampa']['url'] = get_home_url() . "/ampa";
        $this->recursos['epergam']['url'] = "https://aplicacions.ensenyament.gencat.cat/epergam/web/biblioteca.jsp?codi=" . SCHOOL_CODE;

    }


    // Front-End Display of the Widget
    public function widget($args, $instance) {

        extract($args);
        // Saved widget options
        $title = $instance['title'];

        if ( isset($instance['ampa_url']) && trim($instance['ampa_url']) != '' ) {
            $this->recursos["ampa"]["url"] = $instance['ampa_url'];
        }

        if ( isset($instance['escola-verda_url']) && trim($instance['escola-verda_url']) != '' ) {
            $this->recursos["escola-verda"]["url"] = $instance['escola-verda_url'];
        }

        if ( isset($instance['moodle_url']) && trim($instance['moodle_url']) != '' ) {
            $this->recursos["moodle"]["url"] = $instance['moodle_url'];
        }

        if ( isset($instance['serveiseducatius_url']) && trim($instance['serveiseducatius_url']) != '' ) {
            $this->recursos["serveiseducatius"]["url"] = $instance['serveiseducatius_url'];
        }

        if ( isset($instance['classroom_url']) && trim($instance['classroom_url']) != '' ) {
            $this->recursos["classroom"]["url"] = $instance['classroom_url'];
        }

        // Display information
        echo $before_widget;
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }
        echo "<div class='grid-icon'>";
        foreach ($this->recursos as $idRecurs => $nomRecurs) {
            $idRecurs = isset($instance[$idRecurs])?$instance[$idRecurs]:'';
            if (!empty($idRecurs)) {
                echo "<a target='_blank' title=\"" . $nomRecurs['nom'] . "\" href=\"" . esc_url($nomRecurs['url']) . "\"><img alt=\"Logotip de " . $nomRecurs['nom'] . "\" class=\"iconedu\" src=\"" . get_template_directory_uri() . "/custom-tac/imatges/" . $nomRecurs['img'] . "\"></a>";
            }
        }
        echo "</div>";
        echo $after_widget;
    }

    // Back-end form of the Widget
    public function form($instance) {
        // Check for values

        $title = isset($instance['title']) ? $instance['title'] : "Enllaços educatius";
        $this->recursos["ampa"]["url"]              = isset($instance['ampa_url']) ? $instance['ampa_url'] : get_home_url() . "/ampa";
        $this->recursos["escola-verda"]["url"]      = isset($instance['escola-verda_url']) ? $instance['escola-verda_url'] : "http://www.gencat.cat/mediamb/escolesverdes/";
        $this->recursos["moodle"]["url"]            = isset($instance['moodle_url']) ? $instance['moodle_url'] : get_home_url() . "/moodle";
        $this->recursos["serveiseducatius"]["url"]  = isset($instance['serveiseducatius_url']) ? $instance['serveiseducatius_url'] : "";
        $this->recursos["classroom"]["url"]         = isset($instance['classroom_url']) ? $instance['classroom_url'] : "https://classroom.google.com/";

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Títol:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <label>Tria enllaços:</label><br>
        <?php foreach ($this->recursos as $idRecurs => $nomRecurs) { ?>
            <p>
                <input class="checkbox" type="checkbox" <?php echo ($instance[$idRecurs] == 'on') ? 'checked' : ''; ?> id="<?php echo $this->get_field_id($idRecurs); ?>" name="<?php echo $this->get_field_name($idRecurs); ?>
                <label for="<?php echo $this->get_field_id($idRecurs); ?>"><?php echo "<strong>" . $nomRecurs['nom'] . "</strong> (" . $nomRecurs['desc'] . ") <a target='_blank' href=\"" . esc_url($nomRecurs['url']) . "\">>></a>"; ?>
                    <br>
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
        $instance['serveiseducatius_url'] = (!empty($new_instance['serveiseducatius_url']) ) ? sanitize_text_field($new_instance['serveiseducatius_url']) : "";
        $instance['classroom_url'] = (!empty($new_instance['classroom_url']) ) ? sanitize_text_field($new_instance['classroom_url']) : "https://classroom.google.com/";

        return $instance;
    }
}
