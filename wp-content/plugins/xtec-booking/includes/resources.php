<?php 

// CREATE CUSTOM METABOX
function xtec_add_metabox_resource( $post ){

	$metabox = array(
		'id' 		=> 'xtec_resources_status',
		'title' 	=> __('Resources parameters','xtec-booking'),
		'callback' 	=> 'xtec_add_metabox_status_resources',
		'page' 		=> 'calendar_resources',
		'context' 	=> 'normal',
		'priority' 	=> 'high'
	);

	add_meta_box( $metabox['id'],$metabox['title'],$metabox['callback'],$metabox['page'],$metabox['context'],$metabox['priority'] );
}

// HTML TO METABOX STATUS
function xtec_add_metabox_status_resources( $post ){
	$data['status'] = get_post_meta( $post->ID, '_xtec_resources_status', true );
	$data['color'] = get_post_meta( $post->ID, '_xtec_resources_color', true );
?>
	<span class="description"><?php _e('Select availability resource','xtec-booking'); ?></span>
	<br>
	<select name="_xtec_resources_status" class="xtec-booking-input" required>
		<option value="all_users" selected ><?php _e('Available','xtec-booking') ?></option>
		<option value="inactive"><?php _e('Not available','xtec-booking') ?></option>
	</select>
	<?php if ( $data['status'] != '' ){ ?>
	<script>
		jQuery('select[name="_xtec_resources_status"] option[value="<?php echo $data['status']; ?>"').prop('selected',true);
	</script>
	<?php } ?>
	<br><br>
	<span class="description"><?php _e('Select color resource','xtec-booking'); ?></span>
	<br>
	<select name="_xtec_resources_color" class="xtec-booking-input" required>
		<option value="red"><?php _e('Red','xtec-booking') ?></option>
		<option value="yellow"><?php _e('Yellow','xtec-booking') ?></option>
		<option value="blue"><?php _e('Blue','xtec-booking') ?></option>
		<option value="black"><?php _e('Black','xtec-booking') ?></option>
		<option value="green"><?php _e('Green','xtec-booking') ?></option>
		<option value="purple"><?php _e('Purple','xtec-booking') ?></option>
		<option value="orange"><?php _e('Orange','xtec-booking') ?></option>
		<option value="pink"><?php _e('Pink','xtec-booking') ?></option>
		<option value="brown"><?php _e('Brown','xtec-booking') ?></option>
		<option value="light_blue"><?php _e('Light blue','xtec-booking') ?></option>
	</select>
	<?php if ( $data['color'] != '' ){ ?>
	<script>
		jQuery('select[name="_xtec_resources_color"] option[value="<?php echo $data['color']; ?>"').prop('selected',true);
	</script>
	<?php } ?>
<?php
}

function xtec_booking_get_thumbnail_resource($resourceId){

	$size = 'thumbnail';
	$thumbnail = get_the_post_thumbnail($resourceId,$size);
	$post = get_post($resourceId);

	$thumbnail .= '<p>'.strip_tags($post->post_content,'<ul><ol><li>').'</p>';

	return $thumbnail;

}
