<?php
/**
 * BANTU Plus - Admin Video Upload Page
 * Custom admin page for uploading videos to Bunny.net
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

// Register admin page
add_action( 'admin_menu', 'bantu_add_video_upload_menu' );
function bantu_add_video_upload_menu() {
	add_submenu_page(
		'edit.php?post_type=video',
		'Upload Video to Bunny',
		'Bunny Upload',
		'upload_files',
		'bantu-video-upload',
		'bantu_video_upload_page'
	);
}

/**
 * Admin page: Video upload form
 */
function bantu_video_upload_page() {
	// Check user capabilities
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_die( 'Unauthorized' );
	}

	// Handle form submission
	if ( isset( $_POST['bantu_upload_nonce'] ) && wp_verify_nonce( $_POST['bantu_upload_nonce'], 'bantu_video_upload' ) ) {
		bantu_handle_video_upload();
	}
	?>

	<div class="wrap">
		<h1>Upload Video to Bunny CDN</h1>
		<p>Upload a video file and it will be encoded and distributed via Bunny.net CDN.</p>

		<?php if ( isset( $_GET['message'] ) ) : ?>
			<div class="notice notice-success"><p>
				<?php
				switch ( $_GET['message'] ) {
					case 'success':
						esc_html_e( 'Video uploaded successfully!', 'twentytwentyfive' );
						break;
					case 'error':
						esc_html_e( 'Error uploading video. Please try again.', 'twentytwentyfive' );
						break;
				}
				?>
			</p></div>
		<?php endif; ?>

		<form method="post" enctype="multipart/form-data" style="max-width: 600px; margin-top: 20px;">
			<?php wp_nonce_field( 'bantu_video_upload', 'bantu_upload_nonce' ); ?>

			<table class="form-table">
				<tr>
					<th><label for="post_title">Video Title</label></th>
					<td>
						<input type="text" id="post_title" name="post_title" class="regular-text" required>
					</td>
				</tr>

				<tr>
					<th><label for="post_content">Description</label></th>
					<td>
						<textarea id="post_content" name="post_content" class="large-text" rows="5"></textarea>
					</td>
				</tr>

				<tr>
					<th><label for="video_category">Category</label></th>
					<td>
						<?php
						$categories = get_terms( array(
							'taxonomy'   => 'video_category',
							'hide_empty' => false,
						) );
						?>
						<select id="video_category" name="video_category">
							<option value="">-- Select Category --</option>
							<?php foreach ( $categories as $category ) : ?>
								<option value="<?php echo esc_attr( $category->term_id ); ?>">
									<?php echo esc_html( $category->name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th><label for="video_file">Video File (MP4, WebM, etc.)</label></th>
					<td>
						<input 
							type="file" 
							id="video_file" 
							name="video_file" 
							accept="video/*" 
							required
							style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
						>
						<p class="description">Max 5GB. Bunny.net will auto-encode to multiple bitrates (4K, 1080p, 720p, 480p, 360p).</p>
					</td>
				</tr>

				<tr>
					<th><label for="video_thumbnail">Thumbnail (Optional)</label></th>
					<td>
						<input 
							type="file" 
							id="video_thumbnail" 
							name="video_thumbnail" 
							accept="image/*"
							style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
						>
					</td>
				</tr>

				<tr>
					<th><label for="video_duration">Duration (minutes)</label></th>
					<td>
						<input 
							type="number" 
							id="video_duration" 
							name="video_duration" 
							min="0" 
							step="0.1"
							class="small-text"
						>
					</td>
				</tr>
			</table>

			<?php submit_button( 'Upload to Bunny' ); ?>
		</form>
	</div>

	<?php
}

/**
 * Handle video upload form submission
 */
function bantu_handle_video_upload() {
	// Validate nonce
	if ( ! isset( $_POST['bantu_upload_nonce'] ) || ! wp_verify_nonce( $_POST['bantu_upload_nonce'], 'bantu_video_upload' ) ) {
		wp_safe_remote_post( admin_url( 'edit.php?post_type=video&page=bantu-video-upload&message=error' ) );
		return;
	}

	// Validate required fields
	if ( empty( $_POST['post_title'] ) || empty( $_FILES['video_file'] ) ) {
		wp_safe_remote_post( admin_url( 'edit.php?post_type=video&page=bantu-video-upload&message=error' ) );
		return;
	}

	// Get form data
	$title      = sanitize_text_field( $_POST['post_title'] );
	$content    = isset( $_POST['post_content'] ) ? sanitize_textarea_field( $_POST['post_content'] ) : '';
	$category   = isset( $_POST['video_category'] ) ? intval( $_POST['video_category'] ) : 0;
	$duration   = isset( $_POST['video_duration'] ) ? floatval( $_POST['video_duration'] ) : 0;
	$video_file = $_FILES['video_file'];

	// Upload file to temporary directory
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	// Handle file upload
	$upload_overrides = array( 'test_form' => false );
	$movefile         = wp_handle_upload( $video_file, $upload_overrides );

	if ( isset( $movefile['error'] ) ) {
		wp_safe_remote_post( admin_url( 'edit.php?post_type=video&page=bantu-video-upload&message=error' ) );
		return;
	}

	// Upload to Bunny.net
	$bunny_response = bantu_upload_to_bunny( $movefile['file'], $title );

	if ( is_wp_error( $bunny_response ) ) {
		// Delete local file
		unlink( $movefile['file'] );
		wp_safe_remote_post( admin_url( 'edit.php?post_type=video&page=bantu-video-upload&message=error' ) );
		return;
	}

	// Create WordPress post
	$post_id = wp_insert_post( array(
		'post_type'    => 'video',
		'post_title'   => $title,
		'post_content' => $content,
		'post_status'  => 'publish',
	) );

	if ( is_wp_error( $post_id ) ) {
		unlink( $movefile['file'] );
		wp_safe_remote_post( admin_url( 'edit.php?post_type=video&page=bantu-video-upload&message=error' ) );
		return;
	}

	// Set category
	if ( $category > 0 ) {
		wp_set_object_terms( $post_id, $category, 'video_category' );
	}

	// Store Bunny metadata
	update_post_meta( $post_id, 'bunny_guid', $bunny_response['guid'] );
	update_post_meta( $post_id, 'bunny_library_id', defined( 'BUNNY_LIBRARY_ID' ) ? BUNNY_LIBRARY_ID : '' );
	update_post_meta( $post_id, 'video_duration', $duration );

	// Handle thumbnail
	if ( ! empty( $_FILES['video_thumbnail'] ) ) {
		$thumb_file = $_FILES['video_thumbnail'];
		$thumb_move = wp_handle_upload( $thumb_file, $upload_overrides );

		if ( ! isset( $thumb_move['error'] ) ) {
			$attachment_id = wp_insert_attachment( array(
				'guid'           => $thumb_move['url'],
				'post_mime_type' => $thumb_move['type'],
				'post_title'     => $title,
			), $thumb_move['file'], $post_id );

			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	// Clean up temp file
	unlink( $movefile['file'] );

	// Redirect with success message
	wp_safe_remote_post( admin_url( 'edit.php?post_type=video&page=bantu-video-upload&message=success' ) );
}

/**
 * Upload video to Bunny.net API
 *
 * @param string $file_path Local file path
 * @param string $title Video title
 * @return array|WP_Error Bunny response with GUID or error
 */
function bantu_upload_to_bunny( $file_path, $title ) {
	// Get API credentials from constants or options
	$api_key       = defined( 'BUNNY_API_KEY' ) ? BUNNY_API_KEY : get_option( 'bantu_bunny_api_key' );
	$library_id    = defined( 'BUNNY_LIBRARY_ID' ) ? BUNNY_LIBRARY_ID : get_option( 'bantu_bunny_library_id' );
	$storage_zone  = defined( 'BUNNY_STORAGE_ZONE' ) ? BUNNY_STORAGE_ZONE : get_option( 'bantu_bunny_storage_zone' );

	if ( empty( $api_key ) || empty( $library_id ) ) {
		return new WP_Error( 'missing_bunny_credentials', 'Bunny.net credentials not configured' );
	}

	// Generate unique filename
	$filename = sanitize_file_name( $title ) . '-' . time() . '.mp4';

	// Upload to Bunny.net
	$response = wp_remote_post( 'https://video.bunnycdn.com/library/' . $library_id . '/videos', array(
		'method'      => 'POST',
		'headers'     => array(
			'accept'       => 'application/json',
			'AccessKey'    => $api_key,
		),
		'body'        => array(
			'title' => $title,
		),
		'timeout'     => 300,
	) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $body['guid'] ) ) {
		return new WP_Error( 'bunny_error', 'Failed to create video object in Bunny.net' );
	}

	// Upload actual file via Bunny Stream API
	$file_response = wp_remote_post( 'https://video.bunnycdn.com/library/' . $library_id . '/videos/' . $body['guid'], array(
		'method'      => 'PUT',
		'headers'     => array(
			'AccessKey'        => $api_key,
			'Content-Type'     => 'application/octet-stream',
		),
		'body'        => file_get_contents( $file_path ),
		'timeout'     => 600,
	) );

	if ( is_wp_error( $file_response ) ) {
		return $file_response;
	}

	return array(
		'guid'  => $body['guid'],
		'video_id' => isset( $body['videoId'] ) ? $body['videoId'] : '',
	);
}
