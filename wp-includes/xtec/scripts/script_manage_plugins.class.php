<?php

require_once('agora_script_base.class.php');

class script_manage_plugins extends agora_script_base{

	public $title = 'Manage plugin';
	public $info = "Activa / Desactiva un plugin<br/>
					activationfile: Exemple invite-anyone/invite-anyone.php<br/>
					onoff: on per activar / off per desactivar";


	public function params() {
		$params = array();
		$params['activationfile'] = "";
		$params['onoff'] = "";
		return $params;
	}

	protected function _execute($params = array()) {
		include_once (ABSPATH . 'wp-admin/includes/plugin.php');
		switch ($params['onoff']) {
			case 'on':
				$result = activate_plugin($params['activationfile']);
				break;
			case 'off':
				$result = deactivate_plugins($params['activationfile']);
				break;
			default:
				echo 'onoff només admet valors on o off';
				return false;
		}
		if ( is_wp_error( $result ) ) {
			echo $result->get_error_message();
			return false;
		} else {
			echo 'OK';
		}
		return true;
	}
}