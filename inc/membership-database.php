<?php
/**
 * BANTU Plus - Membership Database Schema
 * Creates custom database tables for membership management
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

// Create tables on plugin activation
register_activation_hook( __FILE__, 'bantu_create_membership_tables' );

/**
 * Create custom membership tables
 */
function bantu_create_membership_tables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	// Memberships table
	$sql_memberships = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bantu_memberships (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id bigint(20) UNSIGNED NOT NULL,
		membership_level varchar(50) NOT NULL DEFAULT 'standard',
		stripe_subscription_id varchar(255),
		stripe_customer_id varchar(255),
		status varchar(20) NOT NULL DEFAULT 'active',
		trial_ends_at datetime,
		current_period_start datetime,
		current_period_end datetime,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY user_id (user_id),
		KEY stripe_customer_id (stripe_customer_id),
		KEY status (status)
	) $charset_collate;";

	// Payment history table
	$sql_payments = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bantu_payments (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id bigint(20) UNSIGNED NOT NULL,
		membership_id bigint(20) UNSIGNED,
		stripe_payment_id varchar(255),
		stripe_invoice_id varchar(255),
		amount decimal(10, 2) NOT NULL,
		currency varchar(3) DEFAULT 'USD',
		status varchar(20) NOT NULL DEFAULT 'pending',
		payment_method varchar(50),
		description text,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY user_id (user_id),
		KEY membership_id (membership_id),
		KEY status (status)
	) $charset_collate;";

	// Trials table
	$sql_trials = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bantu_trials (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id bigint(20) UNSIGNED NOT NULL,
		trial_days int(11) DEFAULT 7,
		started_at datetime DEFAULT CURRENT_TIMESTAMP,
		expires_at datetime NOT NULL,
		used tinyint(1) DEFAULT 0,
		PRIMARY KEY (id),
		KEY user_id (user_id),
		UNIQUE KEY user_trial (user_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql_memberships );
	dbDelta( $sql_payments );
	dbDelta( $sql_trials );
}

// Hook into WordPress initialization
add_action( 'wp_loaded', 'bantu_maybe_create_tables' );
function bantu_maybe_create_tables() {
	if ( get_option( 'bantu_db_version' ) !== '1.0' ) {
		bantu_create_membership_tables();
		update_option( 'bantu_db_version', '1.0' );
	}
}

/**
 * Get user membership
 *
 * @param int $user_id User ID
 * @return object|null Membership object or null
 */
function bantu_get_user_membership( $user_id ) {
	global $wpdb;
	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}bantu_memberships WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
			$user_id
		)
	);
}

/**
 * Create or update user membership
 *
 * @param int    $user_id User ID
 * @param array  $data Membership data
 * @return int|WP_Error Membership ID or error
 */
function bantu_create_membership( $user_id, $data = array() ) {
	global $wpdb;

	$defaults = array(
		'membership_level'      => 'standard',
		'stripe_subscription_id' => '',
		'stripe_customer_id'    => '',
		'status'                => 'active',
		'current_period_end'    => date( 'Y-m-d H:i:s', strtotime( '+30 days' ) ),
	);

	$data = wp_parse_args( $data, $defaults );

	// Check if membership exists
	$existing = bantu_get_user_membership( $user_id );

	if ( $existing ) {
		// Update existing
		$result = $wpdb->update(
			$wpdb->prefix . 'bantu_memberships',
			array(
				'membership_level'      => sanitize_text_field( $data['membership_level'] ),
				'stripe_subscription_id' => sanitize_text_field( $data['stripe_subscription_id'] ),
				'stripe_customer_id'    => sanitize_text_field( $data['stripe_customer_id'] ),
				'status'                => sanitize_text_field( $data['status'] ),
				'current_period_end'    => $data['current_period_end'],
			),
			array( 'user_id' => $user_id )
		);
		return $existing->id;
	} else {
		// Create new
		$result = $wpdb->insert(
			$wpdb->prefix . 'bantu_memberships',
			array(
				'user_id'               => intval( $user_id ),
				'membership_level'      => sanitize_text_field( $data['membership_level'] ),
				'stripe_subscription_id' => sanitize_text_field( $data['stripe_subscription_id'] ),
				'stripe_customer_id'    => sanitize_text_field( $data['stripe_customer_id'] ),
				'status'                => sanitize_text_field( $data['status'] ),
				'current_period_start'  => date( 'Y-m-d H:i:s' ),
				'current_period_end'    => $data['current_period_end'],
			)
		);

		if ( $result ) {
			// Also update user meta for compatibility
			update_user_meta( $user_id, 'bantu_membership_level', $data['membership_level'] );
			update_user_meta( $user_id, 'bantu_membership_expiry', strtotime( $data['current_period_end'] ) );
			return $wpdb->insert_id;
		}
	}

	return new WP_Error( 'db_error', 'Failed to create membership' );
}

/**
 * Record a payment
 *
 * @param int   $user_id User ID
 * @param array $data Payment data
 * @return int|WP_Error Payment ID or error
 */
function bantu_record_payment( $user_id, $data = array() ) {
	global $wpdb;

	$defaults = array(
		'membership_id'       => '',
		'stripe_payment_id'   => '',
		'stripe_invoice_id'   => '',
		'amount'              => 5.99,
		'currency'            => 'USD',
		'status'              => 'completed',
		'payment_method'      => 'stripe',
		'description'         => 'Monthly subscription',
	);

	$data = wp_parse_args( $data, $defaults );

	$result = $wpdb->insert(
		$wpdb->prefix . 'bantu_payments',
		array(
			'user_id'             => intval( $user_id ),
			'membership_id'       => intval( $data['membership_id'] ) ?: null,
			'stripe_payment_id'   => sanitize_text_field( $data['stripe_payment_id'] ),
			'stripe_invoice_id'   => sanitize_text_field( $data['stripe_invoice_id'] ),
			'amount'              => floatval( $data['amount'] ),
			'currency'            => sanitize_text_field( $data['currency'] ),
			'status'              => sanitize_text_field( $data['status'] ),
			'payment_method'      => sanitize_text_field( $data['payment_method'] ),
			'description'         => sanitize_textarea_field( $data['description'] ),
		)
	);

	return $result ? $wpdb->insert_id : new WP_Error( 'db_error', 'Failed to record payment' );
}

/**
 * Get payment history for user
 *
 * @param int $user_id User ID
 * @param int $limit Limit
 * @return array Payment records
 */
function bantu_get_user_payments( $user_id, $limit = 10 ) {
	global $wpdb;
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}bantu_payments WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
			$user_id,
			$limit
		)
	);
}
