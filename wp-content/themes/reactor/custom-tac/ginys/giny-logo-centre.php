<?php

class Logo_Centre_Widget extends WP_Widget {

    // Create widget
    public function __construct() {
        parent::__construct(
                'logo_centre_widget', // Base ID
                'Fitxa del centre', // Name
                array('description' => 'Mostra les dades i, opcionalment, el logotip del centre. La informació es defineix a Aparença -> Personalitza -> Identificació del centre')
        );
    }

    // Front-End Display of the Widget
    public function widget($args, $instance) {

        // Saved widget options
        $title = $instance['title'];
        echo $args['before_widget'];

        if (!empty($title)) {
            echo '<h4 class="widget-title">' . $title . '</h4>';
        }

        // Check options to print url link or mailto link
        $contacteCentre = reactor_option('contacteCentre');
        $correuCentre = reactor_option('correuCentre');
        $contacte_mobile_enabled = false;

        ( ! empty($contacteCentre) ) ? $contacte = $contacteCentre : $contacte = false;
        ( ! empty($correuCentre) ) ? $contacte_mobile = "mailto:" . $correuCentre : $contacte_mobile = false;

        if ( $contacte == false && $contacte_mobile != false ){
            $contacte = $contacte_mobile;
            $contacte_mobile_enabled = true;
        } else if ( $contacte_mobile == false && $contacte != false ){
            $contacte_mobile = $contacte;
        }

        ?>
        <div class="targeta_id_centre row">
            <?php
            if (reactor_option('logo_image')) {
                if (reactor_option('logo_inline')) {
                    $class_logo = "logo_inline";
                    $class_addr = "addr-centre";
                    $amplada = "6";
                } else {
                    $class_logo = "logo_clear";
                    $class_addr = "logo_clear";
                    $amplada = "12";
                }
                ?>
                <div class="<?php reactor_columns(array($amplada, 12));
                echo " " . $class_logo; ?> hide-for-small"> 
                    <img src="<?php echo reactor_option('logo_image'); ?>">					
                </div> 
            <?php
            } else {
                $amplada = "12";
                $class = "no_logo";
            }
            ?>

        <?php
            list($postal_code, $locality) = explode( ' ', reactor_option('cpCentre'), 2);
        ?>
            <div class="<?php reactor_columns($amplada); echo ' ' . $class_addr; ?> ">
                <div class="vcard">
                    <span id="tar-nomCentre"><?php echo reactor_option('nomCanonicCentre'); ?></span>
                    <div class="adr">
                        <span class="street-address"><?php echo reactor_option('direccioCentre'); ?></span><br>
                        <span class="postal-code"><?php echo trim($postal_code); ?></span> 
                        <span class="locality"><?php echo trim($locality); ?></span>  
                        <span class="region" title="Catalunya">Catalunya</span>
                        <span class="country-name">Espanya</span>
                        <div class="tel">
                            <span><?php echo reactor_option('telCentre'); ?></span>
                        </div>
                        <?php

                            // Get home url to check behavior target link
                            $currentDomain = get_home_url();
                            $searchDomain = array('http://','https://');
                            $currentDomain = str_replace($searchDomain,'',$currentDomain);
                            $contacteDomain = str_replace($searchDomain,'',$contacte);

                            // Check if googleMaps is empty to show or not
                            $showPipe = false;
                            $emptyMaps = reactor_option('googleMaps');
                            if ( ! empty($emptyMaps)){
                                $showPipe = true;
                                if ( strpos(reactor_option('googleMaps'),$currentDomain) !== false ){
                        ?>
                                    <a id="tar-mapa" href="<?php echo reactor_option('googleMaps'); ?>">mapa</a>
                        <?php   
                                } else if ( strpos(reactor_option('googleMaps'),'http') === false ){
                                    if ( strpos(reactor_option('googleMaps'),'.') !== false ){
                        ?>
                                        <a id="tar-mapa" target="_blank" href="<?php echo esc_url("http://" . reactor_option('googleMaps')); ?>">mapa</a>
                        <?php
                                    } else {
                                        if ( substr ( trim(reactor_option('googleMaps')) , 0 , 1 ) == '/' ){
                        ?>
                                            <a id="tar-mapa" href=" <?php echo esc_url(get_home_url() . reactor_option('googleMaps')); ?>">mapa</a>
                        <?php
                                        } else {
                        ?>
                                            <a id="tar-mapa" href=" <?php echo esc_url(get_home_url() . '/' . reactor_option('googleMaps')); ?>">mapa</a>
                        <?php
                                        }
                                    }
                                } else {
                        ?>
                                    <a id="tar-mapa" target="_blank" href="<?php echo reactor_option('googleMaps'); ?>">mapa</a>
                        <?php
                                }
                            } 
                        ?>
                        <?php 
                            // Check if $contacte is empty to show or not
                            if ( $contacte != false && $contacte_mobile != false ){
                                if( $showPipe == true ){
                        ?>
                                    <span class="pipe" >|</span>
                        <?php 
                                } 

                                if ( $contacte_mobile_enabled == true ){
                        ?>
                                    <a id="tar-contacte" href="<?php echo esc_url($contacte); ?>">contacte</a>
                        <?php 
                                } else {
                                    if ( strpos($contacteDomain,$currentDomain) !== false ){
                        ?>
                                        <a id="tar-contacte" href="<?php echo $contacte; ?>">contacte</a>
                        <?php
                                    } else if ( strpos($contacte,'http') === false ){
                                        if ( strpos($contacte,'.') !== false ){
                        ?>
                                            <a id="tar-contacte" target="_blank" href="<?php echo esc_url("https://" . $contacte); ?>">contacte</a>
                        <?php
                                        } else {
                                            if ( substr ( trim($contacte) , 0 , 1 ) == '/' ){
                        ?>
                                                <a id="tar-contacte" href="<?php echo esc_url($currentDomain . $contacte); ?>">contacte</a>
                        <?php
                                            } else {
                        ?>
                                                <a id="tar-contacte" href="<?php echo esc_url($currentDomain . '/' . $contacte); ?>">contacte</a>
                        <?php
                                            }
                                        }
                                    } else {
                        ?>
                        <a id="tar-contacte" target="_blank" href="<?php echo esc_url($contacte); ?>">contacte</a>
                        <?php
                                    }
                                }
                            }
                        ?>
                    </div>		 
                </div>	
            </div>		 
        </div>
        <?php echo $args['after_widget']; ?>
        <?php
    }

    // End function widget
    // Back-end form of the Widget
    public function form($instance) {

        // Check for values
        if (isset($instance['title'])) {
            $title = $instance['title'];
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Títol:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <?php
    }

    // Sanitize and return the safe form values
    public function update($new_instance, $old_instance) {
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}

// Register widget
add_action('widgets_init', function() {
    register_widget('logo_centre_widget');
}
);
