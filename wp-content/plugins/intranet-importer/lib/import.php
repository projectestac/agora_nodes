<?php

/**
 * Creates an intermediate control table for manage import data
 */
function intranetImportControlTable() {
	global $wpdb;

	$db_table_name = $wpdb->prefix . 'import_intranet';

	if($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
		$sql = "CREATE TABLE " . $db_table_name . " (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`id_intraweb` int(11),
					`id_wp_post` int(11) NOT NULL,
					`type` varchar(255),
					PRIMARY KEY id (id)
				)ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

/**
 * Retrieves records from Intranets tables
 */
function importData($table, $privacity) {
	global $wpdb;

	$query = "SELECT * FROM $table";
	$result = $wpdb->get_results($query);

	if (!empty($wpdb->last_error)) {
		echo __('DataBase error: ', 'intranet-importer').'&nbsp;'.$wpdb->last_error;
		die();
	}

	$report = array(
		'insert' 	=> 0,
		'update' 	=> 0,
		'error'		=> array(),
	);
	if ($result) {
		foreach ($result as $new_data) {
			$report = addToWordPress($table, $new_data, $report, $privacity);
			echo '.';
		}
	}

	return $report;
}

/**
 * Check errors in queries
 */
function manageDataBaseErrors($wpdb){
	if (!empty($wpdb->last_error)) {
		echo __('DataBase transaction error: ', 'intranet-importer').$wpdb->last_error;
		die();
	}
}

/**
 * Processes the data from Intranet table to import
 * If the data has already been imported, the content is updated
 */
function addToWordPress($table, $new_data, $report, $privacity) {
	global $wpdb;

	createMetaTags();

	$db_table_name = $wpdb->prefix . 'import_intranet';
	$post = array();
    
    $ids_intraweb = array (
        'news' => $new_data->sid,
        'pages' => $new_data->pageid,
        'message' => $new_data->mid,
        'IWdocmanager' => $new_data->documentId
    );

    // If imported content is deleted by WordPress admin / editor, import it again and delete record
    $id = $ids_intraweb[$table];
    $query = "SELECT * FROM $db_table_name WHERE id_intraweb = '$id' AND type = '$table' ";
    $result = $wpdb->get_row($query);

    $check_record = $wpdb->get_results('SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE ID = ' . $result->id_wp_post);
    if (count($check_record) == 0) {
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'import_intranet WHERE id_wp_post = %d', $result->id_wp_post));
        $result = false;
    }

    manageDataBaseErrors($wpdb);

	if ($table === 'news') {

		$userId = getUserId($new_data->cr_uid);

		$post['post_content'] = parseSingleContent($new_data->hometext) . parseSingleContent($new_data->bodytext);
		$post['post_name'] 	  = $new_data->urltitle;
		$post['post_title']   = $new_data->title;
		$post['post_status']  = $privacity;
		$post['post_type'] 	  = 'post';
		$post['post_author']  = $userId;
		$post['post_date']    = $new_data->ffrom;

        if ($result) {
			$post['ID'] = $result->id_wp_post;
			$update = wp_update_post($post);

			if ($update === false) {
				$report['error'][] = __('Error Updating Post content: ', 'intranet-importer').'&nbsp;'.$new_data->urltitle;
			} else {
				$report['update']++;
			}
		} else {
			$post_id = wp_insert_post($post);

			if (!$post_id) {
				$report['error'][] = __('Error inserting Post content: ', 'intranet-importer').'&nbsp;'.$new_data->urltitle;
			}else {
				wp_set_post_terms($post_id, 'intraweb, noticies');
				insertDataOnIntranetImport($db_table_name, $new_data->sid, $post_id, $table);
				$report['insert']++;
			}
		}
		return $report;

	} elseif ($table === 'pages') {
		$page_root_id = rootIntranetPage();
		$userId = getUserId($new_data->cr_uid);

		$post['post_content'] = parseSingleContent($new_data->content) . ' <br/> ' . parseSingleContent($new_data->metakeywords);
		$post['post_name']    = $new_data->title;
		$post['post_title']   = $new_data->title;
		$post['post_status']  = $privacity;
		$post['post_type'] 	  = 'page';
		$post['post_author']  = $userId;
		$post['post_parent']  = $page_root_id;
		$post['post_date']    = $new_data->cr_date;

		if ($result) {
			$post['ID'] = $result->id_wp_post;
			$update = wp_update_post($post);

			if ($update === false) {
				$report['error'][] = __('Error Updating Post content: ', 'intranet-importer').'&nbsp;'.$new_data->title;
			}else {
				setTemplate($post['ID']);
				$report['update']++;
			}
		} else {
			$post_id = wp_insert_post($post);

			if (!$post_id) {
				$report['error'][] = __('Error inserting Post content: ', 'intranet-importer').'&nbsp;'.$new_data->title;
			}else {
				setTemplate($post_id);
				insertDataOnIntranetImport($db_table_name, $new_data->pageid, $post_id, $table);
				$report['insert']++;
			}
		}
		return $report;

	} elseif ($table === 'message') {
		$post['post_content'] = parseSingleContent($new_data->content);
		$post['post_name']    = $new_data->title;
		$post['post_title']   = $new_data->title;
		$post['post_status']  = $privacity;
		$post['post_type'] 	  = 'post';
		$post['post_author']  = 1;
        $post['post_date']    = date('Y-m-d H:i:s', $new_data->date);

		if ($result) {
			$post['ID'] = $result->id_wp_post;
			$update = wp_update_post($post);
			if ($update === false) {
				$report['error'][] = __('Error Updating Post content: ', 'intranet-importer').'&nbsp;'.$new_data->title;
			}else {
				setTemplate($post['ID']);
				$report['update']++;
			}
		} else {
			$post_id = wp_insert_post($post);

			if (!$post_id) {
				$report['error'][] = __('Error inserting Post content: ', 'intranet-importer').'&nbsp;'.$new_data->title;
			}else {
				wp_set_post_terms($post_id, 'intraweb, missatges');
				insertDataOnIntranetImport($db_table_name, $new_data->mid, $post_id, $table);
				$report['insert']++;
			}
		}
		return $report;

	} elseif ($table === 'IWdocmanager') {
		$post['post_author']  = 1;
		$post['post_name'] 	  = $new_data->documentName;
		$post['post_type']    = 'bp_doc';
		$post['post_status']  = 'publish';
		$post['post_title']   = $new_data->documentName;
		$post['post_content'] = $new_data->description;
		$post['post_date']    = $new_data->pn_cr_date;

		if ($result) {
			$post['ID'] = $result->id_wp_post;
			$update = wp_update_post($post);
			if ($update === false) {
				$report['error'][] = __('Error Updating Post content: ', 'intranet-importer').'&nbsp;'.$new_data->documentName;
			}else {
				manageBPDocsPermissions($post['ID'], $privacity);
				$report['update']++;
			}
		} else {
			$post_id = wp_insert_post($post);

			if (!$post_id) {
				$report['error'][] = __('Error inserting Post content: ', 'intranet-importer').'&nbsp;'.$new_data->documentName;
			} else {
				addBPDocsCategories($post_id, $new_data->categoryId);
				manageAttachmentDPDocs($post_id, $new_data->fileName, $new_data->fileOriginalName);
				manageBPDocsPermissions($post_id, $privacity);

				insertDataOnIntranetImport($db_table_name, $new_data->documentId, $post_id, $table);
				$report['insert']++;
			}
		}
		return $report;
	}
}

/**
 * Get the complete DPDocs tree of categories
 */
function getBPDocsTreeCategories($categoryName, $categoryId, &$values) {
	global $wpdb;

	if ($result = $wpdb->get_row("SELECT * FROM `IWdocmanager_categories` WHERE categoryId=$categoryId") ) {
		if ($result->parentId != 0) {
			$values[] = $result->categoryName;
			getBPDocsTreeCategories($result->categoryName, $result->parentId, $values);
		}else {
			$values[] = $result->categoryName;
		}
	}
	return $values;
}

/**
 * Sets the BP-Docs categories into the post
 * Sets Intraweb term manually
 */
function addBPDocsCategories($post_id, $categoryId){
	global $wpdb;

	$values = array();
	$categories = getBPDocsTreeCategories($categoryName, $categoryId, $values);
	array_push($categories, 'Intraweb');
	//$categories = array_reverse($categories);

	foreach ($categories as $key=>$category){
		// Set terms for BP-Docs and associate to post
		wp_set_post_terms($post_id, $categories, 'bp_docs_tag');
		wp_set_object_terms($post_id, $categories, 'bp_docs_tag');
		wp_set_post_terms($post_id, $categories, 'bp_docs_access');
		wp_set_object_terms($post_id, $categories, 'bp_docs_access');
	}

}

/**
 * Insert transacction on table import_intranet
 */
function insertDataOnIntranetImport($db_table_name, $id, $post_id, $table) {
	global $wpdb;
	$insert = $wpdb->insert($db_table_name, array(
		"id_intraweb" => $id,
		"id_wp_post" => $post_id,
		"type" => $table
		)
	);
}

/**
 * Get username from old users table (users) by the id
 * Check if the user exists in the new users table (wp_users) and returns the id
 * If not exists returns the admin userId
 */
function getUserId($userId) {
	global $wpdb;

	if ($result = $wpdb->get_row("SELECT * FROM `users` WHERE uid = '$userId'") ) {
		if ($user = get_userdatabylogin($result->uname)) {
			$userId = $user->ID;
		} else {
			$user = get_userdatabylogin('admin');
			$userId = $user->ID;
		}
	}else {
		$user = get_userdatabylogin('admin');
		$userId = $user->ID;
	}

	return $userId;
}

/**
 * Create MetaTags (terms) to assign at the content
 */
function createMetaTags() {
	$tags = array ('Intraweb', 'Noticia', 'Missatge');
	foreach($tags as $tag){
		//manageTerms($tag);
		$term = term_exists($tag, '');
		if ($term !== 0 && $term !== null) {
			// term already exists, do nothing
		} else {
			// create terms
			wp_insert_term( $tag, 'post_tag', array( 'slug' => $tag ) );
		}
	}
}

/**
 * Creates the Intraweb root page
 * The name must be 'intraweb'
 */
function rootIntranetPage() {
	$page_root_id = getPageByName('intraweb');

	if (!empty($page_root_id)) {
		// Intraweb page already exists, get id
		return $page_root_id;
	} else {
		// Create initial page
		$page = array();

		$page['post_content'] 	= __('Content of Intranet Primary Page', 'intranet-importer');
		$page['post_name'] 		= 'intraweb';
		$page['post_title'] 	= __('Title of Intranet Primary Page', 'intranet-importer');
		$page['post_status'] 	= 'publish';
		$page['post_type'] 		= 'page';
		$page['post_author'] 	= 1;

		$page_root_id = wp_insert_post($page);
		setTemplate($page_root_id);

		return $page_root_id;
	}
}

/**
 * Sets template 'Barra Esquerra' on a imported pages
 */
function setTemplate($pageId) {
	$pt = get_page_templates();
	update_post_meta($pageId, '_wp_page_template', $pt['Barra esquerra (Subpàgines)']);
}

/**
 * Search pages into database
 * We use this to search if the home page 'Intraweb' has already been created
 */
function getPageByName($pagename) {
	global $wpdb;

	$db_table_name = $wpdb->prefix . 'posts';
	if ($result = $wpdb->get_row("SELECT * FROM `$db_table_name` WHERE post_name = '" . $pagename . "'", 'ARRAY_A')) {
		return $result['ID'];
	} else {
		return false;
	}
}

/**
 * Import the users from intranets
 * Conditions:
 * 		- Username: Not admin, xtecadmin or convidat
 *		- Email: does not exists in Database and must be unique
 *		- Password: required
 */
function importUsers(){
	global $wpdb;

	$importedUsers = 0;

	$query = 'SELECT * FROM users u LEFT JOIN IWusers iwu ON u.uid=iwu.iw_uid';
	$result = $wpdb->get_results($query);
	manageDataBaseErrors($wpdb);

	if ($result) {
		foreach ($result as $new_user) {
			if ( ($new_user->uname != 'admin') && ($new_user->uname != 'xtecadmin')
					&& ($new_user->uname != 'convidat') ) {
				$user_id = username_exists($new_user->uname);
				// Get hash password for the user (without 1$$)
				$user_password = substr($new_user->pass, 3);

				echo '<div class="intranet">';
                echo '<ul>';
				if ( !$user_id && email_exists($new_user->email) == false && $user_password != '') {
					// Create user without password
					$new_user_id = wp_create_user($new_user->uname, '', $new_user->email);
					// Assign intranet hash password for the user
					$wpdb->update(
						$wpdb->users,
						array( // data
							'user_pass' => $user_password,
							'display_name' => $new_user->iw_nom . ' ' . $new_user->iw_cognom1 . ' ' . $new_user->iw_cognom2
						),
						array('ID' => $new_user_id) // where
					);
					// Update name and surname in wp_usermeta
					update_user_meta( $new_user_id, 'first_name', $new_user->iw_nom );
					update_user_meta( $new_user_id, 'last_name', $new_user->iw_cognom1 . ' ' . $new_user->iw_cognom2 );
					$importedUsers ++;
					echo "<li>";
					echo __('User created: ', 'intranet-importer')."&nbsp;".$new_user->uname."<br/>";
					echo "</li>";
                    echo '</ul>';
                    echo '</div>';
				} else {
					if ($user_id) {
						echo "<li>";
						echo __('User alredy exists: ', 'intranet-importer') . "&nbsp;" . $new_user->uname . "<br/>";
                        echo "</li>";
                        echo '</ul>';
                        echo '</div>';
                        continue;
                    }
                    if (!email_exists($new_user->email) == false) {
                        echo "<li>";
                        echo __('The email address already exists and must be unique: ', 'intranet-importer') . "&nbsp;" . $new_user->uname . ": &nbsp;" . $new_user->email . "<br/>";
                        echo "</li>";
                        echo '</ul>';
                        echo '</div>';
                        continue;
                    }
                    if ($user_password == '') {
                        echo "<li>";
                        echo __('No password provided: ', 'intranet-importer') . "&nbsp;" . $new_user->uname . "<br/>";
                        echo "</li>";
                        echo '</ul>';
                        echo '</div>';
                        continue;
                    }
                    echo '</ul>';
                    echo '</div>';
				}
			}
		}
	}
	return $importedUsers;
}

/**
 * Gets the Special Pages data from Database
 */
function importSpecialPages($privacity) {
	global $wpdb;

    // Get the list of pages
	$table = 'content_page';
	$query = " SELECT * FROM $table WHERE page_active=1";
	$result = $wpdb->get_results($query);
	manageDataBaseErrors($wpdb);

	$report = array(
		'insert' 	=> 0,
		'update' 	=> 0,
		'error'		=> array(),
	);
	if ($result) {
        // Get and process the contents of each page
		foreach ($result as $new_data) {
			$report = getAdvancedContent($new_data, $table, $report, $privacity);
			echo '.';
		}
	}
	return $report;
}

/**
 * Retrieves the content for Special Pages
 * and save it into the post content
 * If found an image, save it into Media Library
 * and change the src
 */
function parseAdvancedContent($resultData){
	$post_content = '';
	$images = array();
    
    // Each iteration process a single content of a page
	foreach ($resultData as $advancedContent) {
		$type = $advancedContent->con_type;
		$advancedContent = unserialize($advancedContent->con_data);

        switch ($type) {
            case 'Heading':
                // Set default value
                $header = (isset($advancedContent['headerSize'])) ? $advancedContent['headerSize'] : 'h3';
                $post_content .= "<$header>" . $advancedContent['text'] . "</$header>";
                break;

            case 'source':
                $lat = $advancedContent["latitude"];
                $long = $advancedContent["longitude"];
                $zoom = $advancedContent["zoom"];
                $post_content .= '<iframe width="350" height="300" frameborder="0" scrolling="no" marginheight="0" src="http://www.openstreetmap.org/export/embed.html?bbox=' . $long . ',' . $lat . '&mamp=' . $zoom . '" style="border: 1px solid black"></iframe>';
                break;

            case 'Vimeo':
                $post_content .= wp_oembed_get($advancedContent['url']) . "<br/>";
                $post_content .= $advancedContent['text'];
                break;

            case 'GoogleMap':
                $lat = $advancedContent["latitude"];
                $long = $advancedContent["longitude"];
                $zoom = $advancedContent["zoom"];
                $post_content .= '<br/><iframe width="350" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=' . $lat . ',' . $long . '&amp;output=embed&z=' . $zoom . '"></iframe><br/><br/>';
                break;

            case 'Quote':
                $post_content .= "<p>";
                $post_content .= $advancedContent['text'] . "<br/>";
                $post_content .= $advancedContent['source'] . "<br/>";
                ;
                $post_content .= $advancedContent['desc'];
                $post_content .= "</p>";
                break;

            case 'ComputerCode':
                $post_content .= "<p>";
                $post_content .= $advancedContent['text'] . "<br/>";
                $post_content .= "</p>";
                break;

            case 'Html':
                // Get the local images in the text
                $images = array_merge($images, get_local_images($advancedContent['text']));

                $post_content .= "<p>";
                $post_content .= $advancedContent['text'] . '<br/>';
                $post_content .= "</p>";

                // break omitted on purpose

            default:
                // URI
                if ($advancedContent['source'] && $advancedContent['desc']) {
                    $post_content .= '<br/><a href="' . $advancedContent['source'] . '">' . $advancedContent['desc'] . '</a><br/>';
                }

                // Video Clip
                if ($advancedContent['url']) {
                    $post_content .= '<br/><a href="' . $advancedContent['url'] . '">' . $advancedContent['clipId'] . '</a><br/>';
                }
        }
	}

	return array($post_content, $images);
}

/**
 * Import the Special Pages content
 */
function getAdvancedContent($new_data, $table, $report, $privacity) {
	global $wpdb;

	$db_table_name = $wpdb->prefix . 'import_intranet';
	$query = "SELECT * FROM `$db_table_name` WHERE id_intraweb = '$new_data->page_id' AND type = '$table' ";
    $result = $wpdb->get_row($query);

    // If imported content is deleted by WordPress admin / editor, import it again and delete record
    $check_record = $wpdb->get_results('SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE ID = ' . $result->id_wp_post);
    if (count($check_record) == 0) {
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'import_intranet WHERE id_wp_post = %d', $result->id_wp_post));
        $result = false;
    }

    manageDataBaseErrors($wpdb);

	$page_root_id = rootIntranetPage();
	$userId = getUserId($new_data->page_cr_uid);

	$post = array();

	$post['post_name']   = $new_data->page_urlname;
	$post['post_title']  = $new_data->page_title;
	$post['post_status'] = $privacity;
	$post['post_type']   = 'page';
	$post['post_author'] = $userId;
	$post['post_parent'] = $page_root_id;
	$post['post_date']   = $new_data->page_cr_date;

    // Get all the contents of a page
	$queryData = "SELECT `con_type`, `con_data` FROM content_content WHERE con_pageid = '$new_data->page_id' AND con_active = 1 ORDER BY con_areaindex ASC ";
	$resultData = $wpdb->get_results($queryData);
	manageDataBaseErrors($wpdb);

    // Process the contents
	if ($resultData) {
		list($post['post_content'], $images) = parseAdvancedContent($resultData);
	}
    
	if ($result) {
		$post['ID']	= $result->id_wp_post;

		$update = wp_update_post($post);
		if ($update === false) {
			$report['error'][] = __('Error Updating Post content: ', 'intranet-importer').'&nbsp;'.$new_data->page_title;
		}else {
			setTemplate($post['ID']);

			if(!empty($images)){
				manageImagesForSpecialImages($images, $post['ID'], $post);
			}
			$report['update']++;
		}
	} else {
		$post_id = wp_insert_post($post);

		if (!$post_id) {
			$report['error'][] = __('Error inserting Post content: ', 'intranet-importer').'&nbsp;'.$new_data->page_title;
		}else {
			setTemplate($post_id);
			if(!empty($images)){
				manageImagesForSpecialImages($images, $post_id, $post);
			}
			insertDataOnIntranetImport($db_table_name, $new_data->page_id, $post_id, $table);
			$report['insert']++;
		}
	}

	return $report;
}

/**
 * Manage the images in Special Pages
 * Upload embedded images from Zikula to Wordpress Upload Directory
 */
function manageImagesForSpecialImages($images, $post_id, $post) {
	global $wpdb;
	global $agora;
	foreach ($images as $image){
		$dbName = $wpdb->dbname;

		$file = $agora['server']['root'].$agora['intranet']['datadir'].$dbName.'/data/'.$image;
		$filename = basename($image);
		$wp_upload_dir = wp_upload_dir();

		if (file_exists($file)) {
			$upload_file = wp_upload_bits($filename, null, file_get_contents($file));

			if (!$upload_file['error']) {
				$image_src = $wp_upload_dir['url'] . '/' . $filename ;
				$query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE guid='$image_src'";
				$count = $wpdb->get_var($query);
				manageDataBaseErrors($wpdb);

				if ($count > 0){
					// Image already exists, change src
					$img = $wp_upload_dir['url'] . '/' . $filename;
				} else {
					createAttachment($upload_file, $filename, $wp_upload_dir, $post_id);
					$img = $wp_upload_dir['url'] . '/' . $filename;
				}
			}
		}

		$post['ID']	= $post_id;
		$post['post_content'] .= "<div class='alignnone size-medium'><img src='".$img."'></div>";
		wp_update_post($post);
	}
}

/**
 * Get IWdocmanager real path
 */
function getBPDocsPath() {
	global $wpdb;

	$query = "SELECT * FROM `module_vars` WHERE `modname`='IWmain' AND `name`='documentRoot'";
	$result = $wpdb->get_results($query);
	manageDataBaseErrors($wpdb);

	$filePath = unserialize($result[0]->value);

	$query = "SELECT * FROM `module_vars` WHERE `modname`='IWdocmanager' AND `name`='documentsFolder'";
	$result = $wpdb->get_results($query);
	manageDataBaseErrors($wpdb);

	$docsFolder = unserialize($result[0]->value);

	$path = $filePath."/".$docsFolder;
	return $path;

}

/**
 * Creates an attachment for BP-DOCS.
 *
 * 1- Copy the original file of the intranet to the uploads folder
 * 		Original Directory: '/srv/www/agora/zkdata/usu1/data/descarregues/'
 * 		Destiny  Directory: '/srv/www/agora/docs/wpdata/DBNAME/YYYY/MM/filename'
 * 2- Create the attachment and is associated to the post
 * 		* The $attachment_data only works  for images
 * 3- Copy the file of the uploads folder to bp-attachments directory
 * 		Directory: '/srv/www/agora/docs/wpdata/DBNAME/bp-attachments/POSTID
 */
function manageAttachmentDPDocs($postId, $fileName, $fullFileName){
	global $wpdb;
	global $agora;

	$path = getBPDocsPath();
	$file = $path."/".$fileName;
	$filename = basename($fullFileName);
	$wp_upload_dir = wp_upload_dir();

	$dbName = $wpdb->dbname;
	// Check if file exists in original folder
	if (file_exists($file)) {
		// Upload file into docs folder (/srv/www/agora/docs/wpdata/DBname/YYYY/MM/filename)
		$upload_file = wp_upload_bits($filename, null, file_get_contents($file));

		if (!$upload_file['error']) {
			createAttachment($upload_file, $filename, $wp_upload_dir, $postId);

			$originFile = $upload_file['file'];
			$destinyDir = $agora['server']['root'].$agora['nodes']['datadir'].$dbName.'/bp-attachments/'.$postId;
			$finalFileName = basename($originFile);

			if (!is_dir($destinyDir)) {
				mkdir($destinyDir);
			}
			// Copy file into bp-attachments directory
			if (!copy($originFile, $destinyDir.'/'.$finalFileName)) {
				echo __('Error coping file '.$filename."<br/>", 'intranet-importer');
			}
		}
	}else {
		echo __('File not found: '.$filename."<br/>", 'intranet-importer');
	}
}

/**
 * Search Images and Pdf files into the content
 * Then calls processSingleContent function to process
 */
function parseSingleContent($content) {
	preg_match_all('/src="([^"]+)"/', $content, $srcImagesArray, PREG_SET_ORDER);
	preg_match_all('/href="([^"]+)"/', $content, $srcLinkArray, PREG_SET_ORDER);

	$content = processSingleContent($content, $srcImagesArray, false);
	$content = processSingleContent($content, $srcLinkArray, true);

	return $content;
}

/**
 * Process the Single Content for manage embedded images and Pdf files
 *   Images:
 *  	1- Search Embed Images: Copy old images into Wordpress uploads folder
 *  	2- Change the original src for the new path
 *   PDF:
 *   	1- Search PDF links: Copy old PDF links into Wordpress uploads folder
 *  	2- Change the original href for the new path
 */

function processSingleContent($content, $srcArray, $pdf) {
	global $wpdb, $agora;

	if (!empty($srcArray)) {
		foreach ($srcArray as $tag => $src) {
			$pos = strpos($src[0], 'file.php?file=');
			if($pos !== FALSE){
				$path = explode("file.php?file=", $src[0]);
				$srcPath = str_replace('"',"", $path[1]);
				$srcPath = str_replace('%20'," ", $srcPath);

				if ($pdf == true) {
					$extension = explode(".", $srcPath);
					if ($extension[1] != 'pdf') {
						// It's a link without an embedded pdf file
						continue;
					}
				}

				$file = $agora['server']['root'] . $agora['intranet']['datadir'] . $wpdb->dbname . '/data/' . $srcPath;
				$filename = basename($srcPath);
				$wp_upload_dir = wp_upload_dir();

				if (file_exists($file)) {
					// Check if Image or Pdf already exist into media library
					$path_to_check = $wp_upload_dir['url'] . '/' . $filename ;
					$query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE guid='$path_to_check'";
					$count = $wpdb->get_var($query);
					manageDataBaseErrors($wpdb);

					if ($count > 0){
						// Embedded content already exists, change src
						$newPath = $wp_upload_dir['url'].'/'.$filename;
						if ($pdf == true) {
							$content = str_replace($src[0], '<a href="'.$newPath.'"', $content);
						} else {
							$content = str_replace($src[0], '<img src="'.$newPath.'"', $content);
						}
					} else {
						// Embedded content doesnt exists, upload and change src
						$upload_file = wp_upload_bits($filename, null, file_get_contents($file));

						if (!$upload_file['error']) {
							createAttachment($upload_file, $filename, $wp_upload_dir);
							$newPath = $wp_upload_dir['url'].'/'.$filename;
							if ($pdf == true) {
								$content = str_replace($src[0], '<a href="'.$newPath.'"', $content);
							}else {
								$content = str_replace($src[0], '<img src="'.$newPath.'"', $content);
							}
						}
					}
				}
			}
		}
	}
	return $content;
}

/**
 * Creates the attachment for Media Library
  */
function createAttachment($upload_file, $filename, $wp_upload_dir, $post_id = NULL) {
	// Creates metadata (table wp_postmeta)
	$wp_filetype = wp_check_filetype($filename, null);

	$attachment = array(
		'guid' => $wp_upload_dir['url'] . '/' . $filename,
		'post_mime_type' => $wp_filetype['type'],
		'post_parent' => $post_id,
		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content' => '',
		'post_status' => 'inherit'
	);

	// Creates the attachment and associates him at the postId
	$attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $post_id);
	if (!is_wp_error($attachment_id)) {
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
		wp_update_attachment_metadata($attachment_id,  $attachment_data);

		if (!is_null($post_id)) {
			// set the featured image
			update_post_meta($post_id, '_thumbnail_id', $attachment_id);
		}
	}
}

/**
 * Sets the permissions to the document
 */
function manageBPDocsPermissions($post_id, $privacity) {
	global $wpdb;

	$db_table_name = $wpdb->prefix . 'postmeta';

	// Check if access to post already exists
	$query = "SELECT * FROM `$db_table_name` WHERE post_id='$post_id' AND meta_key='bp_docs_settings'";
	$result = $wpdb->get_results($query);
	manageDataBaseErrors($wpdb);

	if (!$result) {
		// Not exists, proceed to create access rights
		if ($privacity == 'private') {
			$meta_value = 'a:5:{s:4:"read";s:7:"creator";s:4:"edit";s:7:"creator";s:13:"read_comments";s:7:"creator";s:13:"post_comments";s:7:"creator";s:12:"view_history";s:7:"creator";}';
			$wpdb->insert($db_table_name, array(
					'post_id' => $post_id,
					'meta_key' => 'bp_docs_settings',
					'meta_value' => $meta_value
				)
			);
		} else {
			$meta_value = 'a:5:{s:4:"read";s:6:"anyone";s:4:"edit";s:8:"loggedin";s:13:"read_comments";s:6:"anyone";s:13:"post_comments";s:8:"loggedin";s:12:"view_history";s:6:"anyone";}';
			$wpdb->insert($db_table_name, array(
					'post_id' => $post_id,
					'meta_key' => 'bp_docs_settings',
					'meta_value' => $meta_value
				)
			);
		}
	}else {
		// already exists, update the access rights
		if ($privacity == 'private') {
			$meta_value = 'a:5:{s:4:"read";s:7:"creator";s:4:"edit";s:7:"creator";s:13:"read_comments";s:7:"creator";s:13:"post_comments";s:7:"creator";s:12:"view_history";s:7:"creator";}';
			$wpdb->update($db_table_name,
					array(
						'meta_value' => $meta_value
					),
					array(
						'meta_key' => 'bp_docs_settings',
						'post_id' => $post_id,
					)
				);
		} else {
			$meta_value = 'a:5:{s:4:"read";s:6:"anyone";s:4:"edit";s:8:"loggedin";s:13:"read_comments";s:6:"anyone";s:13:"post_comments";s:8:"loggedin";s:12:"view_history";s:6:"anyone";}';
			$wpdb->update($db_table_name,
					array(
						'meta_value' => $meta_value
					),
					array(
						'meta_key' => 'bp_docs_settings',
						'post_id' => $post_id,
					)
			);
		}
	}
}


/**
 *  Extract path of the images located in zkdata
 * 
 *  @author Toni Ginard
 *  @param string $text HTML text that may include links to local images
 *  @return array Images found in the text
 */
function get_local_images($text) {

    $dom = new DOMDocument;
    libxml_use_internal_errors(true); // Don't show warnings due to malformed HTML code
    $dom->loadHTML($text);
    $images = array();

    foreach ($dom->getElementsByTagName('img') as $node) {
        $dom->saveHtml($node);

        if ($node->hasAttribute('src')) {
            $src = $node->getAttribute('src');
            $pos = 0;
            $offset = 'file.php?file=';

            // Get the relative path of the public images in Intraweb
            while (($pos = strpos($src, $offset, $pos)) !== false) {
                $pos += strlen($offset);
                $images[] = substr($src, $pos);
            }
        }
    }

    return $images;
}
