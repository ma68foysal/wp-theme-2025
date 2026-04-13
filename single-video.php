<?php
/**
 * Template for displaying single video
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

get_header();
?>

<main id="main" class="site-content">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$video_id          = get_the_ID();
			$bunny_guid        = get_post_meta( $video_id, 'bunny_guid', true );
			$has_membership    = is_user_logged_in() ? true : false; // TODO: Check actual membership
			$is_restricted     = ! empty( $bunny_guid );
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header" style="padding: 2rem; background-color: #1a1f3a; margin-bottom: 2rem;">
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-meta" style="color: #b3b3b3; margin-top: 1rem;">
						<?php
						$categories = get_the_terms( $video_id, 'video_category' );
						if ( ! empty( $categories ) ) {
							echo 'Categories: ';
							foreach ( $categories as $category ) {
								echo '<a href="' . esc_url( get_term_link( $category ) ) . '">' . esc_html( $category->name ) . '</a> ';
							}
						}
						?>
					</div>
				</header>

		<?php 
		// Check if user has active membership
		$user_membership_status = bantu_check_user_membership( get_current_user_id() );
		$can_view_content       = is_user_logged_in() && $user_membership_status['active'];
		?>

		<?php if ( ! empty( $bunny_guid ) && $can_view_content ) : ?>
			<!-- Video Player -->
			<div 
				id="bantu-player-container" 
				class="bantu-player-container" 
				data-video-id="<?php echo esc_attr( $video_id ); ?>"
				data-hls-url="<?php echo esc_attr( bantu_get_hls_url( $bunny_guid ) ); ?>"
				data-restricted="true"
				data-has-membership="true"
			>
				<video id="bantu-player" class="bantu-player" controls playsinline crossorigin="anonymous"></video>
			</div>

			<?php if ( isset( $_GET['tracking'] ) ) : ?>
				<div style="margin-top: 2rem; padding: 1rem; background: #2d3354; border-radius: 4px; font-size: 0.9rem; color: #b3b3b3;">
					<strong>Membership Active Until:</strong> <?php echo esc_html( $user_membership_status['expiry'] ); ?>
				</div>
			<?php endif; ?>
		<?php elseif ( ! empty( $bunny_guid ) && ! is_user_logged_in() ) : ?>
			<!-- Login Required Paywall -->
			<div id="bantu-paywall" class="bantu-paywall" style="padding: 4rem 2rem; text-align: center; background: #1a1f3a; margin-bottom: 2rem; border-radius: 8px;">
				<h2 style="color: #e50914; margin-bottom: 1rem;">Sign In Required</h2>
				<p style="color: #b3b3b3; margin-bottom: 2rem;">Please sign in to watch this video.</p>
				<div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
					<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="btn btn-primary">Sign In</a>
					<a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="btn btn-secondary">Create Account</a>
				</div>
			</div>
		<?php elseif ( ! empty( $bunny_guid ) && ! $user_membership_status['active'] ) : ?>
			<!-- Subscription Paywall -->
			<div id="bantu-paywall" class="bantu-paywall" style="padding: 4rem 2rem; text-align: center; background: #1a1f3a; margin-bottom: 2rem; border-radius: 8px;">
				<h2 style="color: #e50914; margin-bottom: 1rem;">Premium Content</h2>
				<p style="color: #b3b3b3; margin-bottom: 2rem;">This video requires an active membership. Subscribe to access exclusive content.</p>
				<div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
					<a href="<?php echo esc_url( home_url( '/subscribe' ) ); ?>" class="btn btn-primary">Subscribe Now</a>
					<a href="<?php echo esc_url( home_url( '/account' ) ); ?>" class="btn btn-secondary">Manage Subscription</a>
				</div>
			</div>
		<?php else : ?>
			<div style="padding: 2rem; background: #1a1f3a; border-radius: 8px; text-align: center;">
				<p style="color: #b3b3b3;">Video not available at this moment.</p>
			</div>
		<?php endif; ?>

				<div class="entry-content container" style="padding: 2rem; max-width: 1000px; margin: 2rem auto;">
					<?php
					the_content(
						sprintf(
							wp_kses(
								__( 'Continue reading<span class="meta-nav"> &rarr;</span>', 'twentytwentyfive' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							get_the_title()
						)
					);
					?>
				</div>

				<footer class="entry-footer" style="padding: 2rem; border-top: 1px solid #404040;">
					<?php wp_link_pages(); ?>
				</footer>
			</article>

			<?php
			// Display related videos
			$args = array(
				'post_type'      => 'video',
				'posts_per_page' => 6,
				'post__not_in'   => array( $video_id ),
				'orderby'        => 'rand',
			);

			$related_videos = get_posts( $args );
			if ( ! empty( $related_videos ) ) :
				?>
				<section class="bantu-content-row">
					<h2>More Videos</h2>
					<div class="bantu-video-grid">
						<?php
						foreach ( $related_videos as $post ) {
							setup_postdata( $post );
							$thumbnail = get_the_post_thumbnail_url( $post->ID, 'medium' );
							$duration  = get_post_meta( $post->ID, 'video_duration', true ) ?: '0';
							?>
							<a href="<?php the_permalink(); ?>" class="bantu-video-card">
								<?php if ( $thumbnail ) : ?>
									<img src="<?php echo esc_attr( $thumbnail ); ?>" alt="<?php the_title_attribute(); ?>" class="bantu-video-card-image">
								<?php endif; ?>
								<div class="bantu-video-card-overlay">
									<h3 class="bantu-video-card-title"><?php the_title(); ?></h3>
									<p class="bantu-video-card-meta"><?php echo esc_html( $duration ); ?> min</p>
								</div>
							</a>
							<?php
						}
						wp_reset_postdata();
						?>
					</div>
				</section>
			<?php endif; ?>

		<?php
		}
	}
	?>
</main>

<?php
get_footer();

/**
 * Generate HLS URL from Bunny GUID
 *
 * @param string $guid Bunny GUID
 * @return string HLS URL
 */
function bantu_get_hls_url( $guid ) {
	// TODO: Replace with your Bunny CDN URL
	$bunny_cdn_url = defined( 'BUNNY_CDN_URL' ) ? BUNNY_CDN_URL : 'https://YOUR_BUNNY_CDN.b-cdn.net';
	return $bunny_cdn_url . '/' . sanitize_text_field( $guid ) . '/playlist.m3u8';
}
?>
