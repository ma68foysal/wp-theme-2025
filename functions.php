<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// ============================================
// BANTU PLUS CUSTOM FUNCTIONALITY
// ============================================

// Include admin functionality
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/admin-video-upload.php';
	require_once get_template_directory() . '/inc/bunny-settings.php';
}

// Include membership and auth functionality
require_once get_template_directory() . '/inc/membership-database.php';
require_once get_template_directory() . '/inc/auth-shortcodes.php';
require_once get_template_directory() . '/inc/stripe-payments.php';
require_once get_template_directory() . '/inc/rest-api.php';

// Register custom post types for BANTU PLUS
if ( ! function_exists( 'bantu_register_post_types' ) ) :
	/**
	 * Register custom post types: Video, Category
	 *
	 * @return void
	 */
	function bantu_register_post_types() {
		// Video Post Type
		register_post_type(
			'video',
			array(
				'label'        => 'Videos',
				'public'       => true,
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'videos' ),
				'menu_icon'    => 'dashicons-format-video',
			)
		);

		// Video Category Taxonomy
		register_taxonomy(
			'video_category',
			'video',
			array(
				'label'        => 'Video Categories',
				'public'       => true,
				'show_in_rest' => true,
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'video-category' ),
			)
		);
	}
endif;
add_action( 'init', 'bantu_register_post_types' );

// Register navigation menus
if ( ! function_exists( 'bantu_register_nav_menus' ) ) :
	/**
	 * Register navigation menus
	 *
	 * @return void
	 */
	function bantu_register_nav_menus() {
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'bantu-plus' ),
		) );
	}
endif;
add_action( 'after_setup_theme', 'bantu_register_nav_menus' );

// Default fallback navigation menu
if ( ! function_exists( 'bantu_default_nav_menu' ) ) :
	/**
	 * Default fallback navigation menu if none is assigned
	 *
	 * @return void
	 */
	function bantu_default_nav_menu() {
		echo '<ul class="bantu-nav-menu">';
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
		echo '<li><a href="' . esc_url( home_url( '/videos' ) ) . '">Browse</a></li>';
		if ( is_user_logged_in() ) {
			echo '<li><a href="' . esc_url( home_url( '/dashboard' ) ) . '">My Dashboard</a></li>';
		}
		echo '</ul>';
	}
endif;

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues the theme stylesheet on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues the theme stylesheet on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$src    = 'style' . $suffix . '.css';

		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( $src ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
		wp_style_add_data(
			'twentytwentyfive-style',
			'path',
			get_parent_theme_file_path( $src )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Enqueue BANTU Plus custom styles
if ( ! function_exists( 'bantu_enqueue_styles' ) ) :
	/**
	 * Enqueue BANTU Plus custom stylesheet.
	 *
	 * @return void
	 */
	function bantu_enqueue_styles() {
		wp_enqueue_style(
			'bantu-plus-style',
			get_template_directory_uri() . '/assets/css/bantu-plus.css',
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'bantu_enqueue_styles' );

// Enqueue BANTU Plus custom scripts
if ( ! function_exists( 'bantu_enqueue_scripts' ) ) :
	/**
	 * Enqueue BANTU Plus custom JavaScript.
	 *
	 * @return void
	 */
	function bantu_enqueue_scripts() {
		// HLS.js for video streaming
		wp_enqueue_script(
			'hls-js',
			'https://cdn.jsdelivr.net/npm/hls.js@latest',
			array(),
			null,
			true
		);

		// BANTU Plus custom JS
		wp_enqueue_script(
			'bantu-plus-script',
			get_template_directory_uri() . '/assets/js/bantu-plus.js',
			array( 'hls-js' ),
			wp_get_theme()->get( 'Version' ),
			true
		);

		// Localize script with AJAX URL and nonce
		wp_localize_script(
			'bantu-plus-script',
			'bantuAjax',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'bantu_security_nonce' ),
			)
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'bantu_enqueue_scripts' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

// ============================================
// BANTU PLUS AJAX HANDLERS
// ============================================

// Check user membership status
if ( ! function_exists( 'bantu_check_user_membership' ) ) :
	/**
	 * Check if user has an active membership.
	 *
	 * @param int $user_id User ID
	 * @return array Membership status array
	 */
	function bantu_check_user_membership( $user_id ) {
		// Default: no membership
		$status = array(
			'active'  => false,
			'level'   => 'none',
			'expiry'  => '',
			'days_left' => 0,
		);

		if ( empty( $user_id ) ) {
			return $status;
		}

		// Get membership expiry from user meta
		$expiry_timestamp = get_user_meta( $user_id, 'bantu_membership_expiry', true );

		if ( empty( $expiry_timestamp ) ) {
			return $status;
		}

		$current_time = current_time( 'timestamp' );
		
		if ( intval( $expiry_timestamp ) > $current_time ) {
			$status['active']  = true;
			$status['level']   = get_user_meta( $user_id, 'bantu_membership_level', true ) ?: 'standard';
			$status['expiry']  = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), intval( $expiry_timestamp ) );
			$status['days_left'] = ceil( ( intval( $expiry_timestamp ) - $current_time ) / DAY_IN_SECONDS );
		}

		return $status;
	}
endif;

// AJAX: Search Videos
if ( ! function_exists( 'bantu_search_videos_callback' ) ) :
	/**
	 * AJAX callback for video search.
	 *
	 * @return void
	 */
	function bantu_search_videos_callback() {
		check_ajax_referer( 'bantu_security_nonce', 'nonce' );

		$query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

		if ( strlen( $query ) < 2 ) {
			wp_send_json_error( 'Query too short' );
		}

		$args = array(
			'post_type'      => 'video',
			'posts_per_page' => 12,
			's'              => $query,
		);

		$videos = get_posts( $args );
		$results = array();

		foreach ( $videos as $video ) {
			$thumbnail_id = get_post_thumbnail_id( $video->ID );
			$thumbnail    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';

			$results[] = array(
				'id'        => $video->ID,
				'title'     => $video->post_title,
				'url'       => get_permalink( $video->ID ),
				'thumbnail' => $thumbnail,
				'duration'  => get_post_meta( $video->ID, 'video_duration', true ) ?: '0',
			);
		}

		wp_send_json_success( $results );
	}
endif;
add_action( 'wp_ajax_bantu_search_videos', 'bantu_search_videos_callback' );
add_action( 'wp_ajax_nopriv_bantu_search_videos', 'bantu_search_videos_callback' );

// AJAX: Record watch history
if ( ! function_exists( 'bantu_record_watch_callback' ) ) :
	/**
	 * AJAX callback to record video watch
	 *
	 * @return void
	 */
	function bantu_record_watch_callback() {
		check_ajax_referer( 'bantu_security_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'User not logged in' );
		}

		$user_id  = get_current_user_id();
		$video_id = isset( $_POST['video_id'] ) ? intval( $_POST['video_id'] ) : 0;

		if ( ! $video_id ) {
			wp_send_json_error( 'Invalid video ID' );
		}

		// Get existing history
		$history = get_user_meta( $user_id, 'bantu_video_history', true ) ?: array();

		// Add/update video watch timestamp
		$history[ $video_id ] = current_time( 'timestamp' );

		// Keep only last 100 watched videos
		if ( count( $history ) > 100 ) {
			asort( $history );
			$history = array_slice( $history, -100 );
		}

		// Save updated history
		update_user_meta( $user_id, 'bantu_video_history', $history );

		wp_send_json_success( array( 'message' => 'Watch recorded' ) );
	}
endif;
add_action( 'wp_ajax_bantu_record_watch', 'bantu_record_watch_callback' );

// AJAX: Add/Remove favorite
if ( ! function_exists( 'bantu_remove_favorite_callback' ) ) :
	/**
	 * AJAX callback to remove from favorites
	 *
	 * @return void
	 */
	function bantu_remove_favorite_callback() {
		check_ajax_referer( 'bantu_security_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'User not logged in' );
		}

		$user_id  = get_current_user_id();
		$video_id = isset( $_POST['video_id'] ) ? intval( $_POST['video_id'] ) : 0;

		if ( ! $video_id ) {
			wp_send_json_error( 'Invalid video ID' );
		}

		// Get existing favorites
		$favorites = get_user_meta( $user_id, 'bantu_favorites', true ) ?: array();

		// Remove from favorites
		if ( in_array( $video_id, $favorites, true ) ) {
			$favorites = array_filter( $favorites, function( $id ) use ( $video_id ) {
				return $id !== $video_id;
			} );
			update_user_meta( $user_id, 'bantu_favorites', $favorites );
			wp_send_json_success( array( 'message' => 'Removed from favorites' ) );
		} else {
			wp_send_json_error( 'Video not in favorites' );
		}
	}
endif;
add_action( 'wp_ajax_bantu_remove_favorite', 'bantu_remove_favorite_callback' );
