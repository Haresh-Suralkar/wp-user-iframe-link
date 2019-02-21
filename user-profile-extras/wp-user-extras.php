<?php
/*
Plugin Name: User Extras
Description: Add custom fields to wordpress for user edit form
Version: 1.0
Author: Haresh Suralkar

Text Domain: wp-user-extras 
*/

/**
 * Show custom user profile fields
 * 
 * @param  object $profileuser A WP_User object
 * @return void
 */
function custom_user_profile_fields( $profileuser ) {
	$current_user = wp_get_current_user();
	if( is_user_logged_in() && array_search('administrator', (array) $current_user->roles) !== FALSE ) {
		?>
			<table class="form-table">
				<tr>
					<th>
						<label for="user_report_url"><?php esc_html_e( 'Report URL' ); ?></label>
					</th>
					<td>
						<input type="text" name="user_report_url" id="user_report_url" value="<?php echo esc_attr( get_the_author_meta( 'user_report_url', $profileuser->ID ) ); ?>" class="regular-text" />
						<br><span class="description"><?php esc_html_e( 'iFrame URL', 'text-domain' ); ?></span>
					</td>
				</tr>
			</table>
		<?php
	}
}
add_action( 'show_user_profile', 'custom_user_profile_fields', 10, 1 );
add_action( 'edit_user_profile', 'custom_user_profile_fields', 10, 1 );

// add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
    $current_user = wp_get_current_user();
	if( is_user_logged_in() && array_search('administrator', (array) $current_user->roles) !== FALSE ) {
    	update_user_meta( $user_id, 'user_report_url', $_POST['user_report_url'] );
    }
}

function zeplin_report_show( $atts = array() ) {
	if( !is_user_logged_in() ){
    	?>
			<script type="text/javascript">
				window.location.href = '<?php echo wp_login_url( v_getUrl() ); ?>';
			</script>
		<?php
	}

	$user_report_url=get_user_meta(get_current_user_id(), 'user_report_url', TRUE);
	if(!empty($user_report_url)) {
			
		?>
			<iframe src="<?php echo $user_report_url; ?>" style=" height: 900px;width: 900px;"></iframe>
		<?php
	} else {
		?>
			<div>Alas! we couldn't find any report for you. Please contact Administrator for more details.</div>
		<?php
	}
}

add_shortcode( 'ZEPLIN_REPORT', 'zeplin_report_show' );

function v_getUrl() {
	$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
	$url .= '://' . $_SERVER['SERVER_NAME'];
	$url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
	$url .= $_SERVER['REQUEST_URI'];
	return $url;
}