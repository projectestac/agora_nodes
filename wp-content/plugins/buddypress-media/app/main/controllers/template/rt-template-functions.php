<?php

/**
 * Checks at any point of time any media is left to be processed in the db pool
 *
 * @global type $rtmedia_query
 * @return type
 */
function have_rtmedia() {
	global $rtmedia_query;

	return $rtmedia_query->have_media();
}

/**
 * Rewinds the db pool of media album and resets it to begining
 *
 * @global type $rtmedia_query
 * @return type
 */
function rewind_rtmedia() {

	global $rtmedia_query;

	return $rtmedia_query->rewind_media();
}

/**
 * moves ahead in the loop of media within the album
 *
 * @global type $rtmedia_query
 * @return type
 */
function rtmedia() {
	global $rtmedia_query;

	return $rtmedia_query->rtmedia();
}

/**
 * echo the title of the media
 *
 * @global type $rtmedia_media
 */
function rtmedia_title() {

	global $rtmedia_backbone;
	if ( $rtmedia_backbone[ 'backbone' ] ){
		echo '<%= media_title %>';
	} else {
		global $rtmedia_media;

		return $rtmedia_media->media_title;
	}
}

function get_rtmedia_gallery_title() {
	global $rtmedia_query;
	$title = false;
	if ( isset( $rtmedia_query->media_query[ 'media_type' ] ) && ! is_array( $rtmedia_query->media_query[ 'media_type' ] ) && $rtmedia_query->media_query[ 'media_type' ] != "" ){
		$photos_title = __( 'All Photos', 'rtmedia' );
		$videos_title = __( 'All Videos', 'rtmedia' );
		$music_title  = __( 'All Music', 'rtmedia' );
		if ( $rtmedia_query->media_query[ 'media_type' ] == "music" ){
			$title = ucwords( __( 'All ' . $rtmedia_query->media_query[ 'media_type' ], 'rtmedia' ) );
		} else {
			$title = ucwords( __( 'All ' . $rtmedia_query->media_query[ 'media_type' ] . "s", 'rtmedia' ) );
		}

		return $title;
	}
	if ( isset( $rtmedia_query->query[ 'media_type' ] ) && $rtmedia_query->query[ 'media_type' ] == "album" && isset( $rtmedia_query->media_query[ 'album_id' ] ) && $rtmedia_query->media_query[ 'album_id' ] != "" ){
		$id = $rtmedia_query->media_query[ 'album_id' ];

		return get_rtmedia_title( $id );
	}
	$title = apply_filters( 'rtmedia_gallery_title', $title );

	return $title;
}

function get_rtmedia_title( $id ) {
	$rtmedia_model = new RTMediaModel();
	$title         = $rtmedia_model->get( array( 'id' => $id ) );

	return $title[ 0 ]->media_title;
}

function rtmedia_author_profile_pic( $show_link = true, $echo = true, $author_id = false ) {
	global $rtmedia_backbone;
	if ( $rtmedia_backbone[ 'backbone' ] ){
		echo '';
	} else {
		if ( ! $author_id || $author_id == "" ){
			global $rtmedia_media;
			$author_id = $rtmedia_media->media_author;
		}

		$show_link   = apply_filters( "rtmedia_single_media_show_profile_picture_link", $show_link );
		$profile_pic = "";

		if ( $show_link ){
			$profile_pic .= "<a href='" . get_rtmedia_user_link( $author_id ) . "' title='" . rtmedia_get_author_name( $author_id ) . "'>";
		}
		$size = apply_filters( "rtmedia_single_media_profile_picture_size", 90 );
		if ( function_exists( "bp_get_user_has_avatar" ) ){
			if ( bp_core_fetch_avatar( array( 'item_id' => $author_id, 'object' => 'user', 'no_grav' => false, 'html' => false ) ) != bp_core_avatar_default() ){
				$profile_pic .= bp_core_fetch_avatar( array( 'item_id' => $author_id, 'object' => 'user', 'no_grav' => false, 'html' => true, 'width' => $size, 'height' => $size ) );
			} else {
				$profile_pic .= "<img src='" . bp_core_avatar_default() . "' width='" . $size . "'  height='" . $size . "'/>";
			}
		} else {
			$profile_pic .= get_avatar( $author_id, $size );
		}
		if ( $show_link ){
			$profile_pic .= "</a>";
		}

		if ( $echo ){
			echo $profile_pic;
		} else {
			return $profile_pic;
		}
	}
}

function rtmedia_author_name( $show_link = true ) {

	global $rtmedia_backbone;
	if ( $rtmedia_backbone[ 'backbone' ] ){
		echo apply_filters( 'rtmedia_media_author_backbone', '',$show_link );
	} else {
		global $rtmedia_media;
		$show_link = apply_filters( "rtmedia_single_media_show_profile_name_link", $show_link );
		if ( $show_link ){
			echo "<a href='" . get_rtmedia_user_link( $rtmedia_media->media_author ) . "' title='" . rtmedia_get_author_name( $rtmedia_media->media_author ) . "'>";
		}
		echo rtmedia_get_author_name( $rtmedia_media->media_author );
		if ( $show_link ){
			echo "</a>";
		}
	}
}

function rtmedia_get_author_name( $user_id ) {
	if ( function_exists( "bp_core_get_user_displayname" ) ){
		return bp_core_get_user_displayname( $user_id );
	} else {
		$user = get_userdata( $user_id );
		if ( $user ){
			return $user->display_name;
		}
	}
}

function rtmedia_media_gallery_class() {
	global $rtmedia_query;
	$classes = '';
	if ( isset ( $rtmedia_query->media_query ) && isset ( $rtmedia_query->media_query[ "context_id" ] ) ){
		$classes = "context-id-" . $rtmedia_query->media_query[ "context_id" ];
	}

	echo apply_filters( 'rtmedia_gallery_class_filter', $classes );
}

function rtmedia_id( $media_id = false ) {
	global $rtmedia_backbone;

	if ( $rtmedia_backbone[ 'backbone' ] ){
		return '<%= id %>';
	}

	if ( $media_id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'media_id' => $media_id ), 0, 1 );
		if ( isset( $media ) && sizeof( $media ) > 0 ){
			return $media[ 0 ]->id;
		}

		return false;
	} else {
		global $rtmedia_media;

		return $rtmedia_media->id;
	}
}

function rtmedia_media_id( $id = false ) {
	if ( $id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'id' => $id ), 0, 1 );

		return $media[ 0 ]->media_id;
	} else {
		global $rtmedia_media;

		return $rtmedia_media->media_id;
	}
}

function rtmedia_activity_id( $id = false ) {
	if ( $id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'id' => $id ), 0, 1 );

		return $media[ 0 ]->activity_id;
	} else {
		global $rtmedia_media;

		return $rtmedia_media->activity_id;
	}
}

function rtmedia_type( $id = false ) {
	if ( $id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'id' => $id ), 0, 1 );

		return $media[ 0 ]->media_type;
	} else {
		global $rtmedia_media;

		return $rtmedia_media->media_type;
	}
}

function rtmedia_cover_art( $id = false ) {
	if ( $id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'id' => $id ), 0, 1 );

		return $media[ 0 ]->cover_art;
	} else {
		global $rtmedia_media;

		return $rtmedia_media->cover_art;
	}
}

/**
 * echo parmalink of the media
 *
 * @global type $rtmedia_media
 */
function rtmedia_permalink( $media_id = false ) {

	global $rtmedia_backbone;

	if ( $rtmedia_backbone[ 'backbone' ] ){
		echo '<%= rt_permalink %>';
	} else {
		echo get_rtmedia_permalink( rtmedia_id( $media_id ) );
	}
}

function rtmedia_media( $size_flag = true, $echo = true, $media_size = "rt_media_single_image" ) {
	$size_flag = true;
	global $rtmedia_media, $rtmedia;
	if ( isset ( $rtmedia_media->media_type ) ){
		if ( $rtmedia_media->media_type == 'photo' ){
			$src  = wp_get_attachment_image_src( $rtmedia_media->media_id, $media_size );
			$html = "<img src='" . $src[ 0 ] . "' alt='" . $rtmedia_media->post_name . "' />";
		} elseif ( $rtmedia_media->media_type == 'video' ) {
			$size = " width=\"" . $rtmedia->options[ "defaultSizes_video_singlePlayer_width" ] . "\" height=\"" . $rtmedia->options[ "defaultSizes_video_singlePlayer_height" ] . "\" ";
			$html = "<div id='rtm-mejs-video-container' style='width:" . $rtmedia->options[ "defaultSizes_video_singlePlayer_width" ] . "px;max-width:96%'>";
			$html .= '<video src="' . wp_get_attachment_url( $rtmedia_media->media_id ) . '" ' . $size . ' type="video/mp4" class="wp-video-shortcode" id="bp_media_video_' . $rtmedia_media->id . '" controls="controls" preload="true"></video>';
			$html .= '</div>';
		} elseif ( $rtmedia_media->media_type == 'music' ) {
			$size = ' width="400" height="30" ';
			if ( ! $size_flag ){
				$size = '';
			}
			$html = '<audio src="' . wp_get_attachment_url( $rtmedia_media->media_id ) . '" ' . $size . ' type="audio/mp3" class="wp-audio-shortcode" id="bp_media_audio_' . $rtmedia_media->id . '" controls="controls" preload="none"></audio>';
		} else {
			$html = false;
		}
	} else {
		$html = false;
	}

	do_action( 'rtmedia_after_' . $rtmedia_media->media_type, $rtmedia_media->id );

	$html = apply_filters( 'rtmedia_single_content_filter', $html, $rtmedia_media );

	if ( $echo ){
		echo $html;
	} else {
		return $html;
	}
}

/*
 * echo http url of the media
 */

function rtmedia_image( $size = 'rt_media_thumbnail', $id = false, $recho = true ) {
	global $rtmedia_backbone;

	if ( $rtmedia_backbone[ 'backbone' ] ){
		echo '<%= guid %>';

		return;
	}

	if ( $id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'id' => $id ), false, false );
		if ( isset ( $media[ 0 ] ) ){
			$media_object = $media[ 0 ];
		} else {
			return false;
		}
	} else {
		global $rtmedia_media;
		$media_object = $rtmedia_media;
	}

	$thumbnail_id = 0;
	if ( isset ( $media_object->media_type ) ){
		if ( $media_object->media_type == 'album' || $media_object->media_type != 'photo' || $media_object->media_type == 'video' ){
			$thumbnail_id = ( isset ( $media_object->cover_art ) && ( $media_object->cover_art != "0" ) ) ? $media_object->cover_art : false;
			$thumbnail_id = apply_filters( 'show_custom_album_cover', $thumbnail_id, $media_object->media_type, $media_object->id ); // for rtMedia pro users
		} elseif ( $media_object->media_type == 'photo' ) {
			$thumbnail_id = $media_object->media_id;
		} else {
			$thumbnail_id = false;
		}
		if ( $media_object->media_type == 'music' && $thumbnail_id == "" ){
			$thumbnail_id = get_music_cover_art( get_attached_file( $media_object->media_id ), $media_object->id );
		}
		if ( $media_object->media_type == 'music' && $thumbnail_id == "-1" ){
			$thumbnail_id = false;
		}

	} else {
		$src = false;
	}

	if ( ! $thumbnail_id ){
		global $rtmedia;
		if ( isset ( $rtmedia->allowed_types[ $media_object->media_type ] ) && isset ( $rtmedia->allowed_types[ $media_object->media_type ][ 'thumbnail' ] ) ){
			$src = $rtmedia->allowed_types[ $media_object->media_type ][ 'thumbnail' ];
		} elseif ( $media_object->media_type == 'album' ) {
			$src = rtmedia_album_image( $size, $id );
		} else {
			$src = false;
		}
	} else {
		if ( is_numeric( $thumbnail_id ) && $thumbnail_id != "0" ){

			list( $src, $width, $height ) = wp_get_attachment_image_src( $thumbnail_id, $size );
		} else {
			$src = $thumbnail_id;
		}
	}

	$src = apply_filters( 'rtmedia_media_thumb', $src, $media_object->id, $media_object->media_type );
	if ( $recho == true ){
		echo $src;
	} else {
		return $src;
	}
}

function rtmedia_image_alt( $id = false, $echo = true ) {
	global $rtmedia_media;
	$model = new RTMediaModel();
	if ( $id ){
		$model = new RTMediaModel();
		$media = $model->get_media( array( 'id' => $id ), false, false );
		if ( isset ( $media[ 0 ] ) ){
			$media_object = $media[ 0 ];
		} else {
			return false;
		}
		$post_object = get_post( $media_object->media_id );
		if ( isset( $post_object->post_name ) ){
			$img_alt = $post_object->post_name;
		} else {
			$img_alt = " ";
		}
	} else {
		global $rtmedia_media;
		$media_object = $rtmedia_media;
		if ( isset( $media_object->post_name ) ){
			$img_alt = $media_object->post_name;
		} else {
			$img_alt = " ";
		}
	}
	if ( $echo ){
		echo $img_alt;
	} else {
		return $img_alt;
	}
}

function rtmedia_album_image( $size = 'thumbnail', $id = false ) {
	global $rtmedia_media;
	$model = new RTMediaModel();
	if ( $id == false ){
		$id = $rtmedia_media->id;
	}
	global $rtmedia_query;
	if ( isset( $rtmedia_query->query[ 'context_id' ] ) && isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] != "group" ){
		$media = $model->get_media( array( 'album_id' => $id, 'media_type' => 'photo', 'media_author' => $rtmedia_query->query[ 'context_id' ] ), 0, 1 );
	} else {
		if ( isset( $rtmedia_query->query[ 'context_id' ] ) && isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == "group" ){
			$media = $model->get_media( array( 'album_id' => $id, 'media_type' => 'photo', 'context_id' => $rtmedia_query->query[ 'context_id' ] ), 0, 1 );
		} else {
			$media = $model->get_media( array( 'album_id' => $id, 'media_type' => 'photo' ), 0, 1 );
		}
	}

	if ( $media ){
		$src = rtmedia_image( $size, $media[ 0 ]->id, false );
	} else {
		global $rtmedia;
		$src = $rtmedia->allowed_types[ 'photo' ][ 'thumbnail' ];
	}

	return $src;
}

function rtmedia_sanitize_object( $data, $exceptions = array() ) {
	foreach ( $data as $key => $value ) {
		if ( ! in_array( $key, array_merge( RTMediaMedia::$default_object, $exceptions ) ) ){
			unset ( $data[ $key ] );
		}
	}

	return $data;
}

function rtmedia_delete_allowed() {
	global $rtmedia_media;

	$flag = $rtmedia_media->media_author == get_current_user_id();

	if ( ! $flag && isset( $rtmedia_media->context ) && $rtmedia_media->context == 'group' && function_exists( 'bp_group_is_admin' ) ){
		$flag = ( bp_group_is_admin() || bp_group_is_mod() );
	}

	if ( ! $flag ){
		$flag = is_super_admin();
	}

	$flag = apply_filters( 'rtmedia_media_delete_priv', $flag );

	return $flag;
}

function rtmedia_edit_allowed() {

	global $rtmedia_media;

	$flag = $rtmedia_media->media_author == get_current_user_id();

	if ( ! $flag ){
		$flag = is_super_admin();
	}

	$flag = apply_filters( 'rtmedia_media_edit_priv', $flag );

	return $flag;
}

function rtmedia_request_action() {
	global $rtmedia_query;

	return $rtmedia_query->action_query->action;
}

function rtmedia_title_input() {
	global $rtmedia_media;

	$name  = 'media_title';
	$value = $rtmedia_media->media_title;

	$html = '';

	if ( rtmedia_request_action() == 'edit' ){
		$html .= '<input type="text" class="rtmedia-title-editor" name="' . $name . '" id="' . $name . '" value="' . $value . '">';
	} else {
		$html .= '<h2 name="' . $name . '" id="' . $name . '">' . $value . '</h2>';
	}

	$html .= '';

	echo $html;
}

function rtmedia_description_input( $editor = true ) {
	global $rtmedia_media;

	$name = 'description';
	if ( isset( $rtmedia_media->post_content ) ){
		$value = $rtmedia_media->post_content;
	} else {
		$post_details = get_post( $rtmedia_media->media_id );
		$value        = $post_details->post_content;
	}


	$html = '';
	if ( $editor ){
		if ( rtmedia_request_action() == 'edit' ){
			$html .= wp_editor( $value, $name, array( 'media_buttons' => false, 'textarea_rows' => 2, 'quicktags' => false ) );
		} else {
			$html .= '<div name="' . $name . '" id="' . $name . '">' . $value . '</div>';
		}
	} else {
		$html .= "<textarea name='" . $name . "' id='" . $name . "' class='rtmedia-desc-textarea'>" . strip_tags( $value ) . "</textarea>";
	}
	$html .= '';

	return $html;
}

/**
 * echo media description
 *
 * @global type $rtmedia_media
 */
function rtmedia_description( $echo = true ) {
	global $rtmedia_media;
	if ( $echo ){
		echo get_post_field( "post_content", $rtmedia_media->media_id );
	} else {
		return get_post_field( "post_content", $rtmedia_media->media_id );
	}
	//echo $rtmedia_media->post_content;
}

/**
 * returns total media count in the album
 *
 * @global type $rtmedia_query
 * @return type
 */
function rtmedia_count() {
	global $rtmedia_query;

	return $rtmedia_query->media_count;
}

/**
 * returns the page offset for the media pool
 *
 * @global type $rtmedia_query
 * @return type
 */
function rtmedia_offset() {
	global $rtmedia_query;

	return ( $rtmedia_query->action_query->page - 1 ) * $rtmedia_query->action_query->per_page_media;
}

/**
 * returns number of media per page to be displayed
 *
 * @global type $rtmedia_query
 * @return type
 */
function rtmedia_per_page_media() {
	global $rtmedia_query;

	return $rtmedia_query->action_query->per_page_media;
}

/**
 * returns the page number of media album in the pagination
 *
 * @global type $rtmedia_query
 * @return type
 */
function rtmedia_page() {
	global $rtmedia_query;

	return $rtmedia_query->action_query->page;
}

/**
 * returns the current media number in the album pool
 *
 * @global type $rtmedia_query
 * @return type
 */
function rtmedia_current_media() {
	global $rtmedia_query;

	return $rtmedia_query->current_media;
}

//rtmedia media_author actions
add_action( 'after_rtmedia_action_buttons', 'rtmedia_author_actions' );
function rtmedia_author_actions() {

	$options_start = $options_end = $option_buttons = $output = "";
	$options       = array();
	$options       = apply_filters( 'rtmedia_author_media_options', $options );

	if ( ! empty( $options ) ){

		$options_start = '<span class="click-nav" id="rtm-media-options-list">
                <span class="no-js">
                <button class="clicker rtmedia-media-options rtmedia-action-buttons button"><i class="rtmicon-cog"></i>' . __( 'Options', 'rtmedia' ) . '</button>
                <ul class="rtm-options">';
		foreach ( $options as $action ) {
			if ( $action != "" ){
				$option_buttons .= "<li>" . $action . "</li>";
			}
		}

		$options_end = "</ul></span></span>";

		if ( $option_buttons != "" ){
			$output = $options_start . $option_buttons . $options_end;
		}

		if ( $output != "" ){
			echo $output;
		}
	}
}

function rtmedia_edit_form() {

	if ( is_user_logged_in() && rtmedia_edit_allowed() ){

		$edit_button = '<button type="submit" class="rtmedia-edit rtmedia-action-buttons" ><i class="rtmicon-edit"></i>' . __( 'Edit', 'rtmedia' ) . '</button>';

		$edit_button = apply_filters( 'rtmedia_edit_button_filter', $edit_button );

		$button = '<form action="' . get_rtmedia_permalink( rtmedia_id() ) . 'edit/">' . $edit_button . "</form>";

		return $button;
	}

	return false;
}

/**
 *
 */
function rtmedia_actions() {

	$actions = array();

	if ( is_user_logged_in() && rtmedia_edit_allowed() ){

		$edit_button = '<button type="submit" class="rtmedia-edit rtmedia-action-buttons button" ><i class="rtmicon-edit"></i>' . __( 'Edit', 'rtmedia' ) . '</button>';

		$edit_button = apply_filters( 'rtmedia_edit_button_filter', $edit_button );

		$actions[ ] = '<form action="' . get_rtmedia_permalink( rtmedia_id() ) . 'edit/">' . $edit_button . "</form>";
	}
	$actions = apply_filters( 'rtmedia_action_buttons_before_delete', $actions );
	foreach ( $actions as $action ) {
		echo $action;
	}
	$actions = array();

	if ( rtmedia_delete_allowed() ){
		//add_filter('rtmedia_addons_action_buttons','rtmedia_delete_action_button',10,1);
		$actions[ ] = rtmedia_delete_form( $echo = false );
	}

	$actions = apply_filters( 'rtmedia_action_buttons_after_delete', $actions );

	foreach ( $actions as $action ) {
		echo $action;
	}
	do_action( "after_rtmedia_action_buttons" );
}


/**
 *  rendering comments section
 */
function rtmedia_comments( $echo = true ) {

	$html = '<ul id="rtmedia_comment_ul" class="large-block-grid-1" data-action="' . get_rtmedia_permalink( rtmedia_id() ) . 'delete-comment/">';

	global $wpdb, $rtmedia_media;

	$comments = $wpdb->get_results( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = '" . $rtmedia_media->media_id . "'", ARRAY_A );

	$comment_list = "";
	foreach ( $comments as $comment ) {
		$comment_list .= rmedia_single_comment( $comment );
	}

	if ( $comment_list != "" ){
		$html .= $comment_list;
	} else {
		$html .= "<li id='rtmedia-no-comments' class='rtmedia-no-comments'>" . __( apply_filters( 'rtmedia_single_media_no_comment_messege', __( 'There are no comments on this media yet.', 'rtmedia' ) ) ) . "</li>";
	}

	$html .= '</ul>';

	if ( $html ){
		echo $html;
	} else {
		return $html;
	}
}

function rmedia_single_comment( $comment ) {
	$html = "";
	$html .= '<li class="rtmedia-comment">';
	if ( $comment[ 'user_id' ] ){
		$user_link = "<a href='" . get_rtmedia_user_link( $comment[ 'user_id' ] ) . "' title='" . rtmedia_get_author_name( $comment[ 'user_id' ] ) . "'>" . rtmedia_get_author_name( $comment[ 'user_id' ] ) . "</a>";
		$user_name   =  apply_filters( 'rtmedia_comment_author_name',$user_link, $comment  ) ;
		$profile_pic = rtmedia_author_profile_pic( $show_link = true, $echo = false, $comment[ 'user_id' ] );
	} else {
		$user_name   = "Annonymous";
		$profile_pic = "";
	}
	if ( $profile_pic != "" ){
		$html .= "<div class='rtmedia-comment-user-pic cleafix'>" . $profile_pic . "</div>";
	}
	$html .= "<div><div class='rtmedia-comment-details'>";
	$html .= '<span class ="rtmedia-comment-author">' . '' . $user_name . '</span>';

	$html .= '<div class="rtmedia-comment-content">' . wpautop( make_clickable( $comment[ 'comment_content' ] ) ) . '</div>';

	global $rtmedia_media;
	if ( isset( $comment[ 'user_id' ] ) && isset( $rtmedia_media->media_author ) && ( is_rt_admin() || ( get_current_user_id() == $comment[ 'user_id' ] || $rtmedia_media->media_author == get_current_user_id() ) ) ){ // show delete button for comment author and admins
		$html .= '<i data-id="' . $comment[ 'comment_ID' ] . '" class = "rtmedia-delete-comment rtmicon-times" title="' . __( 'Delete Comment' ) . '"></i>';
	}

	$html .= '<span class ="rtmedia-comment-date"> ' . apply_filters( 'rtmedia_comment_date_format', rtmedia_convert_date( $comment[ 'comment_date_gmt' ] ), $comment ) . '</span>';
	$html .= '<div class="clear"></div></div></div></li>';

	return apply_filters( 'rtmedia_single_comment', $html, $comment );
}

function rtmedia_get_media_comment_count( $media_id = false ) {
	global $wpdb, $rtmedia_media;
	if( ! $media_id ) {
		$post_id = $rtmedia_media->media_id;
	} else {
		$post_id = rtmedia_media_id( $media_id );
	}
	$query = "SELECT count(*) FROM $wpdb->comments WHERE comment_post_ID = '" . $post_id . "'";
	$comment_count = $wpdb->get_results( $query, ARRAY_N );
	if( is_array( $comment_count ) && is_array( $comment_count[0] ) && isset( $comment_count[0][0] ) ) {
		return $comment_count[0][0];
	} else {
		return 0;
	}
}

function rtmedia_pagination_prev_link() {

	global $rtmedia_media, $rtmedia_interaction, $rtmedia_query;

	$page_url    = ( ( rtmedia_page() - 1 ) == 1 ) ? "" : "pg/" . ( rtmedia_page() - 1 );
	$site_url    = ( is_multisite() ) ? trailingslashit( get_site_url( get_current_blog_id() ) ) : trailingslashit( get_site_url() );
	$author_name = get_query_var( 'author_name' );
	$link        = '';

	if ( $rtmedia_interaction && isset( $rtmedia_interaction->context ) && $rtmedia_interaction->context->type == "profile" ){
		if ( function_exists( "bp_core_get_user_domain" ) ){
			$link .= trailingslashit( bp_core_get_user_domain( $rtmedia_media->media_author ) );
		} else {
			$link = $site_url . 'author/' . $author_name . '/';
		}
	} else {
		if ( $rtmedia_interaction && isset( $rtmedia_interaction->context ) && $rtmedia_interaction->context->type == 'group' ){
			if ( function_exists( "bp_get_current_group_slug" ) ){
				$link .= $site_url . bp_get_groups_root_slug() . '/' . bp_get_current_group_slug() . '/';
			}
		} else {
			//$post = get_post ( $rtmedia_media->post_parent );
			$post = get_post( get_post_field( "post_parent", $rtmedia_media->media_id ) );

			$link .= $site_url . $post->post_name . '/';
		}
	}

	$link .= RTMEDIA_MEDIA_SLUG . '/';

	if ( isset ( $rtmedia_query->action_query->media_type ) ){
		if ( in_array( $rtmedia_query->action_query->media_type, array( "photo", "music", "video", "album", "playlist" ) ) ){
			$link .= $rtmedia_query->action_query->media_type . '/';
		}
	}

	return $link . $page_url;
}

function rtmedia_pagination_next_link() {

	global $rtmedia_media, $rtmedia_interaction, $rtmedia_query;

	$page_url    = 'pg/' . ( rtmedia_page() + 1 );
	$site_url    = ( is_multisite() ) ? trailingslashit( get_site_url( get_current_blog_id() ) ) : trailingslashit( get_site_url() );
	$author_name = get_query_var( 'author_name' );
	$link        = '';

	if ( $rtmedia_interaction && isset( $rtmedia_interaction->context ) && $rtmedia_interaction->context->type == "profile" ){
		if ( function_exists( "bp_core_get_user_domain" ) ){
			$link .= trailingslashit( bp_core_get_user_domain( $rtmedia_media->media_author ) );
		} else {
			$link .= $site_url . 'author/' . $author_name . '/';
		}
	} else {
		if ( $rtmedia_interaction && isset( $rtmedia_interaction->context ) && $rtmedia_interaction->context->type == 'group' ){
			if ( function_exists( "bp_get_current_group_slug" ) ){
				$link .= $site_url . bp_get_groups_root_slug() . '/' . bp_get_current_group_slug() . '/';
			}
		} else {
			//$post = get_post ( $rtmedia_media->post_parent );
			$post = get_post( get_post_field( "post_parent", $rtmedia_media->media_id ) );

			$link .= $site_url . $post->post_name . '/';
		}
	}
	$link .= RTMEDIA_MEDIA_SLUG . '/';
	if ( isset ( $rtmedia_query->media_query[ "album_id" ] ) && intval( $rtmedia_query->media_query[ "album_id" ] ) > 0 ){
		$link .= $rtmedia_query->media_query[ "album_id" ] . "/";
	}
	if ( isset ( $rtmedia_query->action_query->media_type ) ){
		if ( in_array( $rtmedia_query->action_query->media_type, array( "photo", "music", "video", "album", "playlist" ) ) ){
			$link .= $rtmedia_query->action_query->media_type . '/';
		}
	}

	return $link . $page_url;
}

function rtmedia_comments_enabled() {
	global $rtmedia;

	return $rtmedia->options[ 'general_enableComments' ]; // && is_user_logged_in ();
}

/**
 *
 * @return boolean
 */
function is_rtmedia_gallery() {
	global $rtmedia_query;
	if ( $rtmedia_query ){
		return $rtmedia_query->is_gallery();
	} else {
		return false;
	}
}

/**
 *
 * @return boolean
 */
function is_rtmedia_album_gallery() {
	global $rtmedia_query;
	if ( $rtmedia_query ){
		return $rtmedia_query->is_album_gallery();
	} else {
		return false;
	}
}

/**
 *
 * @return boolean
 */
function is_rtmedia_single() {
	global $rtmedia_query;
	if ( $rtmedia_query ){
		return $rtmedia_query->is_single();
	} else {
		return false;
	}
}

/**
 *
 * @return boolean
 */
function is_rtmedia_album( $album_id = false ) {
	if ( $album_id ){
		$rtmedia_model = new RTMediaModel();
		$media         = $rtmedia_model->get( array( "id" => $album_id ) );
		if ( is_array( $media ) && isset( $media[ 0 ] ) && isset( $media[ 0 ]->media_type ) && $media[ 0 ]->media_type == "album" ){
			return true;
		}

		return false;
	}
	global $rtmedia_query;
	if ( $rtmedia_query ){
		return $rtmedia_query->is_album();
	} else {
		return false;
	}
}


function is_rtmedia_group_album() {
	global $rtmedia_query;
	if ( $rtmedia_query ){
		return $rtmedia_query->is_group_album();
	} else {
		return false;
	}
}

/**
 *
 * @return boolean
 */
function is_rtmedia_edit_allowed() {
	global $rtmedia_query;
	if ( $rtmedia_query ){
		if ( isset ( $rtmedia_query->media_query[ 'media_author' ] ) && get_current_user_id() == $rtmedia_query->media_query[ 'media_author' ] && $rtmedia_query->action_query->action == 'edit' ){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

//add_action ( 'rtmedia_add_edit_fields', 'rtmedia_vedio_editor', 1000 );
add_action( 'rtmedia_after_update_media', 'set_video_thumbnail', 12 );
add_filter( 'rtmedia_single_content_filter', 'change_poster', 99, 2 );

function change_poster( $html, $media ) {
	global $rtmedia_media;
	if ( $rtmedia_media->media_type == 'video' ){
		$thumbnail_id = $rtmedia_media->cover_art;
		if ( $thumbnail_id ){
			if ( is_numeric( $thumbnail_id ) ){
				$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, 'full' );
				$html           = str_replace( '<video ', '<video poster="' . $thumbnail_info[ 0 ] . '" ', $html );
			} else {
				$html = str_replace( '<video ', '<video poster="' . $thumbnail_id . '" ', $html );
			}
		}
	}

	return $html;
}

// add title for video editor in tabs
add_action( 'rtmedia_add_edit_tab_title', 'rtmedia_vedio_editor_title', 1000 );
function rtmedia_vedio_editor_title() {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->media[ 0 ]->media_type ) && $rtmedia_query->media[ 0 ]->media_type == 'video' ){
		$flag            = false;
		$media_id        = $rtmedia_query->media[ 0 ]->media_id;
		$thumbnail_array = get_post_meta( $media_id, "rtmedia_media_thumbnails", true );
		if ( is_array( $thumbnail_array ) ){
			$flag = true;
		} else {
			global $rtmedia_media;
			$curr_cover_art = $rtmedia_media->cover_art;
			if ( $curr_cover_art != "" ){
				$rtmedia_video_thumbs = get_rtmedia_meta( $rtmedia_query->media[ 0 ]->media_id, "rtmedia-thumbnail-ids" );
				if ( is_array( $rtmedia_video_thumbs ) ){
					$flag = true;
				}
			}
		}
		if ( $flag ){
			echo '<dd><a href="#panel2"><i class="rtmicon-picture-o"></i>' . __( 'Video Thumbnail', 'rtmedia' ) . '</a></dd>';
		}
	}
}

add_action( 'rtmedia_add_edit_tab_content', 'rtmedia_vedio_editor_content', 1000 );

function rtmedia_vedio_editor_content() {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->media[ 0 ]->media_type ) && $rtmedia_query->media[ 0 ]->media_type == 'video' ){
		$media_id        = $rtmedia_query->media[ 0 ]->media_id;
		$thumbnail_array = get_post_meta( $media_id, "rtmedia_media_thumbnails", true );
		echo '<div class="content" id="panel2">';
		if ( is_array( $thumbnail_array ) ){
			?>

			<div class="rtmedia-change-cover-arts">
				<ul>
					<?php
					foreach ( $thumbnail_array as $key => $thumbnail_src ) {
						?>
						<li<?php echo checked( $thumbnail_src, $rtmedia_query->media[ 0 ]->cover_art, false ) ? ' class="selected"' : ''; ?>
							style="width: 150px;display: inline-block;">
							<label
								for="rtmedia-upload-select-thumbnail-<?php echo intval( sanitize_text_field( $key ) ) + 1; ?>"
								class="alignleft">
								<input
									type="radio"<?php checked( $thumbnail_src, $rtmedia_query->media[ 0 ]->cover_art ); ?>
									id="rtmedia-upload-select-thumbnail-<?php echo intval( sanitize_text_field( $key ) ) + 1; ?>"
									value="<?php echo $thumbnail_src; ?>" name="rtmedia-thumbnail"/>
								<img src="<?php echo $thumbnail_src; ?>" style="max-height: 120px;max-width: 120px"/>
							</label>
						</li>
					<?php
					}
					?>
				</ul>
			</div>


		<?php
		} else { // check for array of thumbs stored as attachement ids
			global $rtmedia_media;
			$curr_cover_art = $rtmedia_media->cover_art;
			if ( $curr_cover_art != "" ){
				$rtmedia_video_thumbs = get_rtmedia_meta( $rtmedia_query->media[ 0 ]->media_id, "rtmedia-thumbnail-ids" );
				if ( is_array( $rtmedia_video_thumbs ) ){
					?>
					<div class="rtmedia-change-cover-arts">
						<p><?php _e( 'Video Thumbnail:', 'rtmedia' ); ?></p>
						<ul>
							<?php
							foreach ( $rtmedia_video_thumbs as $key => $attachment_id ) {
								$thumbnail_src = wp_get_attachment_url( $attachment_id );
								?>
								<li<?php echo checked( $attachment_id, $curr_cover_art, false ) ? ' class="selected"' : ''; ?>
									style="width: 150px;display: inline-block;">
									<label
										for="rtmedia-upload-select-thumbnail-<?php echo intval( sanitize_text_field( $key ) ) + 1; ?>"
										class="alignleft">
										<input type="radio"<?php checked( $attachment_id, $curr_cover_art ); ?>
											   id="rtmedia-upload-select-thumbnail-<?php echo intval( sanitize_text_field( $key ) ) + 1; ?>"
											   value="<?php echo sanitize_text_field( $attachment_id ); ?>"
											   name="rtmedia-thumbnail"/>
										<img src="<?php echo sanitize_text_field( $thumbnail_src ); ?>"
											 style="max-height: 120px;max-width: 120px"/>
									</label>
								</li>
							<?php
							}
							?>
						</ul>
					</div>

				<?php

				}

			}

		}
		echo "</div>";
	}
}

function update_activity_after_thumb_set( $id ) {
	$model                 = new RTMediaModel();
	$mediaObj              = new RTMediaMedia();
	$media                 = $model->get( array( 'id' => $id ) );
	$privacy               = $media[ 0 ]->privacy;
	$activity_id           = rtmedia_activity_id( $id );
	$same_medias           = $mediaObj->model->get( array( 'activity_id' => $activity_id ) );
	$update_activity_media = Array();
	foreach ( $same_medias as $a_media ) {
		$update_activity_media[ ] = $a_media->id;
	}
	$objActivity = new RTMediaActivity ( $update_activity_media, $privacy, false );
	global $wpdb, $bp;
	$activity_old_content = bp_activity_get_meta( $activity_id, "bp_old_activity_content" );
	$activity_text        = bp_activity_get_meta( $activity_id, "bp_activity_text" );
	if ( $activity_old_content == "" ){
		// get old activity content and save in activity meta
		$activity_get  = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );
		$activity      = $activity_get[ 'activities' ][ 0 ];
		$activity_body = $activity->content;
		bp_activity_update_meta( $activity_id, "bp_old_activity_content", $activity_body );
		//extract activity text from old content
		$activity_text = strip_tags( $activity_body, '<span>' );
		$activity_text = explode( "</span>", $activity_text );
		$activity_text = strip_tags( $activity_text[ 0 ] );
		bp_activity_update_meta( $activity_id, "bp_activity_text", $activity_text );
	}
	$activity_text              = bp_activity_get_meta( $activity_id, "bp_activity_text" );
	$objActivity->activity_text = $activity_text;
	$wpdb->update( $bp->activity->table_name, array( "type" => "rtmedia_update", "content" => $objActivity->create_activity_html() ), array( "id" => $activity_id ) );
}

function set_video_thumbnail( $id ) {
	$media_type = rtmedia_type( $id );
	if ( 'video' == $media_type && isset( $_POST[ 'rtmedia-thumbnail' ] ) ){
		$model = new RTMediaModel();
		$model->update( array( 'cover_art' => $_POST[ 'rtmedia-thumbnail' ] ), array( 'id' => $id ) );
		update_activity_after_thumb_set( $id );
		// code to update activity

	}
}

add_action( 'rtmedia_add_edit_tab_title', 'rtmedia_image_editor_title', 12, 1 );
//add the tab title media on media edit screen
function rtmedia_image_editor_title( $type = 'photo' ) {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->media[ 0 ]->media_type ) && $rtmedia_query->media[ 0 ]->media_type == 'photo' && $type == 'photo' ){
		echo '<dd><a href="#panel2" class="rtmedia-modify-image"><i class="rtmicon-picture-o"></i>' . __( "Image", "rtmedia" ) . '</a></dd>';
	}
}

// add the content for the image editor tab
add_action( 'rtmedia_add_edit_tab_content', 'rtmedia_image_editor_content', 12, 1 );
function rtmedia_image_editor_content( $type = 'photo' ) {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->media[ 0 ]->media_type ) && $rtmedia_query->media[ 0 ]->media_type == 'photo' && $type == 'photo' ){
		$media_id = $rtmedia_query->media[ 0 ]->media_id;
		$id       = $rtmedia_query->media[ 0 ]->id;
		//$editor = wp_get_image_editor(get_attached_file($id));
		$modify_button = $nonce = "";
		if ( current_user_can( 'edit_posts' ) ){
			include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
			$nonce         = wp_create_nonce( "image_editor-$media_id" );
			$modify_button = '<p><input type="button" class="rtmedia-image-edit" id="imgedit-open-btn-' . $media_id . '" onclick="imageEdit.open( \'' . $media_id . '\', \'' . $nonce . '\' )" class="button" value="' . __( 'Modify Image', 'rtmedia' ) . '"> <span class="spinner"></span></p>';
		}
		echo '<div class="content" id="panel2">';
		//<div class="tab-content" data-section-content>';
		echo '<div class="rtmedia-image-editor-cotnainer" id="rtmedia-image-editor-cotnainer" >';
		echo '<div class="rtmedia-image-editor" id="image-editor-' . $media_id . '"></div>';
		$thumb_url = wp_get_attachment_image_src( $media_id, 'thumbnail', true );

		echo '<div id="imgedit-response-' . $media_id . '"></div>';
		echo '<div class="wp_attachment_image" id="media-head-' . $media_id . '">' . '<p id="thumbnail-head-' . $id . '"><img class="thumbnail" src="' . set_url_scheme( $thumb_url[ 0 ] ) . '" alt="" /></p>' . $modify_button . '</div>';
		echo '</div>';
		echo '</div>';
	}

}

// provide dropdown to user to change the album of the media in media edit screen.
add_action( 'rtmedia_add_edit_fields', 'rtmedia_add_album_selection_field', 14, 1 );
function rtmedia_add_album_selection_field( $media_type ) {

	if ( is_rtmedia_album_enable() && isset( $media_type ) && $media_type != 'album' && $media_type != 'playlist' ){

		global $rtmedia_query;
		$album_list    = '';
		$curr_album_id = '';
		if ( isset ( $rtmedia_query->media[ 0 ] ) && isset ( $rtmedia_query->media[ 0 ]->album_id ) && $rtmedia_query->media[ 0 ]->album_id != '' ){
			$curr_album_id = $rtmedia_query->media[ 0 ]->album_id;
		}
		?>
		<div class="rtmedia-edit-change-album">
			<label for=""><?php _e( 'Album', 'rtmedia' ); ?> : </label>
			<?php if ( isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == 'group' ){
				//show group album list.
				$album_list = rtmedia_group_album_list( $selected_album_id = $curr_album_id );

			} else {
				//show profile album list
				$album_list = rtmedia_user_album_list( $get_all = false, $selected_album_id = $curr_album_id );
			}
			echo '<select name="album_id" class="rtmedia-merge-user-album-list">' . $album_list . '</select>';
			?>
		</div>
	<?php
	}
}

function update_video_poster( $html, $media, $activity = false ) {
	if ( $media->media_type == 'video' ){
		$thumbnail_id = $media->cover_art;
		if ( $thumbnail_id ){
			$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			$html           = str_replace( '<video ', '<video poster="' . $thumbnail_info[ 0 ] . '" ', $html );
		}
	}

	return $html;
}

function get_video_without_thumbs() {
	$rtmedia_model = new RTMediaModel();
	$sql           = "select media_id from {$rtmedia_model->table_name} where media_type = 'video' and blog_id = '" . get_current_blog_id() . "' and cover_art is null";
	global $wpdb;
	$results = $wpdb->get_col( $sql );

	return $results;
}


function rtmedia_comment_form() {
	if ( is_user_logged_in() ){
		?>
		<form method="post" id="rt_media_comment_form" class="rt_media_comment_form"
			  action="<?php echo esc_url( get_rtmedia_permalink( rtmedia_id() ) ); ?>comment/">
			<div class="row">
				<div class="large-12 columns">
					<textarea style="width:100%" placeholder="<?php _e( 'Type Comment...', 'rtmedia' ); ?>"
							  name="comment_content" id="comment_content"></textarea>
				</div>
			</div>
			<input type="submit" id="rt_media_comment_submit" class="rt_media_comment_submit"
				   value="<?php _e( 'Comment', 'rtmedia' ); ?>">
			<?php RTMediaComment::comment_nonce_generator(); ?>
		</form>
	<?php
	}
}

function rtmedia_get_cover_art_src( $id ) {
	$model     = new RTMediaModel();
	$media     = $model->get( array( "id" => $id ) );
	$cover_art = $media[ 0 ]->cover_art;
	if ( $cover_art != "" ){
		if ( is_numeric( $cover_art ) ){
			$thumbnail_info = wp_get_attachment_image_src( $cover_art, 'full' );

			return $thumbnail_info[ 0 ];
		} else {
			return $cover_art;
		}
	} else {
		return false;
	}

}

function rtmedia_delete_form( $echo = true ) {

	if ( rtmedia_delete_allowed() ){

		$html = '<form method="post" action="' . get_rtmedia_permalink( rtmedia_id() ) . 'delete/">';
		$html .= '<input type="hidden" name="id" id="id" value="' . rtmedia_id() . '">';
		$html .= '<input type="hidden" name="request_action" id="request_action" value="delete">';
		if ( $echo ){
			echo $html;
			RTMediaMedia::media_nonce_generator( rtmedia_id(), true );
			do_action( "rtmedia_media_single_delete_form" );
			echo '<button type="submit" title="' . __( 'Delete Media', 'rtmedia' ) . '" class="rtmedia-delete-media rtmedia-action-buttons button"><i class="rtmicon-trash-o"></i>' . __( 'Delete', 'rtmedia' ) . '</button></form>';
		} else {
			$output          = $html;
			$rtm_nonce       = RTMediaMedia::media_nonce_generator( rtmedia_id(), false );
			$rtm_nonce       = json_decode( $rtm_nonce );
			$rtm_nonce_field = wp_nonce_field( 'rtmedia_' . rtmedia_id(), $rtm_nonce->action, true, false );
			do_action( "rtmedia_media_single_delete_form" );
			$output .= $rtm_nonce_field . '<button type="submit" title="' . __( 'Delete Media', 'rtmedia' ) . '" class="rtmedia-delete-media rtmedia-action-buttons button"><i class="rtmicon-trash-o"></i>' . __( 'Delete', 'rtmedia' ) . '</button></form>';

			return $output;
		}
	}

	return false;

}

/**
 *
 * @param type $attr
 */
function rtmedia_uploader( $attr = '' ) {
	$allow_upload = apply_filters( 'rtmedia_allow_uploader_view', true, 'media_gallery' );
	if ( $allow_upload ){
		if ( function_exists( 'bp_is_blog_page' ) && ! bp_is_blog_page() ){
			if ( function_exists( 'bp_is_user' ) && bp_is_user() && function_exists( 'bp_displayed_user_id' ) && bp_displayed_user_id() == get_current_user_id() ){
				echo RTMediaUploadShortcode::pre_render( $attr );
			} else {
				if ( function_exists( 'bp_is_group' ) && bp_is_group() ){
					if ( can_user_upload_in_group() ){
						echo RTMediaUploadShortcode::pre_render( $attr );
					}
				}
			}
		}
	} else {
		echo "<div class='rtmedia-upload-not-allowed'>" . apply_filters( 'rtmedia_upload_not_allowed_message', __( 'You are not allowed to upload/attach media.', 'rtmedia' ), 'media_gallery' ) . "</div>";
	}
}

function rtmedia_gallery( $attr = '' ) {
	echo RTMediaGalleryShortcode::render( $attr );
}

function get_rtmedia_meta( $id = false, $key = false ) {
	$rtmediameta = new RTMediaMeta();

	return $rtmediameta->get_meta( $id, $key );
}

function add_rtmedia_meta( $id = false, $key = false, $value = false, $duplicate = false ) {
	$rtmediameta = new RTMediaMeta ( $id, $key, $value, $duplicate );

	return $rtmediameta->add_meta( $id, $key, $value, $duplicate );
}

function update_rtmedia_meta( $id = false, $key = false, $value = false, $duplicate = false ) {
	$rtmediameta = new RTMediaMeta();

	return $rtmediameta->update_meta( $id, $key, $value, $duplicate );
}

function delete_rtmedia_meta( $id = false, $key = false ) {
	$rtmediameta = new RTMediaMeta();

	return $rtmediameta->delete_meta( $id, $key );
}

function rtmedia_global_albums() {
	return RTMediaAlbum::get_globals(); //get_site_option('rtmedia-global-albums');
}

function rtmedia_global_album_list( $selected_album_id = false ) {
	global $rtmedia_query;
	$model         = new RTMediaModel();
	$global_albums = rtmedia_global_albums();
	if ( ! empty ( $global_albums ) ){
		if ( is_array( $global_albums ) ){
			$albums = implode( ',', $global_albums );
		} else {
			//return;
		}
		//return;
	}
	$option = null;

	$album_objects = $model->get_media( array( 'id' => ( $global_albums ) ), false, false );

	if ( $album_objects ){
		foreach ( $album_objects as $album ) {
			//if selected_album_id is provided, keep that album_id selected by default
			$selected = '';
			if ( $selected_album_id != false && $selected_album_id != '' && $selected_album_id == $album->id ){
				$selected = 'selected="selected"';
			}

			if ( ( isset ( $rtmedia_query->media_query[ 'album_id' ] ) && ( $album_objects[ 0 ]->id != $rtmedia_query->media_query[ 'album_id' ] ) ) || ! isset ( $rtmedia_query->media_query[ 'album_id' ] ) ){
				$option .= '<option value="' . $album->id . '" ' . $selected . '>' . $album->media_title . '</option>';
			}
		}
	}


	return $option;
}

function rtmedia_user_album_list( $get_all = false, $selected_album_id = false ) {
	global $rtmedia_query;
	$model         = new RTMediaModel();
	$global_option = rtmedia_global_album_list( $selected_album_id );
	$global_albums = rtmedia_global_albums();

	$global_album   = rtmedia_get_site_option( 'rtmedia-global-albums' );
	$album_objects  = $model->get_media( array( 'media_author' => get_current_user_id(), 'media_type' => 'album' ), false, 'context' );
	$option_group   = "";
	$profile_option = "";
	if ( $album_objects ){
		foreach ( $album_objects as $album ) {
			if ( ! in_array( $album->id, $global_albums ) && ( ( isset ( $rtmedia_query->media_query[ 'album_id' ] ) && ( $album->id != $rtmedia_query->media_query[ 'album_id' ] || $get_all ) ) || ! isset ( $rtmedia_query->media_query[ 'album_id' ] ) )
			){
				$selected = '';
				if ( $selected_album_id != false && $selected_album_id != '' && $album->id == $selected_album_id ){
					//if an album_id is specified to be shown as selected, select that album_id by default
					$selected = 'selected="selected"';
				}
				if ( $album->context == 'profile' ){

					$profile_option .= '<option value="' . $album->id . '" ' . $selected . '>' . $album->media_title . '</option>';
				}
				//                else
				//                    $option_group .= '<option value="' . $album->id . '">' . $album->media_title . '</option>';
				//commented out group album section from album dropdown as user will be able to upload to profile albums from profile
				// and group albums from group

			}

		}
	}
	$option = "$global_option";
	if ( $profile_option != "" ){
		$option .= "<optgroup label='" . __( "Profile Albums", "rtmedia" ) . " ' value = 'profile'>$profile_option</optgroup>";
	}
	if ( $option_group != "" && class_exists( 'BuddyPress' ) ){
		$option .= "<optgroup label='" . __( "Group Albums", "rtmedia" ) . "' value = 'group'>$option_group</optgroup>";
	}
	if ( $option ){
		return $option;
	} else {
		return false;
	}
}

function rtmedia_group_album_list( $selected_album_id = false ) { //by default, first album in list will be selected
	global $rtmedia_query;
	$model = new RTMediaModel();

	$global_option = rtmedia_global_album_list( $selected_album_id );
	$global_albums = rtmedia_global_albums();

	$album_objects = $model->get_media( array(
		'context' => $rtmedia_query->media_query[ 'context' ], 'context_id' => $rtmedia_query->media_query[ 'context_id' ], 'media_type' => 'album'
	), false, false );
	$option_group  = "";
	if ( $album_objects ){
		foreach ( $album_objects as $album ) {
			$selected = '';
			if ( $selected_album_id != false && $selected_album_id != '' && $selected_album_id == $album->id ){
				$selected = 'selected="selected"';
			}

			if ( ! in_array( $album->id, $global_albums ) && ( ( isset ( $rtmedia_query->media_query[ 'album_id' ] ) && ( $album->id != $rtmedia_query->media_query[ 'album_id' ] ) ) || ! isset ( $rtmedia_query->media_query[ 'album_id' ] ) ) ){
				$option_group .= '<option value="' . $album->id . '" ' . $selected . '>' . $album->media_title . '</option>';
			}

		}
	}
	$option = $global_option;
	if ( $option_group != "" ){
		$option .= "<optgroup label='" . __( "Group Albums", "rtmedia" ) . "' value = 'group'>$option_group</optgroup>";
	}
	if ( $option ){
		return $option;
	} else {
		return false;
	}
}

add_action( 'rtmedia_media_gallery_actions', 'rtmedia_gallery_options', 80 );
add_action( 'rtmedia_album_gallery_actions', 'rtmedia_gallery_options', 80 );
function rtmedia_gallery_options() {

	$options_start = $options_end = $option_buttons = $output = "";
	$options       = array();
	$options       = apply_filters( 'rtmedia_gallery_actions', $options );
	if ( ! empty( $options ) ){

		$options_start = '<span class="click-nav" id="rtm-media-options-list">
                <span class="no-js">
                <span class="clicker rtmedia-action-buttons"><i class="rtmicon-cog"></i>' . __( 'Options', 'rtmedia' ) . '</span>
                <ul class="rtm-options">';
		foreach ( $options as $action ) {
			if ( $action != "" ){
				$option_buttons .= "<li>" . $action . "</li>";
			}
		}

		$options_end = "</ul></span></span>";

		if ( $option_buttons != "" ){
			$output = $options_start . $option_buttons . $options_end;
		}

		if ( $output != "" ){
			echo $output;
		}
	}
}

add_filter( 'rtmedia_gallery_actions', 'rtmedia_create_album', 12 );

//add_filter ( 'rtmedia_gallery_actions', 'rtmedia_create_album' );

function rtmedia_create_album( $options ) {
	if ( ! is_rtmedia_album_enable() ){
		return;
	}
	$return = true;
	$return = apply_filters( "rtm_is_album_create_enable", $return );
	if ( ! $return ){
		return;
	}
	global $rtmedia_query;
	$user_id = get_current_user_id();
	$display = false;
	if ( isset ( $rtmedia_query->query[ 'context' ] ) && in_array( $rtmedia_query->query[ 'context' ], array( 'profile', 'group' ) ) && $user_id != 0 ){
		switch ( $rtmedia_query->query[ 'context' ] ) {
			case 'profile':
				if ( $rtmedia_query->query[ 'context_id' ] == $user_id ){
					$display = true;
					$display = apply_filters( "rtm_display_create_album_button", $display, $user_id );
				}
				break;
			case 'group':
				$group_id = $rtmedia_query->query[ 'context_id' ];
				if ( can_user_create_album_in_group() ){
					$display = true;
				}
				break;
		}
	}
	if ( $display === true ){

		add_action( 'rtmedia_before_media_gallery', 'rtmedia_create_album_modal' );
		$options[ ] = "<a href='#rtmedia-create-album-modal' class='rtmedia-reveal-modal rtmedia-modal-link'  title='" . __( 'Create New Album', 'rtmedia' ) . "'><i class='rtmicon-plus-circle'></i>" . __( 'Add Album', 'rtmedia' ) . "</a>";

		return $options;

	}
}

add_action( 'rtmedia_before_media_gallery', 'rtmedia_create_album_modal' );
add_action( 'rtmedia_before_album_gallery', 'rtmedia_create_album_modal' );
function rtmedia_create_album_modal() {
	global $rtmedia_query;
	if ( is_rtmedia_album_enable() && ! ( isset( $rtmedia_query->is_gallery_shortcode ) && $rtmedia_query->is_gallery_shortcode == true ) && isset( $rtmedia_query->query[ 'context_id' ] ) && isset( $rtmedia_query->query[ 'context' ] ) ){
		?>
		<div class="mfp-hide rtmedia-popup" id="rtmedia-create-album-modal">
			<div id="rtm-modal-container">
				<?php do_action( "rtmedia_before_create_album_modal" ); ?>
				<h2 class="rtm-modal-title"><?php _e( 'Create New Album', 'rtmedia' ); ?></h2>

				<p>
					<label for="rtmedia_album_name"><?php _e( 'Album Title : ', 'rtmedia' ); ?></label>
					<input type="text" id="rtmedia_album_name" value="" class="rtm-input-medium"/>
					<input type="hidden" id="rtmedia_album_context"
						   value="<?php echo $rtmedia_query->query[ 'context' ]; ?>">
					<input type="hidden" id="rtmedia_album_context_id"
						   value="<?php echo $rtmedia_query->query[ 'context_id' ]; ?>">
					<button type="button"
							id="rtmedia_create_new_album"><?php _e( "Create Album", "rtmedia" ); ?></button>
				</p>
				<?php do_action( "rtmedia_add_album_privacy" ); ?>
				<?php do_action( "rtmedia_after_create_album_modal" ); ?>
			</div>
		</div>

	<?php
	}

}

add_action( 'rtmedia_before_media_gallery', 'rtmedia_merge_album_modal' );
add_action( 'rtmedia_before_album_gallery', 'rtmedia_merge_album_modal' );
function rtmedia_merge_album_modal() {

	if ( ! is_rtmedia_album() || ! is_user_logged_in() ){
		return;
	}
	if ( ! is_rtmedia_album_enable() ){
		return;
	}
	global $rtmedia_query;

	if ( is_rtmedia_group_album() ){
		$album_list = rtmedia_group_album_list();
	} else {
		$album_list = rtmedia_user_album_list();
	}
	if ( $album_list && isset( $rtmedia_query->media_query[ 'album_id' ] ) && $rtmedia_query->media_query[ 'album_id' ] != '' ){

		?>
		<div class="rtmedia-merge-container rtmedia-popup mfp-hide" id="rtmedia-merge">
			<div id="rtm-modal-container">
				<h2 class="rtm-modal-title"><?php _e( 'Merge Album', 'rtmedia' ); ?></h2>

				<form method="post" class="album-merge-form" action="merge/">
					<?php _e( 'Select Album to merge with : ', 'rtmedia' ); ?>
					<?php echo '<select name="album" class="rtmedia-merge-user-album-list">' . $album_list . '</select>'; ?>
					<?php wp_nonce_field( 'rtmedia_merge_album_' . $rtmedia_query->media_query[ 'album_id' ], 'rtmedia_merge_album_nonce' ); ?>
					<input type="submit" class="rtmedia-merge-selected" name="merge-album"
						   value="<?php _e( 'Merge Album', 'rtmedia' ); ?>"/>
				</form>
			</div>
		</div>

	<?php
	}

}

function rtmedia_is_album_editable() {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == "profile" ){
		if ( isset ( $rtmedia_query->media_query[ 'media_author' ] ) && get_current_user_id() == $rtmedia_query->media_query[ 'media_author' ] ){
			return true;
		}
	}
	if ( isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == "group" ){
		if ( isset ( $rtmedia_query->album[ 0 ]->media_author ) && get_current_user_id() == $rtmedia_query->album[ 0 ]->media_author ){
			return true;
		}
	}

	return false;
}

add_filter( 'rtmedia_gallery_actions', 'rtmedia_album_edit', 11 );

function rtmedia_album_edit( $options ) {

	if ( ! is_rtmedia_album() || ! is_user_logged_in() ){
		return;
	}
	if ( ! is_rtmedia_album_enable() ){
		return;
	}
	global $rtmedia_query;

	?>

	<?php
	if ( isset ( $rtmedia_query->media_query ) && isset( $rtmedia_query->media_query[ 'album_id' ] ) && ! in_array( $rtmedia_query->media_query[ 'album_id' ], rtmedia_get_site_option( 'rtmedia-global-albums' ) ) ){
		//if ( isset ( $rtmedia_query->media_query[ 'media_author' ] ) && get_current_user_id () == $rtmedia_query->media_query[ 'media_author' ] ) {
		if ( rtmedia_is_album_editable() || is_rt_admin() ){
			$options[ ] = "<a href='edit/' class='rtmedia-edit' title='" . __( 'Edit Album', 'rtmedia' ) . "' ><i class='rtmicon-edit'></i>" . __( 'Edit Album', 'rtmedia' ) . "</a>";
			$options[ ] = '<form method="post" class="album-delete-form rtmedia-inline" action="delete/">' . wp_nonce_field( 'rtmedia_delete_album_' . $rtmedia_query->media_query[ 'album_id' ], 'rtmedia_delete_album_nonce' ) . '<button type="submit" name="album-delete" class="icon-button rtmedia-delete-album" title="' . __( 'Delete Album', 'rtmedia' ) . '"><i class="rtmicon-trash-o"></i>' . __( 'Delete Album', 'rtmedia' ) . '</button></form>';

			if ( is_rtmedia_group_album() ){
				$album_list = rtmedia_group_album_list();
			} else {
				$album_list = rtmedia_user_album_list();
			}
			if ( $album_list ){

				$options[ ] = '<span><a href="#rtmedia-merge" class="rtmedia-reveal-modal rtmedia-modal-link" title="' . __( 'Merge Album', 'rtmedia' ) . '"><i class="rtmicon-code-fork"></i>' . __( 'Merge Album', 'rtmedia' ) . '</a></span>';

			}
		}
	}

	return $options;
}

add_action( 'rtmedia_before_item', 'rtmedia_item_select' );

function rtmedia_item_select() {
	global $rtmedia_query, $rtmedia_backbone;
	if ( $rtmedia_backbone[ 'backbone' ] ){
		if ( $rtmedia_backbone[ 'is_album' ] && $rtmedia_backbone[ 'is_edit_allowed' ] ){
			echo '<input type="checkbox" name="move[]" class="rtmedia-item-selector" value="<%= id %>" />';
		}
	} else {
		if ( is_rtmedia_album() && isset ( $rtmedia_query->media_query ) && $rtmedia_query->action_query->action == 'edit' ){
			if ( isset ( $rtmedia_query->media_query[ 'media_author' ] ) && get_current_user_id() == $rtmedia_query->media_query[ 'media_author' ] ){
				echo '<input type="checkbox" class="rtmedia-item-selector" name="selected[]" value="' . rtmedia_id() . '" />';
			}
		}
	}
}

add_action( 'rtmedia_query_actions', 'rtmedia_album_merge_action' );

function rtmedia_album_merge_action( $actions ) {
	$actions[ 'merge' ] = __( 'Merge', 'rtmedia' );

	return $actions;
}

function rtmedia_sub_nav() {
	global $rtMediaNav;
	$rtMediaNav = new RTMediaNav();
	$rtMediaNav->sub_nav();
}

function is_rtmedia_album_enable() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "general_enableAlbums" ] ) && $rtmedia->options[ "general_enableAlbums" ] != "0" ){
		return true;
	}

	return false;
}

function rtmedia_load_template() {
	do_action( "rtmedia_before_template_load" );
	include( RTMediaTemplate::locate_template() );
	do_action( "rtmedia_after_template_load" );
}

function is_rtmedia_privacy_enable() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "privacy_enabled" ] ) && $rtmedia->options[ "privacy_enabled" ] != "0" ){
		return true;
	}

	return false;
}

function is_rtmedia_privacy_user_overide() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "privacy_userOverride" ] ) && $rtmedia->options[ "privacy_userOverride" ] != "0" ){
		return true;
	}

	return false;
}

function rtmedia_edit_media_privacy_ui() {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == 'group' ){
		//if context is group i.e editing a group media, dont show the privacy dropdown
		return false;
	}
	$privacymodel = new RTMediaPrivacy();
	$privacy      = $privacymodel->select_privacy_ui( $echo = false );
	if ( $privacy ){
		return "<div class='rtmedia-edit-privacy'><label for='privacy'>" . __( 'Privacy : ', 'rtmedia' ) . "</label>" . $privacy . "</div>";
	}
}

function get_rtmedia_default_privacy() {

	global $rtmedia;
	if ( isset ( $rtmedia->options[ "privacy_default" ] ) ){
		return $rtmedia->options[ "privacy_default" ];
	}

	return 0;
}

function is_rtmedia_group_media_enable() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "buddypress_enableOnGroup" ] ) && $rtmedia->options[ "buddypress_enableOnGroup" ] != "0" ){
		return true;
	}

	return false;
}

// check if media is enabled in profile
function is_rtmedia_profile_media_enable() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "buddypress_enableOnProfile" ] ) && $rtmedia->options[ "buddypress_enableOnProfile" ] != "0" ){
		return true;
	}

	return false;
}

//function to check if user is on bp group
function is_rtmedia_bp_group() {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == 'group' ){
		return true;
	}

	return false;
}

//function to check if user is on bp group
function is_rtmedia_bp_profile() {
	global $rtmedia_query;
	if ( isset( $rtmedia_query->query[ 'context' ] ) && $rtmedia_query->query[ 'context' ] == 'profile' ){
		return true;
	}

	return false;
}

function can_user_upload_in_group() {
	$group        = groups_get_current_group();
	$upload_level = groups_get_groupmeta( $group->id, "rt_upload_media_control_level" );
	$user_id      = get_current_user_id();
	$display_flag = false;
	if ( groups_is_user_member( $user_id, $group->id ) ){
		//        if ($upload_level == "admin") {
		//            if (groups_is_user_admin($user_id, $group->id)) {
		//                $display_flag = true;
		//            }
		//        } else if ($upload_level == "moderator") {
		//            if (groups_is_user_mod($user_id, $group->id)) {
		//                $display_flag = true;
		//            }
		//        } else {
		//            $display_flag = true;
		//        }
		$display_flag = true;
	}
	$display_flag = apply_filters( 'rtm_can_user_upload_in_group', $display_flag );

	return $display_flag;
}

/**
 *
 * @param type $group_id
 * @param type $user_id
 *
 * @return boolean
 */
function can_user_create_album_in_group( $group_id = false, $user_id = false ) {
	if ( $group_id == false ){
		$group    = groups_get_current_group();
		$group_id = $group->id;
	}
	$upload_level = groups_get_groupmeta( $group_id, "rt_media_group_control_level" );
	if ( empty ( $upload_level ) ){
		$upload_level = groups_get_groupmeta( $group_id, "bp_media_group_control_level" );
		if ( empty ( $upload_level ) ){
			$upload_level = "all";
		}
	}
	$user_id      = get_current_user_id();
	$display_flag = false;
	if ( groups_is_user_member( $user_id, $group_id ) ){
		if ( $upload_level == "admin" ){
			if ( groups_is_user_admin( $user_id, $group_id ) > 0 ){
				$display_flag = true;
			}
		} else {
			if ( $upload_level == "moderators" ){
				if ( groups_is_user_mod( $user_id, $group_id ) || groups_is_user_admin( $user_id, $group_id ) ){
					$display_flag = true;
				}
			} else {
				$display_flag = true;
			}
		}
	}
	$display_flag = apply_filters( 'can_user_create_album_in_group', $display_flag );

	return $display_flag;
}

function is_rtmedia_upload_video_enabled() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "allowedTypes_video_enabled" ] ) && $rtmedia->options[ "allowedTypes_video_enabled" ] != "0" ){
		return true;
	}

	return false;
}

function is_rtmedia_upload_photo_enabled() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "allowedTypes_photo_enabled" ] ) && $rtmedia->options[ "allowedTypes_photo_enabled" ] != "0" ){
		return true;
	}

	return false;
}

function is_rtmedia_upload_music_enabled() {
	global $rtmedia;
	if ( isset ( $rtmedia->options[ "allowedTypes_music_enabled" ] ) && $rtmedia->options[ "allowedTypes_music_enabled" ] != "0" ){
		return true;
	}

	return false;
}

function get_rtmedia_allowed_upload_type() {
	global $rtmedia;
	$allow_type_str = "";
	$sep            = "";
	foreach ( $rtmedia->allowed_types as $type ) {

		if ( function_exists( "is_rtmedia_upload_" . $type[ "name" ] . "_enabled" ) && call_user_func( "is_rtmedia_upload_" . $type[ "name" ] . "_enabled" ) ){
			foreach ( $type[ "extn" ] as $extn ) {
				$allow_type_str .= $sep . $extn;
				$sep = ",";
			}
		}
	}

	return $allow_type_str;
}


function is_rt_admin() {
	return current_user_can( "list_users" );
}

function get_rtmedia_like( $media_id = false ) {
	$mediamodel = new RTMediaModel();
	$actions    = $mediamodel->get( array( 'id' => rtmedia_id( $media_id ) ) );

	if ( isset( $actions[ 0 ]->likes ) ){
		$actions = intval( $actions[ 0 ]->likes );
	} else {
		$actions = 0;
	}

	return $actions;
}

function show_rtmedia_like_counts() {
	global $rtmedia;
	$options = $rtmedia->options;
	$count   = get_rtmedia_like();
	if ( ! ( isset( $options[ 'general_enableLikes' ] ) && $options[ 'general_enableLikes' ] == 0 ) ){
		?>
		<div class='rtmedia-like-info<?php if ( $count == 0 ){
			echo " hide";
		} ?>'><i class="rtmicon-thumbs-up"></i> <span
				class="rtmedia-like-counter-wrap"><span
					class="rtmedia-like-counter"><?php echo $count; ?></span> <?php _e( 'people like this', 'rtmedia' ); ?></span>
		</div>
	<?php
	}

}

add_action( 'rtmedia_media_gallery_actions', 'add_upload_button', 99 );
add_action( 'rtmedia_album_gallery_actions', 'add_upload_button', 99 );
function add_upload_button() {
	if ( function_exists( 'bp_is_blog_page' ) && ! bp_is_blog_page() ){
		if ( function_exists( 'bp_is_user' ) && bp_is_user() && function_exists( 'bp_displayed_user_id' ) && bp_displayed_user_id() == get_current_user_id() ){
			echo '<span class="primary rtmedia-upload-media-link" id="rtm_show_upload_ui" title="' . __( 'Upload Media', 'rtmedia' ) . '"><i class="rtmicon-upload"></i>' . __( 'Upload' ) . '</span>';
		} else {
			if ( function_exists( 'bp_is_group' ) && bp_is_group() ){
				if ( can_user_upload_in_group() ){
					echo '<span class="rtmedia-upload-media-link primary" id="rtm_show_upload_ui" title="' . __( 'Upload Media', 'rtmedia' ) . '"><i class="rtmicon-upload"></i>' . __( 'Upload' ) . '</span>';
				}
			}
		}
	}
}

//add_action("rtemdia_after_file_upload_before_activity","add_music_cover_art" ,20 ,2);
function add_music_cover_art( $file_object, $upload_obj ) {
	$mediaObj = new RTMediaMedia();
	$media    = $mediaObj->model->get( array( 'id' => $upload_obj->media_ids[ 0 ] ) );
	if ( $media[ 0 ]->media_type == "music" ){
		//$cover_art = get_music_cover_art($file_object[0]['file'], $upload_obj->media_ids[ 0 ]);
	}
}

function get_music_cover_art( $file, $id ) {
	$mediaObj = new RTMediaMedia();
	if ( ! class_exists( "getID3" ) ){
		include_once( trailingslashit( RTMEDIA_PATH ) . 'lib/getid3/getid3.php' );
	}
	$getID3    = new getID3;
	$file_info = $getID3->analyze( $file );
	if ( isset( $file_info[ 'id3v2' ][ 'APIC' ] ) && is_array( $file_info[ 'id3v2' ][ 'APIC' ] ) && $file_info[ 'id3v2' ][ 'APIC' ] != "" ){
		$title = "cover_art";
		if ( isset( $file_info[ 'id3v2' ][ 'comments' ][ 'title' ][ 0 ] ) ){
			$title = $file_info[ 'id3v2' ][ 'comments' ][ 'title' ][ 0 ];
		}
		$thumb_upload_info = wp_upload_bits( $file_info[ 'id3v2' ][ 'comments' ][ 'title' ][ 0 ] . ".jpeg", null, $file_info[ 'id3v2' ][ 'APIC' ][ 0 ][ 'data' ] );
		if ( is_array( $thumb_upload_info ) && $thumb_upload_info[ 'url' ] != "" ){
			$mediaObj->model->update( array( 'cover_art' => $thumb_upload_info[ 'url' ] ), array( 'id' => $id ) );

			return $thumb_upload_info[ 'url' ];
		}
	}
	$mediaObj->model->update( array( 'cover_art' => "-1" ), array( 'id' => $id ) );

	return false;
}

add_filter( "media_add_tabs", "rtmedia_admin_premium_tab", 99, 1 );
function rtmedia_admin_premium_tab( $tabs ) {
	if ( sizeof( $tabs ) == 0 ){
		$tabs = array();
	}
	$tabs[ ] = array(
		'href' => get_admin_url( null, add_query_arg( array( 'page' => 'rtmedia-premium' ), 'admin.php' ) ), 'name' => __( 'Go PRO!', 'rtmedia' ), 'slug' => 'rtmedia-premium', 'class' => array( 'rtm-premium' )
	);

	return $tabs;
}

function rtmedia_bp_activity_get_types( $actions ) {
	$actions[ 'rtmedia_update' ] = "rtMedia update";

	return $actions;
}

add_filter( 'bp_activity_get_types', 'rtmedia_bp_activity_get_types', 10, 1 );

add_action( "rtmedia_admin_page_insert", "rtmedia_admin_premium_page", 99, 1 );

function rtmedia_admin_premium_page( $page ) {
	if ( 'rtmedia-premium' == $page ){
		?>
		<div class="premium-page-container">
			<div class="row">
				<h1 class="premium-title"><?php _e( 'Reasons to buy rtMedia-PRO', 'rtmedia' ); ?></h1>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-tags rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Media Attributes', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Add media attributes and categories them.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-sort rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Sort Media', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can sort media from media gallery according to media size and the date of media upload.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-link rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Direct URL upload', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You no longer need to download media from URL and then upload it. Just provide link and rtMedia will handle it.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-upload rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Per user upload quota', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can set upload quota for users on the daily, monthly and lifetime basis.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-camera-retro rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'URL preview in BuddyPress activity', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Show URL previews in BuddyPress activity.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-edit rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Bulk media edit', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can edit media in bulk mode.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-thumbs-up rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'User\'s liked media page', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Now you can see user\'s liked media page. A new tab "Likes" has been added.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-rss rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'RSS Feed/Podcasting Support', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can consume rtMedia uploads from iTunes as well as any feed-reader/podcasting software.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-comments rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'WordPress Comment Attachment', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can attach files to WordPress comments.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-user rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'bbPress Attachment', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can attach files to bbPress topic and reply.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-file rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Document Support', 'rtmedia' ); ?></h2>

					<p><?php _e( 'You can add, view and download documents like txt, doc, pdf, also add and upload other file types like zip, tar and tar.gz etc.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-gamepad rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'CubePoints & MyCRED Integration', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Integrating CubePoints/myCRED with rtMedia, you can reward users with virtual points on rtMedia activities.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-lock rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Album Privacy', 'rtmedia' ); ?></h2>

					<p><?php _e( 'This will allow you to set album privacy while creating albums or change album privacy with editing albums too.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-play rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Audio Playlist', 'rtmedia' ); ?></h2>

					<p><?php _e( 'With this feature you can create your audio playlists and listen to your favorite music at will.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-warning rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Report Button & Moderation Tools', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Users can report media if they find it offensive. Set number of reports to automatically take down media.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-download rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Download Button For Media', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Users can download photos, videos and music. Admin has option to allow download the media.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-align-right rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Sidebar widgets', 'rtmedia' ); ?></h2>

					<p><?php _e( 'These will let you display a gallery or an uploader in a sidebar. Several of them can be used in a single sidebar.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-edit rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Post-editor button', 'rtmedia' ); ?></h2>

					<p><?php _e( 'With this button, a UI appears to quickly generate shortcodes for special pages like "Editorial Picks".', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-star rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Star-Rating option', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Users can give up to five stars to rate media. This data can be used for "Most Rated Media" in sidebars.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-picture-o rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Global Albums', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Multiple global albums can be created beforehand. One of these can be chosen as the default album.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-wrench rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Premium one-to-one support', 'rtmedia' ); ?></h2>

					<p><?php _e( 'Without leaving your WordPress dashboard, you can contact us for help using a support form.', 'rtmedia' ); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="columns large-1 rtm-premium-icon-pro"><i class="rtmicon-code rtmicon-3x"></i></div>
				<div class="columns large-10">
					<h2><?php _e( 'Premium & Open-Source', 'rtmedia' ); ?></h2>

					<p><?php echo __( "Developers get full control over rtMedia-PRO's source. They'll get access to " ) . "<a href='http://git.rtcamp.com/' target='_blank'>git.rtcamp.com</a>"; ?></p>
				</div>
			</div>
			</br>
			<div class="row">
				<div class="columns large-12 rtmedia-upgrade">
					<a href="http://rtcamp.com/store/rtmedia-pro/" target="_blank" class='upgrade-button'
					   title='<?php _e( 'Upgrade to rtMedia PRO Now ', 'rtmedia' ); ?>'><?php _e( 'Upgrade to rtMedia PRO Now ', 'rtmedia' ); ?></a>
				</div>
			</div>
		</div>
	<?php
	} else {
		if ( $page == "rtmedia-hire-us" ){
			$url = admin_url() . "admin.php?page=rtmedia-premium";
			?>
			<div class="rtmedia-hire-us-page-container">
				<div class="row">
					<div class='clearfix'>
						<h3><?php _e( 'You can consider rtMedia Team for following :', 'rtmedia' ); ?></h3>
						<ol>
							<li><?php _e( 'rtMedia Customization ( in Upgrade Safe manner )', 'rtmedia' ); ?></li>
							<li><?php _e( 'Wordpress/BuddyPress Theme Design and Development', 'rtmedia' ); ?></li>
							<li><?php _e( 'Wordpress/BuddyPress Plugin Development', 'rtmedia' ); ?></li>
						</ol>
					</div>
					<p class='contact'>
						<a href="https://rtcamp.com/contact" class='upgrade-button'
						   target="_blank"><?php _e( 'Contact Us', 'rtmedia' ); ?></a>
					</p>
				</div>
			</div>
		<?php
		}
	}
}

add_action( 'wp_footer', 'rtmedia_link_in_footer' );
function rtmedia_link_in_footer() {
	global $rtmedia;
	$option = $rtmedia->options;
	$link   = ( isset( $option[ 'rtmedia_add_linkback' ] ) ) ? $option[ 'rtmedia_add_linkback' ] : false;
	if ( $link ){
		$aff_id = ( $option[ 'rtmedia_affiliate_id' ] != "" ) ? '&ref=' . $option[ 'rtmedia_affiliate_id' ] : "";
		$href   = 'https://rtcamp.com/rtmedia/?utm_source=dashboard&utm_medium=plugin&utm_campaign=buddypress-media' . $aff_id;
		?>

		<div class='rtmedia-footer-link'>
			<?php echo __( "Empowering your community with ", 'rtmedia' ); ?>
			<a href='<?php echo esc_url( $href ) ?>'
			   title='<?php echo __( 'The only complete media solution for WordPress, BuddyPress and bbPress', 'rtmedia' ); ?> '>
				rtMedia</a>
		</div>
	<?php
	}
}

//add content before the media in single media page
add_action( 'rtmedia_before_media', 'rtmedia_content_before_media', 10 );
function  rtmedia_content_before_media() {
	global $rt_ajax_request;

	if ( $rt_ajax_request ){
		?>
		<div class='rtm-mfp-close'>
			<!--                <i class='rtmicon-cancel mfp-close'title='Close (Esc)'></i>-->
			<span class="mfp-close" title="<?php _e( 'Close (Esc)' ); ?>">x</span>
		</div>
	<?php
	}
}

//get the mediaprivacy symbol
function get_rtmedia_privacy_symbol( $rtmedia_id = false ) {
	$mediamodel = new RTMediaModel();
	$actions    = $mediamodel->get( array( 'id' => rtmedia_id( $rtmedia_id ) ) );
	$privacy    = "";
	if ( isset( $actions[ 0 ]->privacy ) && $actions[ 0 ]->privacy != "" ){
		$title = $icon = "";

		switch ( $actions[ 0 ]->privacy ) {
			case 0: //public
				$title = __( "Public", 'rtmedia' );
				$icon  = 'rtmicon-globe';
				break;
			case 20: //users
				$title = __( "All members", 'rtmedia' );
				$icon  = 'rtmicon-users';
				break;
			case 40: // friends
				$title = __( "Your friends", 'rtmedia' );
				$icon  = 'rtmicon-users-1';
				break;
			case 60: // private
				$title = __( "Only you", 'rtmedia' );
				$icon  = 'rtmicon-lock';
				break;
			case 80: // private
				$title = __( "Blocked Temperorily", 'rtmedia' );
				$icon  = 'rtmicon-ban';
				break;
		}
		if ( $title != "" && $icon != "" ){
			$privacy = "<i class='" . $icon . "' title='" . $title . "'></i>";
		}

	}

	return $privacy;
}

//
function get_rtmedia_date_gmt( $rtmedia_id = false ) {
	$media     = get_post( rtmedia_media_id( rtmedia_id( $rtmedia_id ) ) );
	$date_time = "";
	if ( isset( $media->post_date_gmt ) && $media->post_date_gmt != "" ){
		$date_time = rtmedia_convert_date( $media->post_date_gmt );
	}

	return $date_time;
}

//function to convert comment datetime to "time ago" format.
function rtmedia_convert_date( $_date ) // $date --> time(); value
{
	$stf       = 0;
	$date      = new DateTime( $_date );
	$date      = $date->format( 'U' );
	$cur_time  = time();
	$diff      = $cur_time - $date;
	$time_unit = array( 'second', 'minute', 'hour' );
	//$phrase = array('second','minute','hour','day','week','month','year','decade');
	//$length = array(1,60,3600,86400,604800,2630880,31570560,315705600);
	$length   = array( 1, 60, 3600, 86400 );
	$ago_text = __( '%s ago ', 'rtmedia' );

	for ( $i = sizeof( $length ) - 1; ( $i >= 0 ) && ( ( $no = $diff / $length[ $i ] ) <= 1 ); $i -- ) ;
	if ( $i < 0 ){
		$i = 0;
	}
	if ( $i <= 2 ){ //if posted in last 24 hours
		$_time = $cur_time - ( $diff % $length[ $i ] );

		$no = floor( $no );
		switch ( $time_unit[ $i ] ) {
			case 'second':
				$time_unit_phrase = _n( '1 second', '%s seconds', $no, 'rtmedia' );
				break;
			case 'minute':
				$time_unit_phrase = _n( '1 minute', '%s minutes', $no, 'rtmedia' );
				break;
			case 'hour':
				$time_unit_phrase = _n( '1 hour', '%s hours', $no, 'rtmedia' );
				break;
			default:
				// should not happen
				$time_unit_phrase = '%s unknown';
		}
		$value = sprintf( $time_unit_phrase . ' ', $no );

		if ( ( $stf == 1 ) && ( $i >= 1 ) && ( ( $cur_tm - $_time ) > 0 ) ){
			$value .= rtmedia_convert_date( $_time );
		}

		return sprintf( $ago_text, $value );
	} else {
		/* translators: date format, see http://php.net/date */
		return date_i18n( "d F Y ", strtotime( $_date ), true );
	}
}


//function to get media counts
function get_media_counts() {
	global $rtmedia_query;
	$user_id = false;
	if ( function_exists( "bp_displayed_user_id" ) ){
		$user_id = bp_displayed_user_id();
	} else {
		if ( isset( $rtmedia_query ) && isset( $rtmedia_query->query[ 'context_id' ] ) && $rtmedia_query->query[ 'context' ] == "profile" ){
			$user_id = $rtmedia_query->query[ 'context_id' ];
		}
	}
	$media_nav         = new RTMediaNav( false );
	$user_media_counts = $media_nav->get_counts( $user_id );
	//var_dump($user_media_counts);
	$temp = $media_nav->actual_counts( $user_id );

	return $temp;

	//return $user_counts;
}

add_action( 'wp_head', 'rtmedia_custom_css' );
function rtmedia_custom_css() {
	global $rtmedia;
	$options = $rtmedia->options;
	if ( isset( $options[ 'styles_custom' ] ) && $options[ 'styles_custom' ] != "" ){
		echo "<style type='text/css'> " . stripslashes( $options[ 'styles_custom' ] ) . " </style>";
	}
}


add_action( 'wp_ajax_delete_uploaded_media', 'rtmedia_delete_uploaded_media' );
function rtmedia_delete_uploaded_media() {

	if ( isset( $_POST ) && isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'delete_uploaded_media' && isset( $_POST[ 'media_id' ] ) && $_POST[ 'media_id' ] != "" ){

		if ( wp_verify_nonce( $_POST[ 'nonce' ], 'rtmedia_' . get_current_user_id() ) ){

			$media    = new RTMediaMedia();
			$media_id = $_POST[ 'media_id' ];

			$delete = $media->delete( $media_id );
			echo "1";
			die();
		}
	}

	echo "0";
	die();

}

function rtmedia_is_edit_page( $new_edit = null ) {
	global $pagenow;
	//make sure we are on the backend
	if ( ! is_admin() ){
		return false;
	}
	if ( $new_edit == "edit" ){
		return in_array( $pagenow, array( 'post.php', ) );
	} elseif ( $new_edit == "new" ) { //check for new post page
		return in_array( $pagenow, array( 'post-new.php' ) );
	} else { //check for either new or edit
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}
}

//update the group media privacy according to the group privacy settings when group settings are changed
add_action( 'groups_settings_updated', 'update_group_media_privacy', 99, 1 );
function update_group_media_privacy( $group_id ) {
	if ( isset( $group_id ) && $group_id != "" && function_exists( 'groups_get_group' ) ){
		//get the buddybress group
		$group = groups_get_group( array( 'group_id' => $group_id ) );
		if ( isset( $group->status ) ){
			$update_sql = '';
			$model      = new RTMediaModel();
			global $wpdb;
			if ( $group->status != 'public' ){
				// when group settings are updated and is private/hidden, set media privacy to 20
				$update_sql = "UPDATE $model->table_name SET privacy = '20' where context='group' AND context_id=" . $group_id . " AND privacy <> 80 ";

			} else {

				// when group settings are updated and is private/hidden, set media privacy to 0
				$update_sql = "UPDATE $model->table_name SET privacy = '0' where context='group' AND context_id=" . $group_id . " AND privacy <> 80 ";
			}
			//update the medias
			$wpdb->query( $update_sql );
		}

	}
}

/* check if rtMedia page */
function is_rtmedia_page() {
	if ( ! defined( 'RTMEDIA_MEDIA_SLUG' ) ){
		return false;
	}

	global $rtmedia_interaction;

	if ( ! isset( $rtmedia_interaction ) ){
		return false;
	}

	if ( ! isset( $rtmedia_interaction->routes ) ){
		return false;
	}

	return $rtmedia_interaction->routes[ RTMEDIA_MEDIA_SLUG ]->is_template();
}

// formatseconds function to be used in migration in importing
function rtmedia_migrate_formatseconds( $secondsLeft ) {

	$minuteInSeconds = 60;
	$hourInSeconds   = $minuteInSeconds * 60;
	$dayInSeconds    = $hourInSeconds * 24;

	$days        = floor( $secondsLeft / $dayInSeconds );
	$secondsLeft = $secondsLeft % $dayInSeconds;

	$hours       = floor( $secondsLeft / $hourInSeconds );
	$secondsLeft = $secondsLeft % $hourInSeconds;

	$minutes = floor( $secondsLeft / $minuteInSeconds );

	$seconds = $secondsLeft % $minuteInSeconds;

	$timeComponents = array();

	if ( $days > 0 ){
		$timeComponents[ ] = $days . " day" . ( $days > 1 ? "s" : "" );
	}

	if ( $hours > 0 ){
		$timeComponents[ ] = $hours . " hour" . ( $hours > 1 ? "s" : "" );
	}

	if ( $minutes > 0 ){
		$timeComponents[ ] = $minutes . " minute" . ( $minutes > 1 ? "s" : "" );
	}

	if ( $seconds > 0 ){
		$timeComponents[ ] = $seconds . " second" . ( $seconds > 1 ? "s" : "" );
	}
	if ( count( $timeComponents ) > 0 ){
		$formattedTimeRemaining = implode( ", ", $timeComponents );
		$formattedTimeRemaining = trim( $formattedTimeRemaining );
	} else {
		$formattedTimeRemaining = "No time remaining.";
	}

	return $formattedTimeRemaining;
}


/**
 * echo the size of the media file
 *
 * @global type $rtmedia_media
 */
function rtmedia_file_size() {

	global $rtmedia_backbone;
	if ( $rtmedia_backbone[ 'backbone' ] ){
		echo '<%= file_size %>';
	} else {
		global $rtmedia_media;
		if ( isset( $rtmedia_media->file_size ) ){
			return $rtmedia_media->file_size;
		} else {
			return filesize( get_attached_file( $rtmedia_media->media_id ) );
		}
	}
}

/*
 * get rtmedia media type from file extension
 */
function rtmedia_get_media_type_from_extn( $extn ) {
	global $rtmedia;
	$allowed_type = $rtmedia->allowed_types;
	foreach ( $allowed_type as $type => $param ) {
		if ( isset( $param[ 'extn' ] ) && is_array( $param[ 'extn' ] ) && in_array( $extn, $param[ 'extn' ] ) ){
			return $type;
		}
	}
	return false;
}