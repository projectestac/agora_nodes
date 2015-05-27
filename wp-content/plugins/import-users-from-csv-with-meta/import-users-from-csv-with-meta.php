<?php
/*
Plugin Name: Import users from CSV with meta
Plugin URI: http://www.codection.com
Description: This plugins allows to import users using CSV files to WP database automatically
Author: codection
Version: 1.2
Author URI: https://codection.com
*/

$url_plugin = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__), "", plugin_basename(__FILE__));
$wp_users_fields = array("user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim", "user_registered");
$wp_min_fields = array("Username", "Password", "Email");

//XTEC ************ AFEGIT - Added language supporting
//2015.03.17 @sarjona
load_plugin_textdomain('import-users-from-csv-with-meta', false, plugin_basename(dirname(__FILE__)). '/languages');
//************ FI

function acui_init(){
	acui_activate();
}

function acui_activate(){
}

function acui_deactivate(){
	delete_option("acui_columns");
}

function acui_menu() {
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.17 @sarjona
add_submenu_page( 'tools.php', 'Insert users massively (CSV)', __('Import users', 'import-users-from-csv-with-meta'), 'manage_options', 'acui', 'acui_options' );
//************ ORIGINAL
/*
	add_submenu_page( 'tools.php', 'Insert users massively (CSV)', 'Import users from CSV', 'manage_options', 'acui', 'acui_options'
*/
//************ FI
}

function acui_detect_delimiter($file){
	$handle = @fopen($file, "r");
	$sumComma = 0;
	$sumSemiColon = 0;
	$sumBar = 0;

    if($handle){
    	while (($data = fgets($handle, 4096)) !== FALSE):
	        $sumComma += substr_count($data, ",");
	    	$sumSemiColon += substr_count($data, ";");
	    	$sumBar += substr_count($data, "|");
	    endwhile;
    }
    fclose($handle);

    if(($sumComma > $sumSemiColon) && ($sumComma > $sumBar))
    	return ",";
    else if(($sumSemiColon > $sumComma) && ($sumSemiColon > $sumBar))
    	return ";";
    else
    	return "|";
}

function acui_string_conversion($string){
	if(!preg_match('%(?:
    [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
    |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
    |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
    |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
    |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
    |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
    |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
    )+%xs', $string)){
		return utf8_encode($string);
    }
	else
		return $string;
}

function acui_import_users($file, $role){?>
	<div class="wrap">
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.30 @nacho
 -->
 <h2><?php _e('Importing users', 'import-users-from-csv-with-meta');?></h2>
 <!--
//************ ORIGINAL
/*
		<h2>Importing users</h2>
*/
*/
//************ FI
-->
		<?php
			set_time_limit(0);
			global $wpdb;
			$headers = array();
			global $wp_users_fields;
			global $wp_min_fields;

//XTEC ************ ELIMINAT - Removed to simplify user experience
//2015.03.18 @sarjona
/*
			echo "<h3>Ready to registers</h3>";
			echo "<p>First row represents the form of sheet</p>";
 */
//************ FI
			$row = 0;

			ini_set('auto_detect_line_endings',TRUE);

			$delimiter = acui_detect_delimiter($file);

			$manager = new SplFileObject($file);
//XTEC ************ AFEGIT - If username exists, do nothing
//2015.03.17 @sarjona
					$errors = '';
//************ FI
			while ( $data = $manager->fgetcsv($delimiter) ):
				if( empty($data[0]) )
					continue;

				if( count($data) == 1 )
					$data = $data[0];

				foreach ($data as $key => $value)   {
					$data[$key] = trim($value);
				}

				for($i = 0; $i < count($data); $i++){
					$data[$i] = acui_string_conversion($data[$i]);
				}

				if($row == 0):
					// check min columns username - password - email
					if(count($data) < 3){
						echo "<div id='message' class='error'>File must contain at least 3 columns: username, password and email</div>";
						break;
					}

					foreach($data as $element)
						$headers[] = $element;

					$columns = count($data);

					$headers_filtered = array_diff($headers, $wp_users_fields);
					$headers_filtered = array_diff($headers_filtered, $wp_min_fields);
					update_option("acui_columns", $headers_filtered);
					?>
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.30 @nacho
 -->
 <h3><?php _e('Inserting and updating data', 'import-users-from-csv-with-meta');?></h3>
 <!--
//************ ORIGINAL
/*
		<h3>Inserting and updating data</h3>
*/
*/
//************ FI
-->
					<table>
					<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.30 @nacho
 -->
					<tr><th><?php _e('Row', 'import-users-from-csv-with-meta');?></th><?php foreach($headers as $element) echo "<th>" . $element . "</th>"; ?></tr>
 <!--
//************ ORIGINAL
/*
		<tr><th>Row</th><?php //foreach($headers as $element) echo "<th>" . $element . "</th>"; ?></tr>
*/
*/
//************ FI
-->
					<?php
					$row++;
				else:
					if(count($data) != $columns): // if number of columns is not the same that columns in header
						echo '<script>alert("Row number: ' . $row . ' has no the same columns than header, we are going to skip");</script>';
						continue;
					endif;

					$username = $data[0];
					$password = $data[1];
					$email = $data[2];
					$user_id = 0;

					if(username_exists($username)){
//XTEC ************ AFEGIT - If username exists, do nothing
//2015.03.17 @sarjona
						$errors .=  sprintf(__('User %s already exists', 'import-users-from-csv-with-meta').'<br/>', '\''.$username.'\'');
						continue;
//************ FI
						$user_object = get_user_by( "login", $username );
						$user_id = $user_object->ID;
					}
					else{
						$user_id = wp_create_user($username, $password, $email);
					}

					if(is_wp_error($user_id)){
//XTEC ************ MODIFICAT - Added language supporting and changed to $errors to print them at the end
//2015.03.17 @sarjona
						$errors .=  sprintf(__('Problems with user %s. Skiping importation', 'import-users-from-csv-with-meta').'<br/>', '\''.$username.'\'');
//************ ORIGINAL
/*
						echo '<script>alert("Problems with user: ' . $username . ', we are going to skip");</script>';
*/
//************ FI
						continue;
					}

					if(!( in_array("administrator", acui_get_roles($user_id), FALSE) || is_multisite() && is_super_admin( $user_id ) ))
						wp_update_user(array ('ID' => $user_id, 'role' => $role)) ;

					if($columns > 3){
						for($i=3; $i<$columns; $i++):
							if( !empty($data) ){
								if(in_array($headers[$i], $wp_users_fields))
									wp_update_user( array( 'ID' => $user_id, $headers[$i] => $data[$i] ) );
								else
									update_user_meta($user_id, $headers[$i], $data[$i]);
							}
						endfor;
					}

					echo "<tr><td>" . ($row - 1) . "</td>";
					foreach ($data as $element)
						echo "<td>$element</td>";

					echo "</tr>\n";
					flush();
				endif;

				$row++;
			endwhile;
			?>
			</table>
<!--//XTEC ************ AFEGIT - If username exists, do nothing
//2015.03.17 @sarjona -->
			<?php
				// If there is no imported user, show message
				if ($row == 2){
					echo '<br/>'.__('No user has been imported correctly', 'import-users-from-csv-with-meta');
				}
				// Show errors
				if ( ! empty($errors)) {
			?>
					<h3><?php _e('Not imported users', 'import-users-from-csv-with-meta');?></h3>
			<?php
					echo $errors;
				}
			?>
<!--//************ FI -->
			<br/>
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.30 @nacho
 -->
 <p><?php _e('Process finished you can go', 'import-users-from-csv-with-meta');?> <a href="<?php echo get_admin_url() . '/users.php'; ?>"><?php _e('here to see results', 'import-users-from-csv-with-meta');?> </a></p>
 <!--
//************ ORIGINAL
/*
		<p>Process finished you can go <a href="<?php //echo get_admin_url() . '/users.php'; ?>">here to see results </a></p>
*/
*/
//************ FI
-->
			<?php
			//fclose($manager);
			ini_set('auto_detect_line_endings',FALSE);
		?>
	</div>
<?php
}

function acui_get_roles($user_id){
	$roles = array();
	$user = new WP_User( $user_id );

	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			$roles[] = $role;
	}

	return $roles;
}

function acui_get_editable_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);
    $list_editable_roles = array();

    foreach ($editable_roles as $key => $editable_role)
		$list_editable_roles[$key] = $editable_role["name"];

    return $list_editable_roles;
}

function acui_options()
{
	if (!current_user_can('edit_users'))
	{
		wp_die(__('You are not allowed to see this content.'));
		$acui_action_url = admin_url('options-general.php?page=' . plugin_basename(__FILE__));
	}
	else if(isset($_POST['uploadfile']))
		acui_fileupload_process($_POST['role']);
	else
	{
?>
	<div class="wrap">
<!--
//XTEC ************ ELIMINAT - Removed to simplify user experience
//2015.03.17 @sarjona
/*
		<div id='message' class='updated'>File must contain at least <strong>3 columns: username, password and email</strong>. These should be the first three columns and it should be placed <strong>in this order: username, password and email</strong>. If there are more columns, this plugin will manage it automatically.</div>
*/
//************ FI
-->
		<div style="clear:both; width:100%;">
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.17 @sarjona
-->
			<h2><?php _e('Import users from CSV', 'import-users-from-csv-with-meta');?></h2>
<!--
//************ ORIGINAL
/*
			<h2>Import users from CSV</h2>
*/
//************ FI
-->
		</div>

<!--
//XTEC ************ ELIMINAT - Removed to simplify user experience
//2015.03.17 @sarjona
/*
		<div style="float:right; width:20%;">
			<p><em>If you like this plugin, you can support it.</em></p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="T5J5F6XZTSYH2">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
*/
//************ FI
-->

		<div style="float:left; width:80%;">
			<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" onsubmit="return check();">
<!--
//XTEC ************ MODIFICAT - Alter table
//2015.03.20 @nacho
-->
				<table class="form-table">
<!-- ************ ORIGINAL
/*
				<table class="form-table" style="width:50%" border="1">
*/
//************ FI
-->
				<tbody>
				<tr class="form-field">
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.17 @sarjona
-->
					<th scope="row"><label for="role"><?php _e('Role', 'import-users-from-csv-with-meta'); ?></label></th>
<!--
//************ ORIGINAL
/*
					<th scope="row"><label for="role">Role</label></th>
*/
//************ FI
-->
					<td>
					<select name="role" id="role">
						<?php
							$list_roles = acui_get_editable_roles();
							foreach ($list_roles as $key => $value) {
								//XTEC ************ MODIFICAT - Added language supporting
								//2015.05.27 @nacho
								if($key == "subscriber")
									echo "<option selected='selected' value='$key'>".translate_user_role($value)."</option>";
								else
									echo "<option value='$key'>".translate_user_role($value)."</option>";

								//************ ORIGINAL
								/*if($key == "subscriber")
									echo "<option selected='selected' value='$key'>$value</option>";
								else
									echo "<option value='$key'>$value</option>";
								*/
								//************ FI
							}
						?>
					</select>
					</td>
<!--
//XTEC ************ AFEGIT - Added function for hidden or display help
//2015.03.20 @nacho
-->
					<td>
					<a href="javascript:void(0)" onClick="toggleproviderhelp()"><?php _e("Where do I get this info?", 'import-users-from-csv-with-meta') ?></a>
					</td>
<!--
//************ FI
-->
				</tr>

				<tr class="form-field form-required">
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.17 @sarjona
-->
					<th scope="row"><label for="user_login"><?php _e('CSV file', 'import-users-from-csv-with-meta'); ?> <span class="description">(<?php _e('required', 'import-users-from-csv-with-meta'); ?>)</span></label></th>
<!--
//************ ORIGINAL
/*
					<th scope="row"><label for="user_login">CSV file <span class="description">(required)</span></label></th>
*/
//************ FI
-->
					<td><input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" /></td>
				</tr>
				</tbody>
			</table>
<!--
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.17 @sarjona
-->
			<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php _e('Start importing', 'import-users-from-csv-with-meta'); ?>"/>
<!--
//************ ORIGINAL
/*
			<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Start importing"/>
*/
//************ FI
-->
			</form>
		</div>

		<div style="clear:both; width:100%;"></div>

<!--
//XTEC ************ AFEGIT - Added block for show help
//2015.03.20 @nacho
-->
		<div
			class="iu_div_settings_help_importUsers"
			style="<?php if( isset( $_REQUEST["enable"] )  && $_REQUEST["enable"] == $provider_id ) echo "-"; // <= lolz ?>display:none;">
			<table class="form-table editcomment">
				<tbody>
					<tr valign="top">
						<td>
							<div id="post-body-content">
								<div id="namediv" class="stuffbox">
									<h4 style="padding: 8px 12px; margin: 0.33em 0;">
										<label>
										<?php _e("Help", "import-users-from-csv-with-meta");?>
										</label>
							        </h4>
							        <div class="inside">
								        <hr class="wsl">
									        <strong><?php _e("You should fill the first three rows with the next values", "import-users-from-csv-with-meta");?></strong><br/>
									        <ul><ol>
									        	<li>
													<strong>
													<?php _e("Username", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("Sets the username.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("Password", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("Sets user password.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("Email", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("Sets user email.", "import-users-from-csv-with-meta");?>
												</li>
									        </ul></ol>

									        <strong><?php _e("The next columns are totally customizable and you can use whatever you want. All rows must contains same columns", "import-users-from-csv-with-meta");?></strong><br/>

									        <ol>
												<li>
													<strong>
													<?php _e("user_nicename", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("A string that contains a URL-friendly name for the user. The default is the user's username.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("user_url", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("A string containing the user's URL for the user's web site.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("display_name", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through obscurity (that is if you dont use and delete the default admin user).", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("nickname", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("The user's nickname, defaults to the user's username.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("first_name", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("The user's first name.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("last_name", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("The user's last name.", "import-users-from-csv-with-meta");?>
												</li>
												<li>
													<strong>
													<?php _e("description", "import-users-from-csv-with-meta");?>
													</strong>
													<?php _e("A string containing content about the user.", "import-users-from-csv-with-meta");?>
												</li>
											</ol>
								        </hr>
							        </div>
							    </div>
							</div>
						</td>
						<td width="10"></td>
						<td width="400"> </td>
					</tr>
				</tbody>
			</table>
	    </div>
<!--
*/
//************ FI
-->

<!--
//XTEC ************ ELIMINAT - Removed to simplify user experience
//2015.03.17 @sarjona
/*
		<div style="float:right; width:20%;">
			<p><em>If you like this plugin, you can support it.</em></p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="T5J5F6XZTSYH2">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
		<?php
		$headers = get_option("acui_columns");

		if(is_array($headers) && !empty($headers)):
		?>

		<h3>Custom columns loaded</h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Columns loaded in previous files</th>
				<td><small><em>(if you load another CSV with different columns, the new ones will replace this list)</em></small>
					<ol>
						<?php foreach ($headers as $column): ?>
							<li><?php echo $column; ?></li>
						<?php endforeach; ?>
					</ol>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">WordPress default profile data</th>
				<td>You can use those labels if you want to set data adapted to the WordPress default user columns (the ones who use the function <a href="http://codex.wordpress.org/Function_Reference/wp_update_user">wp_update_user</a>)
					<ol>
						<li><strong>user_nicename</strong>: A string that contains a URL-friendly name for the user. The default is the user's username.</li>
						<li><strong>user_url</strong>: A string containing the user's URL for the user's web site.	</li>
						<li><strong>display_name</strong>: A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through obscurity (that is if you dont use and delete the default admin user).	</li>
						<li><strong>nickname</strong>: The user's nickname, defaults to the user's username.	</li>
						<li><strong>first_name</strong>: The user's first name.</li>
						<li><strong>last_name</strong>: The user's last name.</li>
						<li><strong>description</strong>: A string containing content about the user.</li>
						<li><strong>jabber</strong>: User's Jabber account.</li>
						<li><strong>aim</strong>: User's AOL IM account.</li>
						<li><strong>yim</strong>: User's Yahoo IM account.</li>
					</ol>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Important notices</th>
				<td>1) You can upload as many files as you want, but all must have the same columns. If you upload another file, the columns will change to the form of last file uploaded.</td>
				<td>2) If you are updating data, leave empty any field to leave it without update. If you want to update it leaving it blank, you can always insert a blank space in this field.</td>
			</tr>
			<tr valign="top">
				<th scope="row">Any question about it</th>
			<td>Please contact: <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</td>
			</tr>
			<tr valign="top">
				<th scope="row">Example</th>
			<td>Download this <a href="<?php echo plugins_url() . "/import-users-from-csv-with-meta/test.csv"; ?>">.csv file</a> to test</td>
			</tr>
		</tbody></table>

		<?php endif; ?>

		<h3>Doc</h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Columns position</th>
				<td><small><em>(Documents should look like the one presented into screenshot. Remember you should fill the first three rows with the next values)</em></small>
					<ol>
						<li>Username</li>
						<li>Password</li>
						<li>Email</li>
					</ol>
					<small><em>(The next columns are totally customizable and you can use whatever you want. All rows must contains same columns)</em></small>
					<small><em>(User profile will be adapted to the kind of data you have selected)</em></small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">WordPress default profile data</th>
				<td>You can use those labels if you want to set data adapted to the WordPress default user columns (the ones who use the function <a href="http://codex.wordpress.org/Function_Reference/wp_update_user">wp_update_user</a>)
					<ol>
						<li><strong>user_nicename</strong>: A string that contains a URL-friendly name for the user. The default is the user's username.</li>
						<li><strong>user_url</strong>: A string containing the user's URL for the user's web site.	</li>
						<li><strong>display_name</strong>: A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through obscurity (that is if you dont use and delete the default admin user).	</li>
						<li><strong>nickname</strong>: The user's nickname, defaults to the user's username.	</li>
						<li><strong>first_name</strong>: The user's first name.</li>
						<li><strong>last_name</strong>: The user's last name.</li>
						<li><strong>description</strong>: A string containing content about the user.</li>
						<li><strong>jabber</strong>: User's Jabber account.</li>
						<li><strong>aim</strong>: User's AOL IM account.</li>
						<li><strong>yim</strong>: User's Yahoo IM account.</li>
						<li><strong>user_registered</strong>: Using the WordPress format for this kind of data Y-m-d H:i:s.</li>
					</ol>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Important notice</th>
				<td>You can upload as many files as you want, but all must have the same columns. If you upload another file, the columns will change to the form of last file uploaded.</td>
			</tr>
			<tr valign="top">
				<th scope="row">Any question about it</th>
			<td>Please contact: <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</td>
			</tr>
			<tr valign="top">
				<th scope="row">Example</th>
			<td>Download this <a href="<?php echo plugins_url() . "/import-users-from-csv-with-meta/test.csv"; ?>">.csv file</a> to test</td>
			</tr>
		</tbody></table>
		<br/>
		<div style="width:775px;margin:0 auto"><img src="<?php echo plugins_url() . "/import-users-from-csv-with-meta/csv_example.png"; ?>"/></div>
*/
//************ FI
-->
	</div>
	<script type="text/javascript">
	function check(){
		if(document.getElementById("uploadfiles").value == "") {
//XTEC ************ MODIFICAT - Added language supporting
//2015.03.20 @nacho
			alert ("<?php $msg = _e("Please choose a file", "import-users-from-csv-with-meta");; echo $msg;?>");
//************ ORIGINAL
			//alert("Please choose a file");
//************ FI
			return false;
		}
	}
	</script>
<?php
	}
}

/**
 * Handle file uploads
 *
 * @todo check nonces
 * @todo check file size
 *
 * @return none
 */
function acui_fileupload_process($role) {
  $uploadfiles = $_FILES['uploadfiles'];

  if (is_array($uploadfiles)) {

	foreach ($uploadfiles['name'] as $key => $value) {

	  // look only for uploded files
	  if ($uploadfiles['error'][$key] == 0) {
		$filetmp = $uploadfiles['tmp_name'][$key];

		//clean filename and extract extension
		$filename = $uploadfiles['name'][$key];

		// get file info
		// @fixme: wp checks the file extension....
		$filetype = wp_check_filetype( basename( $filename ), array('csv' => 'text/csv') );
		$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
		$filename = $filetitle . '.' . $filetype['ext'];
		$upload_dir = wp_upload_dir();

		if ($filetype['ext'] != "csv") {
		  wp_die('File must be a CSV');
		  return;
		}

		/**
		 * Check if the filename already exist in the directory and rename the
		 * file if necessary
		 */
		$i = 0;
		while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
		  $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
		  $i++;
		}
		$filedest = $upload_dir['path'] . '/' . $filename;

		/**
		 * Check write permissions
		 */
		if ( !is_writeable( $upload_dir['path'] ) ) {
		  wp_die('Unable to write to directory. Is this directory writable by the server?');
		  return;
		}

		/**
		 * Save temporary file to uploads dir
		 */
		if ( !@move_uploaded_file($filetmp, $filedest) ){
		  wp_die("Error, the file $filetmp could not moved to : $filedest ");
		  continue;
		}

		$attachment = array(
		  'post_mime_type' => $filetype['type'],
		  'post_title' => $filetitle,
		  'post_content' => '',
		  'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $filedest );
		require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
		wp_update_attachment_metadata( $attach_id,  $attach_data );

		acui_import_users($filedest, $role);
	  }
	}
  }
}

function acui_extra_user_profile_fields( $user ) {
	global $wp_users_fields;
	global $wp_min_fields;

	$headers = get_option("acui_columns");
	if(count($headers) > 0):
?>
	<h3><?php _e("Extra profile information", "blank"); ?></h3>

	<table class="form-table"><?php

	foreach ($headers as $column):
		if(in_array($column, $wp_min_fields) || in_array($column, $wp_users_fields))
			continue;
	?>
		<tr>
			<th><label for="<?php echo $column; ?>"><?php echo $column; ?></label></th>
			<td><input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" value="<?php echo esc_attr(get_the_author_meta($column, $user->ID )); ?>" class="regular-text" /></td>
		</tr>
		<?php
	endforeach;
	?>
	</table><?php
	endif;
}

function acui_save_extra_user_profile_fields( $user_id ){
	if (!current_user_can('edit_user', $user_id)) { return false; }

	global $wp_users_fields;
	global $wp_min_fields;
	$headers = get_option("acui_columns");

	if(count($headers) > 0):
		foreach ($headers as $column){
			if(in_array($column, $wp_min_fields) || in_array($column, $wp_users_fields))
				continue;

			update_user_meta( $user_id, $column, $_POST[$column] );
		}
	endif;
}

register_activation_hook(__FILE__,'acui_init');
register_deactivation_hook( __FILE__, 'acui_deactivate' );
add_action("plugins_loaded", "acui_init");
add_action("admin_menu", "acui_menu");
add_action("show_user_profile", "acui_extra_user_profile_fields");
add_action("edit_user_profile", "acui_extra_user_profile_fields");
add_action("personal_options_update", "acui_save_extra_user_profile_fields");
add_action("edit_user_profile_update", "acui_save_extra_user_profile_fields");

// misc
if (!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') {
        if (is_string($input) && !empty($input)) {
            $output = array();
            $tmp    = preg_split("/".$eol."/",$input);
            if (is_array($tmp) && !empty($tmp)) {
                while (list($line_num, $line) = each($tmp)) {
                    if (preg_match("/".$escape.$enclosure."/",$line)) {
                        while ($strlen = strlen($line)) {
                            $pos_delimiter       = strpos($line,$delimiter);
                            $pos_enclosure_start = strpos($line,$enclosure);
                            if (
                                is_int($pos_delimiter) && is_int($pos_enclosure_start)
                                && ($pos_enclosure_start < $pos_delimiter)
                                ) {
                                $enclosed_str = substr($line,1);
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure);
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end);
                                $output[$line_num][] = $enclosed_str;
                                $offset = $pos_enclosure_end+3;
                            } else {
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) {
                                    $output[$line_num][] = substr($line,0);
                                    $offset = strlen($line);
                                } else {
                                    $output[$line_num][] = substr($line,0,$pos_delimiter);
                                    $offset = (
                                                !empty($pos_enclosure_start)
                                                && ($pos_enclosure_start < $pos_delimiter)
                                                )
                                                ?$pos_enclosure_start
                                                :$pos_delimiter+1;
                                }
                            }
                            $line = substr($line,$offset);
                        }
                    } else {
                        $line = preg_split("/".$delimiter."/",$line);

                        /*
                         * Validating against pesky extra line breaks creating false rows.
                         */
                        if (is_array($line) && !empty($line[0])) {
                            $output[$line_num] = $line;
                        }
                    }
                }
                return $output;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
?>

<!--
//XTEC ************ AFEGIT - Added Provided Help
//2015.03.20 @nacho
-->
<script>
function toggleproviderhelp() {
	if(typeof jQuery=="undefined") {
		alert ("<?php $msg = _e("Import Users module require jQuery to be installed on your wordpress in order to work!", "import-users-from-csv-with-meta"); echo $msg;?>");
		return false;
	}

	idp = 'importUsers';
	jQuery('.iu_div_settings_help_' + idp).toggle();

	return false;
}
</script>
<!-- ************ FI -->