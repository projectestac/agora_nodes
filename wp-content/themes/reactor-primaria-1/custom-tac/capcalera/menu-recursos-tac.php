<?php
function add_recursos( $wp_admin_bar ) {

	$args = array(
		'id'     => 'recursosXTEC',     
		'title'  => '<img src='.get_bloginfo('template_directory').'-primaria-1/custom-tac/imatges/logo_xtec.png',
		'title'  => '<img src=http://ies-sabadell.cat/nodes/wp-content/uploads/2014/03/xtec4.png>',
		//'href'=>'http://www.xtec.cat/', 
		'parent' => false,          
		//'meta'=>array('class' =>'ab-top-secondary ab-top-menu')
	);

	$wp_admin_bar->add_node( $args );
	
	$args = array(
		'id'     => 'xtec',     
		'title'  => '<img src=http://educacio.gencat.cat/documents/img/meva_xtec.jpg>&nbsp;Xtec',
		'href'=>'http://www.xtec.cat/', 
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'edu365',     
		'href' => 'http://www.edu365.cat/',
		'title'  => '<img src=http://educacio.gencat.cat/documents/img/edu365.jpg>&nbsp;Edu365', 
		'parent' => 'recursosXTEC',         
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'edu3',     
		'href'=> 'http://www.edu3.cat/',
		'title'  => '<img src=http://educacio.gencat.cat/documents/img/edu3.jpg>&nbsp;Edu3', 
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'alexandria',     
		'title'  => 'Alexandria',
		'href' => 'http://alexandria.xtec.cat/', 
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'xarxadocent',     
		'title'  => 'Xarxa Docent', 
		'href'=>'http://educat.xtec.cat/',
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'arc',     
		'title'  => 'ARC', 
		'href' => 'http://apliense.xtec.cat/arc/',
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'merli',     
		'title'  => 'Merlí', 
		'href'=>'http://aplitic.xtec.cat/merli/',
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'jclic',     
		'title'  => 'jClic', 
		'href'	=> 'http://clic.xtec.cat/ca/index.htm',
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'linkat',     
		'title'  => 'Linkat', 
		'href' => 'http://linkat.xtec.cat/portal/index.php',
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'     => 'odissea',     
		'title'  => 'Odissea', 
		'href' => 'http://odissea.xtec.cat/',
		'parent' => 'recursosXTEC',          
	);

	$wp_admin_bar->add_node( $args );


}
?>
