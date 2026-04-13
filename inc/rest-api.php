<?php
/**
 * BANTU Plus - REST API Endpoints
 * Mobile app integration with HLS streaming support
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

// Register REST API routes
add_action( 'rest_api_init', 'bantu_register_rest_routes' );
function bantu_register_rest_routes() {
	// Auth endpoints
	register_rest_route( 'bantu/v1', '/auth/login', array(
		'methods'             => 'POST',
		'callback'            => 'bantu_api_login',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'bantu/v1', '/auth/logout', array(
		'methods'             => 'POST',
		'callback'            => 'bantu_api_logout',
		'permission_callback' => 'is_user_logged_in',
	) );

	register_rest_route( 'bantu/v1', '/auth/me', array(
		'methods'             => 'GET',
		'callback'            => 'bantu_api_get_current_user',
		'permission_callback' => 'is_user_logged_in',
	) );

	// Video endpoints
	register_rest_route( 'bantu/v1', '/videos', array(
		'methods'             => 'GET',
		'callback'            => 'bantu_api_get_videos',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'bantu/v1', '/videos/(?P<id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'bantu_api_get_video',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'bantu/v1', '/videos/(?P<id>\d+)/stream', array(
		'methods'             => 'GET',
		'callback'            => 'bantu_api_get_video_stream',
		'permission_callback' => '__return_true',
	) );

	// Membership endpoints
	register_rest_route( 'bantu/v1', '/membership/status', array(
		'methods'             => 'GET',
		'callback'            => 'bantu_api_get_membership',
		'permission_callback' => 'is_user_logged_in',
	) );

	register_rest_route( 'bantu/v1', '/account/progress', array(
		'methods'             => 'POST',
		'callback'            => 'bantu_api_save_progress',
		'permission_callback' => 'is_user_logged_in',
	) );

	register_rest_route( 'bantu/v1', '/account/progress/(?P<video_id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'bantu_api_get_progress',
		'permission_callback' => 'is_user_logged_in',
	) );
}

/**
 * Login endpoint
 */
function bantu_api_login( $request ) {
	$params = $request->get_json_params();
	$username = isset( $params['username'] ) ? sanitize_user( $params['username'] ) : '';
	$password = isset( $params['password'] ) ? $params['password'] : '';

	if ( empty( $username ) || empty( $password ) ) {
		return new WP_Error( 'missing_credentials', 'Username and password required', array( 'status' => 400 ) );
	}

	$user = wp_signon( array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => false,
	), false );

	if ( is_wp_error( $user ) ) {
		return new WP_Error( 'login_failed', $user->get_error_message(), array( 'status' => 401 ) );
	}

	wp_set_current_user( $user->ID );
	do_action( 'wp_login', $user->user_login, $user );

	return rest_ensure_response( array(
		'user_id'  => $user->ID,
		'username' => $user->user_login,
		'email'    => $user->user_email,
		'token'    => wp_create_nonce( 'bantu_mobile_token' ),
	) );
}

/**
 * Logout endpoint
 */
function bantu_api_logout() {
	wp_logout();
	return rest_ensure_response( array(
		'message' => 'Logged out successfully',
	) );
}

/**
 * Get current user endpoint
 */
function bantu_api_get_current_user() {
	$user = wp_get_current_user();

	return rest_ensure_response( array(
		'user_id'  => $user->ID,
		'username' => $user->user_login,
		'email'    => $user->user_email,
		'name'     => $user->display_name,
	) );
}

/**
 * Get videos endpoint
 */
function bantu_api_get_videos( $request ) {
	$per_page = intval( $request->get_param( 'per_page' ) ) ?: 12;
	$page = intval( $request->get_param( 'page' ) ) ?: 1;
	$category = $request->get_param( 'category' );
	$search = $request->get_param( 'search' );

	$args = array(
		'post_type'      => 'video',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'video_category',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $category ),
			),
		);
	}

	if ( ! empty( $search ) ) {
		$args['s'] = sanitize_text_field( $search );
	}

	$videos = get_posts( $args );
	$total = wp_count_posts( 'video' )->publish;

	$data = array(
		'videos' => array(),
		'total'  => $total,
		'pages'  => ceil( $total / $per_page ),
		'page'   => $page,
	);

	foreach ( $videos as $video ) {
		$data['videos'][] = bantu_format_video_response( $video );
	}

	return rest_ensure_response( $data );
}

/**
 * Get single video endpoint
 */
function bantu_api_get_video( $request ) {
	$video_id = intval( $request['id'] );
	$video = get_post( $video_id );

	if ( ! $video || $video->post_type !== 'video' ) {
		return new WP_Error( 'not_found', 'Video not found', array( 'status' => 404 ) );
	}

	return rest_ensure_response( bantu_format_video_response( $video ) );
}

/**
 * Get video stream URL endpoint (with access control)
 */
function bantu_api_get_video_stream( $request ) {
	$video_id = intval( $request['id'] );
	$video = get_post( $video_id );

	if ( ! $video || $video->post_type !== 'video' ) {
		return new WP_Error( 'not_found', 'Video not found', array( 'status' => 404 ) );
	}

	// Check membership
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		$membership = bantu_check_user_membership( $user_id );

		if ( ! $membership['active'] ) {
			return new WP_Error( 'no_access', 'Active membership required', array( 'status' => 403 ) );
		}
	} else {
		return new WP_Error( 'not_authenticated', 'Authentication required', array( 'status' => 401 ) );
	}

	// Get HLS URL
	$bunny_guid = get_post_meta( $video_id, 'bunny_guid', true );
	if ( empty( $bunny_guid ) ) {
		return new WP_Error( 'no_stream', 'Video not available for streaming', array( 'status' => 404 ) );
	}

	$hls_url = bantu_get_hls_url( $bunny_guid );

	return rest_ensure_response( array(
		'video_id' => $video_id,
		'hls_url'  => $hls_url,
		'type'     => 'application/x-mpegURL',
	) );
}

/**
 * Get membership status endpoint
 */
function bantu_api_get_membership() {
	$user_id = get_current_user_id();
	$status = bantu_check_user_membership( $user_id );

	return rest_ensure_response( $status );
}

/**
 * Save video progress endpoint
 */
function bantu_api_save_progress( $request ) {
	$user_id = get_current_user_id();
	$params = $request->get_json_params();

	$video_id = intval( $params['video_id'] ?? 0 );
	$current_time = floatval( $params['current_time'] ?? 0 );

	if ( empty( $video_id ) ) {
		return new WP_Error( 'missing_data', 'Video ID required', array( 'status' => 400 ) );
	}

	// Save to user meta (alternatively, save to custom table)
	update_user_meta( $user_id, "bantu_video_{$video_id}_progress", $current_time );

	return rest_ensure_response( array(
		'message' => 'Progress saved',
		'video_id' => $video_id,
		'current_time' => $current_time,
	) );
}

/**
 * Get video progress endpoint
 */
function bantu_api_get_progress( $request ) {
	$user_id = get_current_user_id();
	$video_id = intval( $request['video_id'] );

	$progress = get_user_meta( $user_id, "bantu_video_{$video_id}_progress", true );

	return rest_ensure_response( array(
		'video_id' => $video_id,
		'current_time' => floatval( $progress ) ?: 0,
	) );
}

/**
 * Format video data for API response
 */
function bantu_format_video_response( $post ) {
	$video_id = $post->ID;
	$thumbnail_id = get_post_thumbnail_id( $video_id );
	$thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';

	$categories = get_the_terms( $video_id, 'video_category' );
	$category_names = array();
	if ( ! empty( $categories ) ) {
		$category_names = wp_list_pluck( $categories, 'name' );
	}

	return array(
		'id'        => $video_id,
		'title'     => $post->post_title,
		'excerpt'   => wp_trim_excerpt( $post->post_excerpt ?: $post->post_content ),
		'thumbnail' => $thumbnail_url,
		'duration'  => floatval( get_post_meta( $video_id, 'video_duration', true ) ) ?: 0,
		'categories' => $category_names,
		'url'       => get_permalink( $video_id ),
		'created'   => $post->post_date_gmt,
	);
}

/**
 * Helper: Get HLS URL (moved from single-video.php for reuse)
 */
if ( ! function_exists( 'bantu_get_hls_url' ) ) {
	function bantu_get_hls_url( $guid ) {
		$bunny_cdn_url = defined( 'BUNNY_CDN_URL' ) ? BUNNY_CDN_URL : get_option( 'bantu_bunny_cdn_url' );
		return $bunny_cdn_url . '/' . sanitize_text_field( $guid ) . '/playlist.m3u8';
	}
}
