<?php
/*
Plugin Name: AgoraFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: A pluggin to include specific functions which affects only to Àgora-Nodes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/



/**
 * To avoid error uploading files from HTTP pages
 * @param  string $url create docs URL
 * @return string Create docs URL always with HTTPS
 * @author Sara Arjona
 */
function bp_docs_get_create_link_filter($url) {
	return preg_replace('/^http:/i', 'https:', $url);
}
add_filter('bp_docs_get_create_link', 'bp_docs_get_create_link_filter');
