<?php
/**
 * BANTU Plus - Stripe Payment Integration
 * Handles subscriptions, trials, and webhook processing
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

// Register shortcode
add_shortcode( 'bantu_stripe_checkout', 'bantu_stripe_checkout_shortcode' );

/**
 * Get Stripe API keys
 */
function bantu_get_stripe_keys() {
	return array(
		'public_key' => defined( 'STRIPE_PUBLIC_KEY' ) ? STRIPE_PUBLIC_KEY : get_option( 'bantu_stripe_public_key' ),
		'secret_key' => defined( 'STRIPE_SECRET_KEY' ) ? STRIPE_SECRET_KEY : get_option( 'bantu_stripe_secret_key' ),
	);
}

/**
 * Stripe checkout page shortcode
 */
function bantu_stripe_checkout_shortcode( $atts ) {
	// Require login
	if ( ! is_user_logged_in() ) {
		return '<div style="text-align: center; padding: 2rem;"><p style="color: #b3b3b3;">Please <a href="' . esc_url( wp_login_url() ) . '">sign in</a> to subscribe.</p></div>';
	}

	$user_id = get_current_user_id();
	$user = get_user_by( 'id', $user_id );
	$keys = bantu_get_stripe_keys();

	if ( empty( $keys['public_key'] ) ) {
		return '<div style="text-align: center; padding: 2rem; color: #b3b3b3;"><p>Payment system is not configured.</p></div>';
	}

	// Check for existing membership
	$membership = bantu_get_user_membership( $user_id );
	if ( $membership && $membership->status === 'active' ) {
		return '<div style="text-align: center; padding: 2rem;"><p style="color: #b3b3b3;">You already have an active subscription. <a href="' . esc_url( home_url( '/account' ) ) . '">Manage it here</a>.</p></div>';
	}

	ob_start();
	?>
	<div class="bantu-checkout-container" style="max-width: 600px; margin: 3rem auto; padding: 2rem; background: #1a1f3a; border-radius: 8px;">
		<h2 style="color: #ffffff; margin-bottom: 2rem;">Choose Your Plan</h2>

		<!-- Plans Selection -->
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
			<!-- Standard Plan -->
			<div class="plan-card" style="border: 2px solid #404040; padding: 1.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;" data-plan="standard" data-price="5.99">
				<h3 style="color: #e50914; margin: 0 0 1rem 0;">Standard</h3>
				<p style="color: #b3b3b3; margin: 0 0 0.5rem 0;">Access all videos</p>
				<div style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 1rem 0;">$5.99<span style="font-size: 1rem; color: #b3b3b3;">/month</span></div>
				<ul style="list-style: none; padding: 0; margin: 1.5rem 0; color: #b3b3b3;">
					<li style="margin-bottom: 0.75rem;">✓ All videos included</li>
					<li style="margin-bottom: 0.75rem;">✓ HD streaming</li>
					<li style="margin-bottom: 0.75rem;">✓ 7-day free trial</li>
				</ul>
				<button type="button" class="select-plan btn btn-primary" style="width: 100%;">Select Plan</button>
			</div>

			<!-- Premium Plan -->
			<div class="plan-card" style="border: 2px solid #e50914; padding: 1.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; position: relative;" data-plan="premium" data-price="9.99">
				<span style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #e50914; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">POPULAR</span>
				<h3 style="color: #e50914; margin: 0 0 1rem 0;">Premium</h3>
				<p style="color: #b3b3b3; margin: 0 0 0.5rem 0;">Everything in Standard</p>
				<div style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 1rem 0;">$9.99<span style="font-size: 1rem; color: #b3b3b3;">/month</span></div>
				<ul style="list-style: none; padding: 0; margin: 1.5rem 0; color: #b3b3b3;">
					<li style="margin-bottom: 0.75rem;">✓ All videos in 4K</li>
					<li style="margin-bottom: 0.75rem;">✓ Offline downloads</li>
					<li style="margin-bottom: 0.75rem;">✓ 7-day free trial</li>
				</ul>
				<button type="button" class="select-plan btn btn-primary" style="width: 100%;">Select Plan</button>
			</div>
		</div>

		<!-- Checkout Form -->
		<form id="bantu-payment-form" style="display: none;">
			<?php wp_nonce_field( 'bantu_checkout_nonce', 'bantu_nonce' ); ?>
			<input type="hidden" id="selected_plan" name="plan" value="standard">
			<input type="hidden" id="selected_price" name="price" value="5.99">

			<div style="margin-bottom: 2rem;">
				<h3 style="color: #ffffff; margin: 0 0 1rem 0;">Billing Information</h3>

				<div style="margin-bottom: 1rem;">
					<label style="color: #b3b3b3; display: block; margin-bottom: 0.5rem;">Email</label>
					<input type="email" value="<?php echo esc_attr( $user->user_email ); ?>" readonly style="width: 100%; padding: 0.75rem; background: #2d3354; border: 1px solid #404040; border-radius: 4px; color: #b3b3b3;">
				</div>

				<div id="card-element" style="padding: 0.75rem; background: #2d3354; border: 1px solid #404040; border-radius: 4px; color: #ffffff;"></div>
				<div id="card-errors" role="alert" style="color: #ff6b6b; margin-top: 0.5rem;"></div>
			</div>

			<!-- Trial Info -->
			<div style="background: #2d3354; padding: 1rem; border-radius: 4px; margin-bottom: 2rem;">
				<p style="color: #b3b3b3; margin: 0;">7-day free trial included. Cancel anytime before trial ends with no charges.</p>
			</div>

			<button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Start 7-Day Free Trial</button>
			<button type="button" class="btn btn-secondary" onclick="document.getElementById('bantu-payment-form').style.display='none'; document.querySelectorAll('.plan-card').forEach(el => el.style.display='grid');" style="width: 100%; padding: 1rem; margin-top: 0.5rem;">Back</button>
		</form>
	</div>

	<script src="https://js.stripe.com/v3/"></script>
	<script>
	(function() {
		const stripe = Stripe('<?php echo esc_js( $keys['public_key'] ); ?>');
		const elements = stripe.elements();
		const cardElement = elements.create('card', {
			style: {
				base: {
					color: '#ffffff',
					fontFamily: 'system-ui, -apple-system, sans-serif',
					fontSize: '16px',
					'::placeholder': {
						color: '#b3b3b3',
					},
				},
				invalid: {
					color: '#ff6b6b',
				},
			},
		});

		cardElement.mount('#card-element');

		// Plan selection
		document.querySelectorAll('.select-plan').forEach(btn => {
			btn.addEventListener('click', function() {
				const card = this.closest('.plan-card');
				document.getElementById('selected_plan').value = card.dataset.plan;
				document.getElementById('selected_price').value = card.dataset.price;
				document.querySelectorAll('.plan-card').forEach(el => el.style.display = 'none');
				document.getElementById('bantu-payment-form').style.display = 'block';
				window.scrollTo(0, 0);
			});
		});

		// Form submission
		document.getElementById('bantu-payment-form').addEventListener('submit', async function(e) {
			e.preventDefault();

			const formData = new FormData(this);
			const nonce = formData.get('bantu_nonce');
			const plan = formData.get('plan');
			const price = formData.get('price');

			// Create payment method
			const { paymentMethod, error } = await stripe.createPaymentMethod({
				type: 'card',
				card: cardElement,
			});

			if (error) {
				document.getElementById('card-errors').textContent = error.message;
				return;
			}

			// Send to server
			const data = new FormData();
			data.append('action', 'bantu_process_stripe_payment');
			data.append('payment_method_id', paymentMethod.id);
			data.append('plan', plan);
			data.append('price', price);
			data.append('nonce', nonce);

			try {
				const response = await fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
					method: 'POST',
					body: data,
				});

				const result = await response.json();

				if (result.success) {
					alert('Subscription created successfully! Your 7-day trial has started.');
					window.location.href = '<?php echo esc_url( home_url( '/account' ) ); ?>';
				} else {
					document.getElementById('card-errors').textContent = result.data || 'Payment failed';
				}
			} catch (err) {
				document.getElementById('card-errors').textContent = 'Network error: ' + err.message;
			}
		});
	})();
	</script>
	<?php
	return ob_get_clean();
}

/**
 * AJAX: Process Stripe payment
 */
add_action( 'wp_ajax_bantu_process_stripe_payment', 'bantu_process_stripe_payment_callback' );
function bantu_process_stripe_payment_callback() {
	check_ajax_referer( 'bantu_checkout_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( 'Not logged in' );
	}

	$user_id = get_current_user_id();
	$payment_method_id = isset( $_POST['payment_method_id'] ) ? sanitize_text_field( $_POST['payment_method_id'] ) : '';
	$plan = isset( $_POST['plan'] ) ? sanitize_text_field( $_POST['plan'] ) : 'standard';
	$price = isset( $_POST['price'] ) ? floatval( $_POST['price'] ) : 5.99;

	if ( empty( $payment_method_id ) ) {
		wp_send_json_error( 'Payment method required' );
	}

	// TODO: Implement Stripe API call here
	// For now, we'll create a trial membership directly
	
	// Create trial
	$trial_days = 7;
	$expires_at = date( 'Y-m-d H:i:s', strtotime( "+$trial_days days" ) );

	global $wpdb;
	$wpdb->insert(
		$wpdb->prefix . 'bantu_trials',
		array(
			'user_id'    => $user_id,
			'trial_days' => $trial_days,
			'expires_at' => $expires_at,
			'used'       => 1,
		),
		array( '%d', '%d', '%s', '%d' )
	);

	// Create membership with trial
	$membership_id = bantu_create_membership( $user_id, array(
		'membership_level'      => $plan,
		'status'                => 'trialing',
		'current_period_end'    => $expires_at,
	) );

	if ( is_wp_error( $membership_id ) ) {
		wp_send_json_error( $membership_id->get_error_message() );
	}

	// Record payment (future charge)
	bantu_record_payment( $user_id, array(
		'membership_id'   => $membership_id,
		'amount'          => $price,
		'status'          => 'pending', // Will be charged after trial
		'payment_method'  => 'stripe',
		'description'     => ucfirst( $plan ) . ' plan - Trial period',
	) );

	wp_send_json_success( array(
		'message' => 'Trial started successfully',
	) );
}

/**
 * Stripe webhook handler (for production use)
 */
add_action( 'init', 'bantu_stripe_webhook_handler' );
function bantu_stripe_webhook_handler() {
	// Only process webhook requests
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset( $_GET['bantu_webhook'] ) ) {
		return;
	}

	$payload = file_get_contents( 'php://input' );
	$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

	// TODO: Verify webhook signature with your Stripe endpoint secret
	// For now, just acknowledge the webhook
	http_response_code( 200 );
	exit( json_encode( array( 'received' => true ) ) );
}
