<?php
/**
 * BANTU Plus - Bunny.net Settings
 * Configuration page for Bunny.net CDN integration
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

// Register settings
add_action( 'admin_init', 'bantu_register_bunny_settings' );
function bantu_register_bunny_settings() {
	register_setting( 'bantu-settings-group', 'bantu_bunny_api_key' );
	register_setting( 'bantu-settings-group', 'bantu_bunny_library_id' );
	register_setting( 'bantu-settings-group', 'bantu_bunny_storage_zone' );
	register_setting( 'bantu-settings-group', 'bantu_bunny_cdn_url' );
	register_setting( 'bantu-settings-group', 'bantu_stripe_public_key' );
	register_setting( 'bantu-settings-group', 'bantu_stripe_secret_key' );
	register_setting( 'bantu-settings-group', 'bantu_stripe_webhook_secret' );
}

// Add admin menu
add_action( 'admin_menu', 'bantu_add_settings_menu' );
function bantu_add_settings_menu() {
	add_menu_page(
		'BANTU Plus Settings',
		'BANTU Plus',
		'manage_options',
		'bantu-settings',
		'bantu_settings_page',
		'dashicons-video-alt3',
		20
	);
}

/**
 * Settings page content
 */
function bantu_settings_page() {
	// Check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}
	?>

	<div class="wrap">
		<h1>BANTU Plus Settings</h1>
		<p>Configure your Bunny.net CDN and Stripe payment settings.</p>

		<form method="post" action="options.php" style="max-width: 600px; margin-top: 20px;">
			<?php settings_fields( 'bantu-settings-group' ); ?>

			<h2>Bunny.net Configuration</h2>
			<table class="form-table">
				<tr>
					<th><label for="bantu_bunny_api_key">Bunny.net API Key</label></th>
					<td>
						<input 
							type="password" 
							id="bantu_bunny_api_key" 
							name="bantu_bunny_api_key" 
							value="<?php echo esc_attr( get_option( 'bantu_bunny_api_key' ) ); ?>"
							class="regular-text"
						>
						<p class="description">Get this from your Bunny.net account (Account > API)</p>
					</td>
				</tr>

				<tr>
					<th><label for="bantu_bunny_library_id">Bunny.net Library ID</label></th>
					<td>
						<input 
							type="text" 
							id="bantu_bunny_library_id" 
							name="bantu_bunny_library_id" 
							value="<?php echo esc_attr( get_option( 'bantu_bunny_library_id' ) ); ?>"
							class="regular-text"
						>
						<p class="description">Your Bunny Stream video library ID</p>
					</td>
				</tr>

				<tr>
					<th><label for="bantu_bunny_storage_zone">Bunny.net Storage Zone</label></th>
					<td>
						<input 
							type="text" 
							id="bantu_bunny_storage_zone" 
							name="bantu_bunny_storage_zone" 
							value="<?php echo esc_attr( get_option( 'bantu_bunny_storage_zone' ) ); ?>"
							class="regular-text"
						>
						<p class="description">Your Bunny storage zone name (optional)</p>
					</td>
				</tr>

				<tr>
					<th><label for="bantu_bunny_cdn_url">Bunny.net CDN URL</label></th>
					<td>
						<input 
							type="text" 
							id="bantu_bunny_cdn_url" 
							name="bantu_bunny_cdn_url" 
							value="<?php echo esc_attr( get_option( 'bantu_bunny_cdn_url' ) ); ?>"
							class="regular-text"
							placeholder="https://your-cdn.b-cdn.net"
						>
						<p class="description">Your Bunny CDN pull zone URL</p>
					</td>
				</tr>
			</table>

			<h2 style="margin-top: 40px;">Stripe Configuration</h2>
			<table class="form-table">
				<tr>
					<th><label for="bantu_stripe_public_key">Stripe Public Key</label></th>
					<td>
						<input 
							type="text" 
							id="bantu_stripe_public_key" 
							name="bantu_stripe_public_key" 
							value="<?php echo esc_attr( get_option( 'bantu_stripe_public_key' ) ); ?>"
							class="regular-text"
						>
						<p class="description">Get this from your Stripe dashboard (publishable key)</p>
					</td>
				</tr>

				<tr>
					<th><label for="bantu_stripe_secret_key">Stripe Secret Key</label></th>
					<td>
						<input 
							type="password" 
							id="bantu_stripe_secret_key" 
							name="bantu_stripe_secret_key" 
							value="<?php echo esc_attr( get_option( 'bantu_stripe_secret_key' ) ); ?>"
							class="regular-text"
						>
						<p class="description">Get this from your Stripe dashboard (secret key)</p>
					</td>
				</tr>

				<tr>
					<th><label for="bantu_stripe_webhook_secret">Stripe Webhook Secret</label></th>
					<td>
						<input 
							type="password" 
							id="bantu_stripe_webhook_secret" 
							name="bantu_stripe_webhook_secret" 
							value="<?php echo esc_attr( get_option( 'bantu_stripe_webhook_secret' ) ); ?>"
							class="regular-text"
						>
						<p class="description">Webhook signing secret from Stripe</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>

		<div style="margin-top: 40px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
			<h3>Quick Start</h3>
			<ol>
				<li>Get your Bunny.net API key from <a href="https://bunnycdn.com" target="_blank">bunnycdn.com</a></li>
				<li>Create a Stream video library in your Bunny account</li>
				<li>Copy the Library ID and save it here</li>
				<li>Go to Videos → Bunny Upload to start uploading videos</li>
			</ol>
		</div>
	</div>

	<?php
}
