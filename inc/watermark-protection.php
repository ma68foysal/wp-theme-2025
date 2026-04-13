<?php
/**
 * BANTU Plus - Watermark Protection Implementation
 * Handles watermark generation and video protection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enqueue anti-piracy script on video pages
add_action( 'wp_enqueue_scripts', 'bantu_enqueue_antipiracy_script' );
function bantu_enqueue_antipiracy_script() {
	if ( is_singular( 'video' ) || is_singular( 'post' ) ) {
		wp_enqueue_script(
			'bantu-anti-piracy',
			get_template_directory_uri() . '/assets/js/anti-piracy.js',
			array( 'bantu-plus-script' ),
			wp_get_theme()->get( 'Version' ),
			true
		);

		// Pass settings to JS
		wp_localize_script(
			'bantu-anti-piracy',
			'bantuAntiPiracy',
			array(
				'disableDownload'    => get_option( 'bantu_disable_download' ),
				'disableRightClick'  => get_option( 'bantu_disable_rightclick' ),
				'disableScreenRecord' => get_option( 'bantu_disable_screenrecord' ),
				'enableLogging'      => get_option( 'bantu_enable_logging' ),
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'bantu_piracy_nonce' ),
			)
		);
	}
}

// Log video access attempts
add_action( 'wp_ajax_bantu_log_access', 'bantu_log_video_access' );
add_action( 'wp_ajax_nopriv_bantu_log_access', 'bantu_log_video_access' );
function bantu_log_video_access() {
	check_ajax_referer( 'bantu_piracy_nonce', 'nonce', false );

	if ( ! get_option( 'bantu_enable_logging' ) ) {
		wp_send_json_success();
		return;
	}

	$video_id = isset( $_POST['video_id'] ) ? intval( $_POST['video_id'] ) : 0;
	$action   = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : 'view';
	$user_id  = get_current_user_id();
	$ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
	$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

	// Create logs table if needed
	global $wpdb;
	$table_name = $wpdb->prefix . 'bantu_access_logs';

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			video_id bigint(20),
			user_id bigint(20),
			action varchar(50),
			ip_address varchar(45),
			user_agent text,
			timestamp datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	// Log the access
	$wpdb->insert(
		$table_name,
		array(
			'video_id'   => $video_id,
			'user_id'    => $user_id,
			'action'     => $action,
			'ip_address' => $ip_address,
			'user_agent' => $user_agent,
		),
		array( '%d', '%d', '%s', '%s', '%s' )
	);

	wp_send_json_success();
}

// Monitor concurrent streams
add_action( 'wp_ajax_bantu_check_stream', 'bantu_check_concurrent_streams' );
function bantu_check_concurrent_streams() {
	check_ajax_referer( 'bantu_piracy_nonce', 'nonce', false );

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		wp_send_json_error( 'Not logged in' );
	}

	$video_id = isset( $_POST['video_id'] ) ? intval( $_POST['video_id'] ) : 0;
	$max_concurrent = intval( get_option( 'bantu_max_concurrent', 2 ) );

	// Get user's active sessions
	global $wpdb;
	$table_name = $wpdb->prefix . 'bantu_access_logs';

	$concurrent_count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT ip_address) FROM $table_name 
			WHERE user_id = %d AND action = 'streaming' 
			AND timestamp > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
			$user_id
		)
	);

	if ( $concurrent_count >= $max_concurrent ) {
		wp_send_json_error( 'Max concurrent streams exceeded' );
	}

	wp_send_json_success();
}

// Create table on plugin activation
add_action( 'admin_init', 'bantu_create_access_logs_table' );
function bantu_create_access_logs_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bantu_access_logs';

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			video_id bigint(20),
			user_id bigint(20),
			action varchar(50),
			ip_address varchar(45),
			user_agent text,
			timestamp datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY video_id (video_id),
			KEY user_id (user_id),
			KEY timestamp (timestamp)
		) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

// Add menu for viewing access logs
add_action( 'admin_menu', 'bantu_register_access_logs_menu' );
function bantu_register_access_logs_menu() {
	add_submenu_page(
		'bantu-settings',
		'Access Logs',
		'Access Logs',
		'manage_options',
		'bantu-access-logs',
		'bantu_render_access_logs_page'
	);
}

// Render Access Logs Page
function bantu_render_access_logs_page() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bantu_access_logs';

	// Get logs from last 30 days
	$logs = $wpdb->get_results(
		"SELECT * FROM $table_name 
		WHERE timestamp > DATE_SUB(NOW(), INTERVAL 30 DAY)
		ORDER BY timestamp DESC
		LIMIT 500"
	);
	?>
	<div class="wrap">
		<h1>Video Access Logs</h1>
		
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<th>Date/Time</th>
					<th>Video ID</th>
					<th>User</th>
					<th>Action</th>
					<th>IP Address</th>
					<th>User Agent</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $logs ) : ?>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( $log->timestamp ); ?></td>
							<td>
								<a href="<?php echo esc_url( get_edit_post_link( $log->video_id ) ); ?>">
									<?php echo esc_html( get_the_title( $log->video_id ) ); ?>
								</a>
							</td>
							<td>
								<?php 
									$user = get_userdata( $log->user_id );
									echo $user ? esc_html( $user->user_login ) : 'Guest';
								?>
							</td>
							<td><?php echo esc_html( $log->action ); ?></td>
							<td><?php echo esc_html( $log->ip_address ); ?></td>
							<td style="font-size: 0.85em; max-width: 300px; word-break: break-word;">
								<?php echo esc_html( substr( $log->user_agent, 0, 100 ) ); ?>...
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="6">No access logs found</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

// Watermark validation function
function bantu_validate_watermark_settings() {
	$errors = array();

	if ( get_option( 'bantu_watermark_enabled' ) ) {
		$text = get_option( 'bantu_watermark_text' );
		if ( empty( $text ) ) {
			$errors[] = 'Watermark text is required when watermarking is enabled';
		}

		$opacity = intval( get_option( 'bantu_watermark_opacity' ) );
		if ( $opacity < 5 || $opacity > 100 ) {
			$errors[] = 'Watermark opacity must be between 5 and 100';
		}
	}

	if ( get_option( 'bantu_geo_restrict' ) ) {
		$countries = get_option( 'bantu_geo_countries' );
		if ( empty( $countries ) ) {
			$errors[] = 'Country list is required when geo-restriction is enabled';
		}
	}

	return $errors;
}
