<?php
/**
 * Template for displaying video archive/grid
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

get_header();
?>

<main id="main" class="site-content">
	<header class="page-header" style="padding: 3rem 2rem; background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%); margin-bottom: 3rem;">
		<div class="container">
			<h1 class="page-title" style="margin: 0; color: #ffffff;">
				<?php
				if ( is_category() ) {
					single_cat_title();
				} elseif ( is_tag() ) {
					single_tag_title();
				} elseif ( is_tax() ) {
					single_term_title();
				} else {
					esc_html_e( 'Videos', 'twentytwentyfive' );
				}
				?>
			</h1>
		</div>
	</header>

	<div class="container">
		<!-- Search Bar -->
		<div style="margin-bottom: 3rem; display: flex; gap: 1rem; flex-wrap: wrap;">
			<input 
				type="text" 
				id="bantu-search" 
				placeholder="Search videos..." 
				style="flex: 1; min-width: 250px; padding: 0.75rem 1rem; background-color: #1a1f3a; border: 1px solid #404040; border-radius: 4px; color: #ffffff;"
			>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="bantu-content-row">
				<div class="bantu-video-grid">
					<?php
					while ( have_posts() ) {
						the_post();
						$video_id = get_the_ID();
						$thumbnail = get_the_post_thumbnail_url( $video_id, 'medium' );
						$duration  = get_post_meta( $video_id, 'video_duration', true ) ?: '0';
						?>
						<a href="<?php the_permalink(); ?>" class="bantu-video-card">
							<?php if ( $thumbnail ) : ?>
								<img src="<?php echo esc_attr( $thumbnail ); ?>" alt="<?php the_title_attribute(); ?>" class="bantu-video-card-image">
							<?php else : ?>
								<div style="width: 100%; height: 100%; background-color: #2d3354; display: flex; align-items: center; justify-content: center; color: #b3b3b3;">
									No Image
								</div>
							<?php endif; ?>
							<div class="bantu-video-card-overlay">
								<h3 class="bantu-video-card-title"><?php the_title(); ?></h3>
								<p class="bantu-video-card-meta"><?php echo esc_html( $duration ); ?> min</p>
							</div>
						</a>
						<?php
					}
					?>
				</div>
			</div>

			<!-- Pagination -->
			<div style="margin-top: 3rem; text-align: center;">
				<?php the_posts_pagination(); ?>
			</div>
		<?php else : ?>
			<div style="text-align: center; padding: 4rem 2rem;">
				<h2 style="color: #b3b3b3;">No videos found</h2>
				<p style="color: #b3b3b3; margin-bottom: 2rem;">Check back soon for new content!</p>
				<a href="<?php echo esc_url( home_url() ); ?>" class="btn btn-primary">Back to Home</a>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
?>
