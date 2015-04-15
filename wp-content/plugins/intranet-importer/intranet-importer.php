<?php
/*
Plugin Name: Intraweb Importer
Plugin URI: https://github.com/projectestac/agora_nodes
Description: Import Intraweb info to Nodes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

// Wordpress Classes needed by importer
require_once ABSPATH . 'wp-admin/includes/import.php';

if (!class_exists('WP_Importer')) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if (file_exists($class_wp_importer))
		require_once $class_wp_importer;
}

if (!class_exists('WP_Error')) {
	$class_wp_error = ABSPATH . 'wp-includes/class-wp-error.php';
	if (file_exists($class_wp_error))
		require_once $class_wp_error;
}

require_once dirname( __FILE__ ) . '/lib/import.php';

// Added css style
wp_enqueue_style( 'intranet-css', plugin_dir_url( __FILE__ ) . 'css/intranet.css', array( 'editor-buttons' ), '4.0' );

// Added language support
load_plugin_textdomain('intranet-importer', false, basename(dirname(__FILE__)) . '/language');

if (class_exists('WP_Importer')) {

	class Intranet_Import extends WP_Importer {

		function Intranet_Import() {
		}

		// Called from intranet_importer_init
		static function register_importer() {
			if (!defined('WP_LOAD_IMPORTERS'))
				return;

			$intranet_import = new Intranet_Import();
			register_importer('intranet', 'Intraweb', __('Import Intranets to Wordpress', 'intranet-importer'), array($intranet_import,'start'));
		}

		function start() {
			$users = $this->check_tables('users');

			if (!$users) {
				$title = __('Intranet Import', 'intranet-importer');
				$body = __("There is no content to be imported", 'intranet-importer');
				echo "<div class='wrap'> <h2>$title</h2> <p>$body</p></div>";
			}else {
				$news = $this->check_tables('news');
				$pages = $this->check_tables('pages');
				$messages = $this->check_tables('message');
				$specialPages = $this->check_tables('content_page');
				$documents = $this->check_tables('IWdocmanager');
				$users = $this->check_tables('users');

				if (isset($_POST['start'])) {
					$this->start_import();
				}
				else {
					wp_enqueue_script( 'Intranet_Import', plugins_url('/js/importer.js', __file__) , array(), '1.0.0', true );

					$title  = __('Intranet Import', 'intranet-importer');
					$body   = __('This tool allows to import Intranet content to Wordpress', 'intranet-importer');
					$submit = __('Start Import', 'intranet-importer');

					echo "<form method='post' action='?import=intranet&amp;start=true'>";
					echo "<div class='wrap'> <h2>$title</h2> <p>$body</p> </div>";
					echo "<p>".__("Indicate the content you want to import:", 'intranet-importer'). "</p>";

					if ($users[0]->Total > 0) {
						echo "<input type='checkbox' id='users' name='selections[]' value='users'> ".__('Users', 'intranet-importer')."<br>";
					} else{
						echo "<input type='checkbox' id='users' name='selections[]' value='users' disabled> ".__('Users. (No users to import)', 'intranet-importer')."<br>";
					}
					if ($news[0]->Total > 0) {
						echo "<input type='checkbox' id='news' name='selections[]' value='news'> ".__('News', 'intranet-importer')."<br>";
					} else {
						echo "<input type='checkbox' id='news' name='selections[]' value='news' disabled> ".__('News. (No news to import)', 'intranet-importer')."<br>";
					}
					if ($pages[0]->Total > 0) {
						echo "<input type='checkbox' id='pages' name='selections[]' value='pages'> ".__('Simple Pages', 'intranet-importer')."<br>";
					} else {
						echo "<input type='checkbox' id='pages' name='selections[]' value='pages' disabled> ".__('Simple Pages. (No Simple Pages to import)', 'intranet-importer')."<br>";
					}
					if ($messages[0]->Total > 0) {
						echo "<input type='checkbox' id='messages' name='selections[]' value='messages'> ".__('Admin Messages / Notice', 'intranet-importer')."<br>";
					} else {
						echo "<input type='checkbox' id='messages' name='selections[]' value='messages' disabled> ".__('Admin Messages / Notice. (No Admin Messages / Notice to import)', 'intranet-importer')."<br>";
					}
					if ($specialPages[0]->Total > 0) {
						echo "<input type='checkbox' id='specialPages' name='selections[]' value='specialPages'> ".__('Advanced Pages / Content', 'intranet-importer')."<br>";
					} else {
						echo "<input type='checkbox' id='specialPages' name='selections[]' value='specialPages' disabled> ".__('Advanced Pages / Content. (No advanced Pages / Content to import)', 'intranet-importer')."<br>";
					}
					if ($documents[0]->Total > 0) {
						echo "<input type='checkbox' id='documents' name='selections[]' value='documents'> ".__('Documents', 'intranet-importer')."<br>";
					} else {
						echo "<input type='checkbox' id='documents' name='selections[]' value='documents' disabled> ".__('Documents. (No documents to import)', 'intranet-importer')."<br>";
					}

					echo '<br/>';
                    
					echo '<div>';
					echo __('Select the privacity:', 'intranet-importer')."<br/>";
					echo '<select id="privacity" name="privacity">
							<option value=\'\'>--</option>
							<option value=\'publish\'>' . __('Publish', 'intranet-importer') . '</option>
							<option value=\'private\'>' . __('Private', 'intranet-importer') . '</option>
						 </select>';
					echo '</div>';
                    
					echo '<div class="info-text">';
					echo __('Info text', 'intranet-importer')."<br/><br/>";
					echo '</div>';

					$msg_select_option = __('You must select at least one option', 'intranet-importer');
					$msg_select_privacity = __('You must select the privacity', 'intranet-importer');

					printf("<p class=\"submit\" style=\"text-align:left;\"><input type=\"submit\" class=\"button-primary\" value=\"%s\" name=\"start\" onclick=\"return checkImportOptions('%s', '%s'); return false;\"/></p></form>", $submit , addslashes($msg_select_option), addslashes($msg_select_privacity));
				}
			}
		}

		function check_tables($table) {
			global $wpdb;

			$query = "SELECT count(*) AS Total FROM $table";
			$result = $wpdb->get_results($query);
			return $result;
		}

		function start_import(){
			set_time_limit(0);

			$title   = __('Intranet Import', 'intranet-importer');
			$submit  = __('Return', 'intranet-importer');
			$summary = '';

			echo '<div class="wrap"><h2>' . $title . '</h2></div>';
			echo '<div class="wrap"><br/>';
			echo __('Start Process', 'intranet-importer');
			echo '</div>';

			$privacity = $_POST['privacity'];

			$selections = array();
			if(!empty($_POST['selections'])) {
				foreach($_POST['selections'] as $check) {
					array_push($selections, $check);
				}
			}

			$activeNews 		 = in_array("news", $selections) ? "news" : false;
			$activePages 		 = in_array("pages", $selections) ? "pages" : false;
			$activeMessages 	 = in_array("messages", $selections) ? "messages" : false;
			$activeUsers 		 = in_array("users", $selections) ? "users" : false;
			$activeSpecialPages  = in_array("specialPages", $selections) ? "specialPages" : false;
			$activeDocuments  	 = in_array("documents", $selections) ? "documents" : false;

			intranetImportControlTable();

			$summary = '<div class="intranet">';
            $summary .= '<ul>';

			if ($activeUsers) {
				$totalUsers = importUsers();
				$summary .= "<li>".__('Total Imported Users: ', 'intranet-importer') . $totalUsers."</li>";
			}
			if ($activeNews) {
				$report = importData('news', $privacity);
				$summary .= "<li>".__('Report Imported News: ', 'intranet-importer') ."<br/>";
				$summary .= __('Inserted: ', 'intranet-importer') ."&nbsp;". $report['insert']."<br/>";
				$summary .= __('Updated: ', 'intranet-importer') ."&nbsp;". $report['update']."<br/>";
				if (!empty($report['error'])) {
					foreach ($report['error'] as $key => $value) {
						$summary .= $value."<br/>";
					}
				}
				$summary .= "</li>";
			}
			if ($activePages) {
				$report = importData('pages', $privacity);
				$summary .= "<li>".__('Report Imported Pages: ', 'intranet-importer') ."<br/>";
				$summary .= __('Inserted: ', 'intranet-importer') ."&nbsp;". $report['insert']."<br/>";
				$summary .= __('Updated: ', 'intranet-importer') ."&nbsp;". $report['update']."<br/>";
				if (!empty($report['error'])) {
					foreach ($report['error'] as $key => $value) {
						$summary .= $value."<br/>";
					}
				}
				$summary .= "</li>";
			}
			if ($activeMessages) {
				$report = importData('message', $privacity);
				$summary .= "<li>".__('Report Imported Messages: ', 'intranet-importer') ."<br/>";
				$summary .= __('Inserted: ', 'intranet-importer') ."&nbsp;". $report['insert']."<br/>";
				$summary .= __('Updated: ', 'intranet-importer') ."&nbsp;". $report['update']."<br/>";
				if (!empty($report['error'])) {
					foreach ($report['error'] as $key => $value) {
						$summary .= $value."<br/>";
					}
				}
				$summary .= "</li>";
			}
			if ($activeDocuments) {
				$report = importData('IWdocmanager', $privacity);
				$summary .= "<li>".__('Report Imported Documents: ', 'intranet-importer') ."<br/>";
				$summary .= __('Inserted: ', 'intranet-importer') ."&nbsp;". $report['insert']."<br/>";
				$summary .= __('Updated: ', 'intranet-importer') ."&nbsp;". $report['update']."<br/>";
				if (!empty($report['error'])) {
					foreach ($report['error'] as $key => $value) {
						$summary .= $value."<br/>";
					}
				}
				$summary .= "</li>";
			}
			if ($activeSpecialPages) {
				$report = importSpecialPages($privacity);
				$summary .= "<li>".__('Report Imported Advanced Pages ', 'intranet-importer') ."<br/>";
				$summary .= __('Inserted: ', 'intranet-importer') ."&nbsp;". $report['insert']."<br/>";
				$summary .= __('Updated: ', 'intranet-importer') ."&nbsp;". $report['update']."<br/>";
				if (!empty($report['error'])) {
					foreach ($report['error'] as $key => $value) {
						$summary .= $value."<br/>";
					}
				}
				$summary .= "</li>";
			}

			$summary .= '</ul>';
			$summary .= '</div>';

			$body .= "<p>".__('Process finished', 'intranet-importer')."</p>";
			$body .= $summary;

			echo '<div class="wrap">' . $body . '</div>';
			echo '<form method="post" action="admin.php?import=intranet">';
			printf("<p class='submit' style='text-align:left;'><input type='submit' class='button-primary' value='%s' name='return' /></p></form>",$submit);
		}
	}
}

add_action('admin_init', array('Intranet_Import','register_importer'));