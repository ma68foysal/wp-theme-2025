<?php
/**
 * BANTU Plus - Watermark & Security Settings
 * Admin settings page for video watermarking and anti-piracy features
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register Watermark Settings Menu
add_action( 'admin_menu', 'bantu_register_watermark_menu' );
function bantu_register_watermark_menu() {
	add_submenu_page(
		'bantu-settings',
		'Watermark & Security',
		'Watermark & Security',
		'manage_options',
		'bantu-watermark-settings',
		'bantu_render_watermark_settings_page'
	);
}

// Render Watermark Settings Page
function bantu_render_watermark_settings_page() {
	?>
	<div class="wrap">
		<h1>BANTU Plus - Watermark & Video Protection</h1>
		
		<?php settings_errors(); ?>
		
		<form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields( 'bantu-watermark-group' ); ?>
			
			<!-- Watermark Settings -->
			<div class="postbox" style="margin-top: 20px;">
				<h2 class="hndle" style="background: #e50914; color: white; padding: 15px; margin: 0;">
					<span>Video Watermark Settings</span>
				</h2>
				<div class="inside" style="padding: 20px;">
					
					<table class="form-table">
						<tr>
							<th scope="row"><label for="bantu_watermark_enabled">Enable Watermark Protection</label></th>
							<td>
								<input type="checkbox" id="bantu_watermark_enabled" name="bantu_watermark_enabled" value="1" <?php checked( get_option( 'bantu_watermark_enabled' ), 1 ); ?>>
								<p class="description">Enable watermarking on all videos to prevent piracy</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_watermark_text">Watermark Text</label></th>
							<td>
								<input type="text" id="bantu_watermark_text" name="bantu_watermark_text" value="<?php echo esc_attr( get_option( 'bantu_watermark_text' ) ); ?>" class="regular-text" placeholder="e.g., CONFIDENTIAL or {username}">
								<p class="description">Use {username} to personalize with viewer's login name</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_watermark_position">Watermark Position</label></th>
							<td>
								<select id="bantu_watermark_position" name="bantu_watermark_position" class="regular-text">
									<option value="top-left" <?php selected( get_option( 'bantu_watermark_position' ), 'top-left' ); ?>>Top Left</option>
									<option value="top-center" <?php selected( get_option( 'bantu_watermark_position' ), 'top-center' ); ?>>Top Center</option>
									<option value="top-right" <?php selected( get_option( 'bantu_watermark_position' ), 'top-right' ); ?>>Top Right</option>
									<option value="middle-left" <?php selected( get_option( 'bantu_watermark_position' ), 'middle-left' ); ?>>Middle Left</option>
									<option value="center" <?php selected( get_option( 'bantu_watermark_position' ), 'center' ); ?>>Center</option>
									<option value="middle-right" <?php selected( get_option( 'bantu_watermark_position' ), 'middle-right' ); ?>>Middle Right</option>
									<option value="bottom-left" <?php selected( get_option( 'bantu_watermark_position' ), 'bottom-left' ); ?>>Bottom Left</option>
									<option value="bottom-center" <?php selected( get_option( 'bantu_watermark_position' ), 'bottom-center' ); ?>>Bottom Center</option>
									<option value="bottom-right" <?php selected( get_option( 'bantu_watermark_position' ), 'bottom-right' ); ?>>Bottom Right</option>
									<option value="tiled" <?php selected( get_option( 'bantu_watermark_position' ), 'tiled' ); ?>>Tiled (Repeating)</option>
								</select>
								<p class="description">Select where the watermark should appear on videos</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_watermark_opacity">Watermark Opacity (%)</label></th>
							<td>
								<input type="range" id="bantu_watermark_opacity" name="bantu_watermark_opacity" min="5" max="100" value="<?php echo esc_attr( get_option( 'bantu_watermark_opacity', 40 ) ); ?>" oninput="document.getElementById('opacity-value').innerText = this.value + '%'">
								<span id="opacity-value" style="margin-left: 10px;"><?php echo esc_html( get_option( 'bantu_watermark_opacity', 40 ) ); ?>%</span>
								<p class="description">Lower values = more transparent (5-100%)</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_watermark_logo">Logo Image (Optional)</label></th>
							<td>
								<?php
									$logo_id = get_option( 'bantu_watermark_logo_id' );
									if ( $logo_id ) {
										echo wp_get_attachment_image( $logo_id, array( 150, 150 ) );
										echo '<br><br>';
									}
								?>
								<input type="button" class="button" value="Upload/Select Logo" onclick="bantuOpenMediaUploader('bantu_watermark_logo')">
								<input type="hidden" id="bantu_watermark_logo" name="bantu_watermark_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
								<p class="description">Optional: Add a logo/image watermark</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
			<!-- Anti-Piracy Features -->
			<div class="postbox" style="margin-top: 20px;">
				<h2 class="hndle" style="background: #FFC107; color: black; padding: 15px; margin: 0;">
					<span>Anti-Piracy Features</span>
				</h2>
				<div class="inside" style="padding: 20px;">
					
					<table class="form-table">
						<tr>
							<th scope="row"><label for="bantu_disable_download">Disable Video Download</label></th>
							<td>
								<input type="checkbox" id="bantu_disable_download" name="bantu_disable_download" value="1" <?php checked( get_option( 'bantu_disable_download' ), 1 ); ?>>
								<p class="description">Prevent users from downloading videos</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_disable_rightclick">Disable Right-Click Menu</label></th>
							<td>
								<input type="checkbox" id="bantu_disable_rightclick" name="bantu_disable_rightclick" value="1" <?php checked( get_option( 'bantu_disable_rightclick' ), 1 ); ?>>
								<p class="description">Block right-click on video player to prevent "Save video as"</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_disable_screenrecord">Disable Screen Recording</label></th>
							<td>
								<input type="checkbox" id="bantu_disable_screenrecord" name="bantu_disable_screenrecord" value="1" <?php checked( get_option( 'bantu_disable_screenrecord' ), 1 ); ?>>
								<p class="description">Block screen recording, AirPlay, and Chromecast</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_geo_restrict">Enable Geo-Restriction</label></th>
							<td>
								<input type="checkbox" id="bantu_geo_restrict" name="bantu_geo_restrict" value="1" <?php checked( get_option( 'bantu_geo_restrict' ), 1 ); ?>>
								<p class="description">Restrict video access by geography (requires Bunny.net support)</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_geo_countries">Allowed Countries (if restricted)</label></th>
							<td>
								<input type="text" id="bantu_geo_countries" name="bantu_geo_countries" value="<?php echo esc_attr( get_option( 'bantu_geo_countries' ) ); ?>" class="regular-text" placeholder="US,GB,CA,AU (comma-separated ISO codes)">
								<p class="description">ISO country codes only</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
			<!-- Security Features -->
			<div class="postbox" style="margin-top: 20px;">
				<h2 class="hndle" style="background: #2196F3; color: white; padding: 15px; margin: 0;">
					<span>Security Settings</span>
				</h2>
				<div class="inside" style="padding: 20px;">
					
					<table class="form-table">
						<tr>
							<th scope="row"><label for="bantu_token_expiry">Token Expiry (minutes)</label></th>
							<td>
								<input type="number" id="bantu_token_expiry" name="bantu_token_expiry" value="<?php echo esc_attr( get_option( 'bantu_token_expiry', 120 ) ); ?>" min="5" max="1440">
								<p class="description">How long video access tokens remain valid (5-1440 minutes)</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_enable_logging">Enable Access Logging</label></th>
							<td>
								<input type="checkbox" id="bantu_enable_logging" name="bantu_enable_logging" value="1" <?php checked( get_option( 'bantu_enable_logging' ), 1 ); ?>>
								<p class="description">Log all video access attempts for security monitoring</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_max_concurrent">Max Concurrent Streams</label></th>
							<td>
								<input type="number" id="bantu_max_concurrent" name="bantu_max_concurrent" value="<?php echo esc_attr( get_option( 'bantu_max_concurrent', 2 ) ); ?>" min="1" max="10">
								<p class="description">Limit how many devices can stream same account simultaneously</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bantu_ip_whitelist">IP Whitelist (Optional)</label></th>
							<td>
								<textarea id="bantu_ip_whitelist" name="bantu_ip_whitelist" class="regular-text" rows="4" placeholder="192.168.1.1&#10;203.0.113.0&#10;(one IP per line, leave empty to allow all)"><?php echo esc_textarea( get_option( 'bantu_ip_whitelist' ) ); ?></textarea>
								<p class="description">Restrict video access to specific IPs (advanced)</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
			<?php submit_button( 'Save Settings', 'primary', 'submit', true ); ?>
		</form>
	</div>
	
	<script>
		function bantuOpenMediaUploader( fieldId ) {
			const frame = wp.media( {
				title: 'Select Logo Image',
				button: { text: 'Use Image' },
				multiple: false,
				library: { type: 'image' }
			} );
			
			frame.on( 'select', function() {
				const attachment = frame.state().get( 'selection' ).first().toJSON();
				document.getElementById( fieldId ).value = attachment.id;
				location.reload();
			} );
			
			frame.open();
		}
	</script>
	<?php
}

// Register Settings
add_action( 'admin_init', 'bantu_register_watermark_settings' );
function bantu_register_watermark_settings() {
	register_setting( 'bantu-watermark-group', 'bantu_watermark_enabled' );
	register_setting( 'bantu-watermark-group', 'bantu_watermark_text' );
	register_setting( 'bantu-watermark-group', 'bantu_watermark_position' );
	register_setting( 'bantu-watermark-group', 'bantu_watermark_opacity' );
	register_setting( 'bantu-watermark-group', 'bantu_watermark_logo_id' );
	register_setting( 'bantu-watermark-group', 'bantu_disable_download' );
	register_setting( 'bantu-watermark-group', 'bantu_disable_rightclick' );
	register_setting( 'bantu-watermark-group', 'bantu_disable_screenrecord' );
	register_setting( 'bantu-watermark-group', 'bantu_geo_restrict' );
	register_setting( 'bantu-watermark-group', 'bantu_geo_countries' );
	register_setting( 'bantu-watermark-group', 'bantu_token_expiry' );
	register_setting( 'bantu-watermark-group', 'bantu_enable_logging' );
	register_setting( 'bantu-watermark-group', 'bantu_max_concurrent' );
	register_setting( 'bantu-watermark-group', 'bantu_ip_whitelist' );
}

// Helper function to get watermarked HLS URL
function bantu_get_watermarked_hls_url( $bunny_guid, $user_id = 0 ) {
	if ( ! $bunny_guid ) {
		return '';
	}

	$cdn_url = get_option( 'bantu_bunny_cdn_url' );
	if ( ! $cdn_url ) {
		return '';
	}

	// Base HLS URL
	$hls_url = $cdn_url . '/' . $bunny_guid . '/playlist.m3u8';

	// Add watermark parameters if enabled
	if ( get_option( 'bantu_watermark_enabled' ) ) {
		$watermark_text = get_option( 'bantu_watermark_text', 'CONFIDENTIAL' );
		
		// Replace {username} with actual username
		if ( $user_id && strpos( $watermark_text, '{username}' ) !== false ) {
			$user = get_userdata( $user_id );
			if ( $user ) {
				$watermark_text = str_replace( '{username}', $user->user_login, $watermark_text );
			}
		}

		// Add query parameters for watermarking
		$params = array(
			'token'     => bantu_generate_video_token( $bunny_guid, $user_id ),
			'watermark' => urlencode( $watermark_text ),
			'position'  => get_option( 'bantu_watermark_position', 'center' ),
			'opacity'   => get_option( 'bantu_watermark_opacity', 40 ),
		);

		$hls_url .= '?' . http_build_query( $params );
	}

	return $hls_url;
}

// Generate secure video token
function bantu_generate_video_token( $video_id, $user_id = 0 ) {
	$expiry  = time() + ( get_option( 'bantu_token_expiry', 120 ) * 60 );
	$token_data = array(
		'video_id' => $video_id,
		'user_id'  => $user_id,
		'ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
		'expiry'   => $expiry,
	);

	// Sign the token with a secret key
	$secret = wp_salt( 'auth' );
	$token  = base64_encode( json_encode( $token_data ) );
	$signature = hash_hmac( 'sha256', $token, $secret );

	return $token . '.' . $signature;
}

// Verify video token
function bantu_verify_video_token( $token ) {
	if ( ! $token || strpos( $token, '.' ) === false ) {
		return false;
	}

	list( $token_data, $signature ) = explode( '.', $token );
	
	$secret = wp_salt( 'auth' );
	$expected_signature = hash_hmac( 'sha256', $token_data, $secret );

	if ( ! hash_equals( $signature, $expected_signature ) ) {
		return false;
	}

	$data = json_decode( base64_decode( $token_data ), true );
	
	if ( ! $data || $data['expiry'] < time() ) {
		return false;
	}

	return $data;
}
