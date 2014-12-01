<?php

function add_logo( $wp_admin_bar ) {
    
    $isConsorci = stripos(reactor_option('cpCentre'),"barcelona")?true:false;
    
    if ($isConsorci) {
	$args = array(
		'id'     => 'consorci',    
		'title'  => '<img src="'.get_stylesheet_directory_uri().'/custom-tac/imatges/logo_consorci.png">',
		'href' =>'http://www.edubcn.cat/ca/',
		'parent' => false,          
	);
    } else {
        $args = array(
		'id'     => 'gencat',    
		'title'  => '<img src="'.get_stylesheet_directory_uri().'/custom-tac/imatges/logo_gene.png">',
		'href' =>'http://www20.gencat.cat/portal/site/ensenyament',
		'parent' => false,
        );
    }
        
    $wp_admin_bar->add_menu( $args );
        
}
?>
