<?php
/**
 * Member Dashboard Template
 * Displays user profile, watch history, and settings
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( wp_login_url( get_permalink() ) );
	exit;
}

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$membership   = bantu_check_user_membership( $user_id );

get_header(); ?>

<main id="site-content" role="main">
	<div class="bantu-dashboard">
		<div class="bantu-container">
			<!-- Dashboard Header -->
			<div class="bantu-dashboard-header">
				<div class="bantu-user-info">
					<div class="bantu-user-avatar">
						<?php echo get_avatar( $user_id, 120, '', $current_user->display_name ); ?>
					</div>
					<div class="bantu-user-details">
						<h1><?php echo esc_html( $current_user->display_name ); ?></h1>
						<p class="bantu-user-email"><?php echo esc_html( $current_user->user_email ); ?></p>
						<?php if ( $membership['active'] ) : ?>
							<div class="bantu-membership-badge">
								<span class="bantu-badge-status">✓ Premium Member</span>
								<span class="bantu-badge-expiry">Until <?php echo esc_html( $membership['expiry'] ); ?></span>
							</div>
						<?php else : ?>
							<div class="bantu-membership-badge" style="background: #404040;">
								<span class="bantu-badge-status">Free Plan</span>
								<a href="<?php echo esc_url( home_url( '/subscribe' ) ); ?>">Upgrade Now</a>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="bantu-dashboard-actions">
					<a href="<?php echo esc_url( home_url( '/my-account' ) ); ?>" class="bantu-btn bantu-btn-secondary">Edit Profile</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="bantu-btn bantu-btn-secondary">Sign Out</a>
				</div>
			</div>

			<!-- Dashboard Tabs -->
			<div class="bantu-dashboard-tabs">
				<button class="bantu-tab-button active" data-tab="watch-history">Watch History</button>
				<button class="bantu-tab-button" data-tab="continue-watching">Continue Watching</button>
				<button class="bantu-tab-button" data-tab="favorites">My Favorites</button>
				<button class="bantu-tab-button" data-tab="settings">Settings</button>
			</div>

			<!-- Watch History Tab -->
			<div class="bantu-tab-content active" id="watch-history-tab">
				<h2>Watch History</h2>
				<div class="bantu-video-grid" id="bantu-history-grid">
					<?php
					$history = get_user_meta( $user_id, 'bantu_video_history', true ) ?: array();

					if ( ! empty( $history ) ) {
						// Sort by timestamp (most recent first)
						arsort( $history );
						$video_ids = array_keys( array_slice( $history, 0, 20 ) );

						$args = array(
							'post_type'      => 'video',
							'posts_per_page' => 20,
							'post__in'       => $video_ids,
							'orderby'        => 'post__in',
						);

						$history_query = new WP_Query( $args );

						if ( $history_query->have_posts() ) :
							while ( $history_query->have_posts() ) :
								$history_query->the_post();
								$watch_time = $history[ get_the_ID() ];
								$thumb      = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
								?>
								<div class="bantu-video-card">
									<div class="bantu-video-card-image">
										<img src="<?php echo esc_attr( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
										<div class="bantu-video-card-overlay">
											<a href="<?php the_permalink(); ?>" class="bantu-play-button">▶</a>
											<span class="bantu-watch-status">
												<?php echo esc_html( gmdate( 'M d, Y', $watch_time ) ); ?>
											</span>
										</div>
										<div class="bantu-progress-bar" style="width: 100%;"></div>
									</div>
									<div class="bantu-video-card-info">
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<p class="bantu-video-meta">Watched on <?php echo esc_html( gmdate( 'M d, Y', $watch_time ) ); ?></p>
									</div>
								</div>
								<?php
							endwhile;
							wp_reset_postdata();
						else :
							echo '<p style="grid-column: 1/-1; color: #b3b3b3;">No watch history yet.</p>';
						endif;
					} else {
						echo '<p style="grid-column: 1/-1; color: #b3b3b3;">Your watch history will appear here as you watch videos.</p>';
					}
					?>
				</div>
			</div>

			<!-- Continue Watching Tab -->
			<div class="bantu-tab-content" id="continue-watching-tab">
				<h2>Continue Watching</h2>
				<div class="bantu-video-grid">
					<?php
					$progress = get_user_meta( $user_id, 'bantu_video_progress', true ) ?: array();

					if ( ! empty( $progress ) ) {
						$in_progress_ids = array_keys( $progress );
						$continue_query  = new WP_Query( array(
							'post_type'      => 'video',
							'posts_per_page' => 12,
							'post__in'       => $in_progress_ids,
							'orderby'        => 'post__in',
						) );

						if ( $continue_query->have_posts() ) :
							while ( $continue_query->have_posts() ) :
								$continue_query->the_post();
								$prog = $progress[ get_the_ID() ];
								?>
								<div class="bantu-video-card">
									<div class="bantu-video-card-image">
										<img src="<?php echo esc_attr( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
										<div class="bantu-video-card-overlay">
											<a href="<?php the_permalink(); ?>" class="bantu-play-button">▶</a>
										</div>
										<div class="bantu-progress-bar-container">
											<div class="bantu-progress-bar" style="width: <?php echo esc_attr( $prog['percentage'] ); ?>%;"></div>
										</div>
										<span class="bantu-progress-text"><?php echo esc_html( $prog['percentage'] ); ?>% watched</span>
									</div>
									<div class="bantu-video-card-info">
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									</div>
								</div>
								<?php
							endwhile;
						else :
							echo '<p style="grid-column: 1/-1; color: #b3b3b3;">No videos in progress. Start watching something!</p>';
						endif;
						wp_reset_postdata();
					} else {
						echo '<p style="grid-column: 1/-1; color: #b3b3b3;">Your in-progress videos will appear here.</p>';
					}
					?>
				</div>
			</div>

			<!-- Favorites Tab -->
			<div class="bantu-tab-content" id="favorites-tab">
				<h2>My Favorites</h2>
				<div class="bantu-video-grid">
					<?php
					$favorites = get_user_meta( $user_id, 'bantu_favorites', true ) ?: array();

					if ( ! empty( $favorites ) ) {
						$favorites_query = new WP_Query( array(
							'post_type'      => 'video',
							'posts_per_page' => 20,
							'post__in'       => $favorites,
						) );

						if ( $favorites_query->have_posts() ) :
							while ( $favorites_query->have_posts() ) :
								$favorites_query->the_post();
								?>
								<div class="bantu-video-card">
									<div class="bantu-video-card-image">
										<img src="<?php echo esc_attr( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
										<div class="bantu-video-card-overlay">
											<a href="<?php the_permalink(); ?>" class="bantu-play-button">▶</a>
											<button class="bantu-remove-favorite" data-video-id="<?php echo esc_attr( get_the_ID() ); ?>" aria-label="Remove from favorites">✕</button>
										</div>
									</div>
									<div class="bantu-video-card-info">
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									</div>
								</div>
								<?php
							endwhile;
						else :
							echo '<p style="grid-column: 1/-1; color: #b3b3b3;">No favorites yet. Add videos to your favorites!</p>';
						endif;
						wp_reset_postdata();
					} else {
						echo '<p style="grid-column: 1/-1; color: #b3b3b3;">Your favorite videos will appear here.</p>';
					}
					?>
				</div>
			</div>

			<!-- Settings Tab -->
			<div class="bantu-tab-content" id="settings-tab">
				<div class="bantu-settings-grid">
					<!-- Subscription Settings -->
					<div class="bantu-settings-card">
						<h3>Subscription</h3>
						<?php if ( $membership['active'] ) : ?>
							<div class="bantu-setting-item">
								<label>Plan Type</label>
								<p><?php echo esc_html( ucfirst( $membership['level'] ) ); ?> Plan</p>
							</div>
							<div class="bantu-setting-item">
								<label>Expires</label>
								<p><?php echo esc_html( $membership['expiry'] ); ?></p>
							</div>
							<div class="bantu-setting-item">
								<label>Days Remaining</label>
								<p><?php echo esc_html( $membership['days_left'] ); ?> days</p>
							</div>
							<a href="<?php echo esc_url( home_url( '/my-account' ) ); ?>" class="bantu-btn bantu-btn-secondary">Manage Subscription</a>
						<?php else : ?>
							<p style="color: #b3b3b3;">You're on the free plan. Upgrade to access premium content.</p>
							<a href="<?php echo esc_url( home_url( '/subscribe' ) ); ?>" class="bantu-btn bantu-btn-primary">Upgrade Now</a>
						<?php endif; ?>
					</div>

					<!-- Profile Settings -->
					<div class="bantu-settings-card">
						<h3>Profile</h3>
						<div class="bantu-setting-item">
							<label>Email</label>
							<p><?php echo esc_html( $current_user->user_email ); ?></p>
						</div>
						<div class="bantu-setting-item">
							<label>Member Since</label>
							<p><?php echo esc_html( gmdate( 'F d, Y', strtotime( $current_user->user_registered ) ) ); ?></p>
						</div>
						<a href="<?php echo esc_url( home_url( '/my-account' ) ); ?>" class="bantu-btn bantu-btn-secondary">Edit Profile</a>
					</div>

					<!-- Download Settings -->
					<div class="bantu-settings-card">
						<h3>Downloads</h3>
						<p style="color: #b3b3b3;">Download videos to watch offline (Premium only)</p>
						<?php if ( $membership['active'] ) : ?>
							<p style="color: #b3b3b3; font-size: 0.9rem;">You can download up to 100 videos. Downloaded videos expire after 30 days.</p>
							<a href="#" class="bantu-btn bantu-btn-secondary" onclick="return false;">Manage Downloads</a>
						<?php else : ?>
							<a href="<?php echo esc_url( home_url( '/subscribe' ) ); ?>" class="bantu-btn bantu-btn-secondary">Upgrade to Download</a>
						<?php endif; ?>
					</div>

					<!-- Privacy Settings -->
					<div class="bantu-settings-card">
						<h3>Privacy</h3>
						<div class="bantu-setting-checkbox">
							<input type="checkbox" id="watch-history-privacy" checked>
							<label for="watch-history-privacy">Save watch history</label>
						</div>
						<div class="bantu-setting-checkbox">
							<input type="checkbox" id="recommendations-privacy" checked>
							<label for="recommendations-privacy">Use watch history for recommendations</label>
						</div>
						<a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>" class="bantu-link">View Privacy Policy</a>
					</div>

					<!-- Notifications -->
					<div class="bantu-settings-card">
						<h3>Notifications</h3>
						<div class="bantu-setting-checkbox">
							<input type="checkbox" id="email-new-releases" checked>
							<label for="email-new-releases">Email me about new releases</label>
						</div>
						<div class="bantu-setting-checkbox">
							<input type="checkbox" id="email-recommendations" checked>
							<label for="email-recommendations">Email me recommendations</label>
						</div>
					</div>

					<!-- Delete Account -->
					<div class="bantu-settings-card bantu-settings-danger">
						<h3>Danger Zone</h3>
						<p style="color: #b3b3b3; font-size: 0.9rem;">Deleting your account is permanent and cannot be undone.</p>
						<button class="bantu-btn bantu-btn-danger" onclick="if(confirm('Are you sure? This cannot be undone.')) { window.location.href='<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=bantu_delete_account' ), 'bantu_delete_nonce' ) ); ?>'; }">Delete Account</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script>
document.querySelectorAll('.bantu-tab-button').forEach(button => {
	button.addEventListener('click', function() {
		const tabName = this.dataset.tab;
		document.querySelectorAll('.bantu-tab-content').forEach(tab => tab.classList.remove('active'));
		document.querySelectorAll('.bantu-tab-button').forEach(btn => btn.classList.remove('active'));
		document.getElementById(tabName + '-tab').classList.add('active');
		this.classList.add('active');
	});
});

document.querySelectorAll('.bantu-remove-favorite').forEach(btn => {
	btn.addEventListener('click', function(e) {
		e.preventDefault();
		const videoId = this.dataset.videoId;
		// AJAX call to remove from favorites
		console.log('Remove favorite:', videoId);
	});
});
</script>

<?php get_footer();
