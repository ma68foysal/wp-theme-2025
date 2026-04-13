<?php
/**
 * BANTU Plus - Authentication Shortcodes
 * Custom login and register forms using WordPress core functions
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

// Register shortcodes
add_shortcode( 'bantu_login_form', 'bantu_login_form_shortcode' );
add_shortcode( 'bantu_register_form', 'bantu_register_form_shortcode' );
add_shortcode( 'bantu_account_dashboard', 'bantu_account_dashboard_shortcode' );

/**
 * Login form shortcode
 */
function bantu_login_form_shortcode( $atts ) {
	// Redirect if already logged in
	if ( is_user_logged_in() ) {
		return '<div style="text-align: center; padding: 2rem;"><p style="color: #b3b3b3;">You are already logged in. <a href="' . esc_url( home_url( '/account' ) ) . '">Go to account</a></p></div>';
	}

	ob_start();
	?>
	<div class="bantu-login-container" style="max-width: 500px; margin: 3rem auto; padding: 2rem; background: #1a1f3a; border-radius: 8px;">
		<h2 style="margin: 0 0 2rem 0; color: #ffffff;">Sign In</h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" class="bantu-form">
			<?php wp_nonce_field( 'bantu_login_nonce', 'bantu_nonce' ); ?>
			<input type="hidden" name="action" value="bantu_login_user">

			<div class="form-group">
				<label for="user_login">Email or Username</label>
				<input 
					type="text" 
					id="user_login" 
					name="user_login" 
					required
					placeholder="Enter your email or username"
				>
			</div>

			<div class="form-group">
				<label for="user_password">Password</label>
				<input 
					type="password" 
					id="user_password" 
					name="user_password" 
					required
					placeholder="Enter your password"
				>
			</div>

			<div class="form-group">
				<label>
					<input type="checkbox" name="remember_me" value="1">
					<span style="color: #b3b3b3; margin-left: 0.5rem;">Remember me</span>
				</label>
			</div>

			<button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>

			<p style="text-align: center; color: #b3b3b3; margin-top: 1.5rem; margin-bottom: 0;">
				Don&apos;t have an account? <a href="<?php echo esc_url( home_url( '/register' ) ); ?>" style="color: #e50914;">Create one</a>
			</p>

			<p style="text-align: center; color: #b3b3b3; margin-top: 0.5rem;">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="color: #b3b3b3; text-decoration: underline;">Forgot password?</a>
			</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Register form shortcode
 */
function bantu_register_form_shortcode( $atts ) {
	// Redirect if already logged in
	if ( is_user_logged_in() ) {
		return '<div style="text-align: center; padding: 2rem;"><p style="color: #b3b3b3;">You are already logged in. <a href="' . esc_url( home_url( '/account' ) ) . '">Go to account</a></p></div>';
	}

	// Check if registration is enabled
	if ( ! get_option( 'users_can_register' ) ) {
		return '<div style="text-align: center; padding: 2rem;"><p style="color: #b3b3b3;">Registration is currently disabled.</p></div>';
	}

	ob_start();
	?>
	<div class="bantu-register-container" style="max-width: 500px; margin: 3rem auto; padding: 2rem; background: #1a1f3a; border-radius: 8px;">
		<h2 style="margin: 0 0 2rem 0; color: #ffffff;">Create Account</h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" class="bantu-form">
			<?php wp_nonce_field( 'bantu_register_nonce', 'bantu_nonce' ); ?>
			<input type="hidden" name="action" value="bantu_register_user">

			<div class="form-group">
				<label for="user_email">Email Address</label>
				<input 
					type="email" 
					id="user_email" 
					name="user_email" 
					required
					placeholder="Enter your email"
				>
			</div>

			<div class="form-group">
				<label for="user_login_reg">Username</label>
				<input 
					type="text" 
					id="user_login_reg" 
					name="user_login" 
					required
					placeholder="Choose a username"
				>
			</div>

			<div class="form-group">
				<label for="user_password_reg">Password</label>
				<input 
					type="password" 
					id="user_password_reg" 
					name="user_password" 
					required
					placeholder="Create a password (min 8 characters)"
				>
				<p style="font-size: 0.85rem; color: #b3b3b3; margin-top: 0.5rem;">Password must be at least 8 characters.</p>
			</div>

			<div class="form-group">
				<label for="user_password_confirm">Confirm Password</label>
				<input 
					type="password" 
					id="user_password_confirm" 
					name="user_password_confirm" 
					required
					placeholder="Confirm your password"
				>
			</div>

			<div class="form-group">
				<label>
					<input type="checkbox" name="accept_terms" value="1" required>
					<span style="color: #b3b3b3; margin-left: 0.5rem;">I agree to the <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>" style="color: #e50914;">Terms of Service</a></span>
				</label>
			</div>

			<button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>

			<p style="text-align: center; color: #b3b3b3; margin-top: 1.5rem; margin-bottom: 0;">
				Already have an account? <a href="<?php echo esc_url( wp_login_url() ); ?>" style="color: #e50914;">Sign in</a>
			</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Account dashboard shortcode
 */
function bantu_account_dashboard_shortcode( $atts ) {
	// Require login
	if ( ! is_user_logged_in() ) {
		return '<div style="text-align: center; padding: 2rem;"><p style="color: #b3b3b3;">Please <a href="' . esc_url( wp_login_url() ) . '">sign in</a> to view your account.</p></div>';
	}

	$user_id = get_current_user_id();
	$user = get_user_by( 'id', $user_id );
	$membership = bantu_get_user_membership( $user_id );
	$payments = bantu_get_user_payments( $user_id, 5 );

	ob_start();
	?>
	<div class="bantu-account-dashboard container" style="max-width: 1000px; margin: 2rem auto; padding: 2rem;">
		<h1 style="margin-bottom: 2rem; color: #ffffff;">My Account</h1>

		<!-- Profile Section -->
		<section style="background: #1a1f3a; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
			<h2 style="color: #e50914; margin-bottom: 1.5rem;">Profile Information</h2>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
				<div>
					<label style="color: #b3b3b3; font-size: 0.9rem;">Email</label>
					<p style="color: #ffffff; margin: 0.5rem 0 0 0;"><?php echo esc_html( $user->user_email ); ?></p>
				</div>
				<div>
					<label style="color: #b3b3b3; font-size: 0.9rem;">Username</label>
					<p style="color: #ffffff; margin: 0.5rem 0 0 0;"><?php echo esc_html( $user->user_login ); ?></p>
				</div>
				<div>
					<label style="color: #b3b3b3; font-size: 0.9rem;">Member Since</label>
					<p style="color: #ffffff; margin: 0.5rem 0 0 0;"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) ) ); ?></p>
				</div>
			</div>
			<a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>" class="btn btn-secondary" style="margin-top: 1.5rem;">Edit Profile</a>
		</section>

		<!-- Membership Section -->
		<?php if ( $membership ) : ?>
			<section style="background: #1a1f3a; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
				<h2 style="color: #e50914; margin-bottom: 1.5rem;">Membership</h2>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
					<div>
						<label style="color: #b3b3b3; font-size: 0.9rem;">Current Level</label>
						<p style="color: #e50914; margin: 0.5rem 0 0 0; font-weight: 600; text-transform: capitalize;"><?php echo esc_html( $membership->membership_level ); ?></p>
					</div>
					<div>
						<label style="color: #b3b3b3; font-size: 0.9rem;">Status</label>
						<p style="color: #ffffff; margin: 0.5rem 0 0 0; text-transform: capitalize;<?php echo $membership->status === 'active' ? ' color: #4ade80;' : ''; ?>"><?php echo esc_html( $membership->status ); ?></p>
					</div>
					<div>
						<label style="color: #b3b3b3; font-size: 0.9rem;">Renewal Date</label>
						<p style="color: #ffffff; margin: 0.5rem 0 0 0;"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $membership->current_period_end ) ) ); ?></p>
					</div>
				</div>
				<div style="margin-top: 1.5rem;">
					<a href="<?php echo esc_url( home_url( '/subscribe' ) ); ?>" class="btn btn-primary">Upgrade Plan</a>
					<a href="#" class="btn btn-secondary" style="margin-left: 1rem;">Cancel Subscription</a>
				</div>
			</section>
		<?php else : ?>
			<section style="background: #1a1f3a; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
				<h2 style="color: #b3b3b3; margin-bottom: 1.5rem;">No Active Membership</h2>
				<p style="color: #b3b3b3; margin-bottom: 2rem;">Subscribe now to access premium content.</p>
				<a href="<?php echo esc_url( home_url( '/subscribe' ) ); ?>" class="btn btn-primary">Get Started</a>
			</section>
		<?php endif; ?>

		<!-- Payment History -->
		<?php if ( ! empty( $payments ) ) : ?>
			<section style="background: #1a1f3a; padding: 2rem; border-radius: 8px;">
				<h2 style="color: #e50914; margin-bottom: 1.5rem;">Recent Payments</h2>
				<table style="width: 100%; color: #b3b3b3; border-collapse: collapse;">
					<thead>
						<tr style="border-bottom: 1px solid #404040;">
							<th style="text-align: left; padding: 1rem; color: #ffffff;">Date</th>
							<th style="text-align: left; padding: 1rem; color: #ffffff;">Amount</th>
							<th style="text-align: left; padding: 1rem; color: #ffffff;">Description</th>
							<th style="text-align: left; padding: 1rem; color: #ffffff;">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $payments as $payment ) : ?>
							<tr style="border-bottom: 1px solid #404040;">
								<td style="padding: 1rem;"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $payment->created_at ) ) ); ?></td>
								<td style="padding: 1rem;"><?php echo esc_html( $payment->currency . ' ' . $payment->amount ); ?></td>
								<td style="padding: 1rem;"><?php echo esc_html( $payment->description ); ?></td>
								<td style="padding: 1rem;">
									<span style="text-transform: capitalize; color: <?php echo $payment->status === 'completed' ? '#4ade80' : '#fbbf24'; ?>;">
										<?php echo esc_html( $payment->status ); ?>
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</section>
		<?php endif; ?>

		<!-- Logout -->
		<div style="margin-top: 3rem; text-align: center;">
			<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-secondary">Sign Out</a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// AJAX: Login user
add_action( 'wp_ajax_nopriv_bantu_login_user', 'bantu_login_user_callback' );
function bantu_login_user_callback() {
	check_ajax_referer( 'bantu_login_nonce', 'bantu_nonce' );

	$username = isset( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '';
	$password = isset( $_POST['user_password'] ) ? $_POST['user_password'] : ''; // Don't sanitize password

	if ( empty( $username ) || empty( $password ) ) {
		wp_send_json_error( 'Username and password are required.' );
	}

	$user = wp_signon( array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => isset( $_POST['remember_me'] ),
	), false );

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( $user->get_error_message() );
	}

	wp_send_json_success( array(
		'redirect' => home_url( '/account' ),
	) );
}

// AJAX: Register user
add_action( 'wp_ajax_nopriv_bantu_register_user', 'bantu_register_user_callback' );
function bantu_register_user_callback() {
	check_ajax_referer( 'bantu_register_nonce', 'bantu_nonce' );

	$email    = isset( $_POST['user_email'] ) ? sanitize_email( $_POST['user_email'] ) : '';
	$username = isset( $_POST['user_login'] ) ? sanitize_user( $_POST['user_login'] ) : '';
	$password = isset( $_POST['user_password'] ) ? $_POST['user_password'] : '';
	$confirm  = isset( $_POST['user_password_confirm'] ) ? $_POST['user_password_confirm'] : '';

	// Validation
	if ( empty( $email ) || empty( $username ) || empty( $password ) ) {
		wp_send_json_error( 'All fields are required.' );
	}

	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( 'Password must be at least 8 characters.' );
	}

	if ( $password !== $confirm ) {
		wp_send_json_error( 'Passwords do not match.' );
	}

	if ( email_exists( $email ) ) {
		wp_send_json_error( 'Email already exists.' );
	}

	if ( username_exists( $username ) ) {
		wp_send_json_error( 'Username already exists.' );
	}

	// Create user
	$user_id = wp_create_user( $username, $password, $email );

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( $user_id->get_error_message() );
	}

	// Log user in
	wp_signon( array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => false,
	), false );

	wp_send_json_success( array(
		'redirect' => home_url( '/account' ),
	) );
}
