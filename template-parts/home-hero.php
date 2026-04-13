<?php
/**
 * Netflix-style hero section for homepage
 */

// Get featured video
$featured_args = array(
	'post_type'      => 'video',
	'posts_per_page' => 1,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$featured_query = new WP_Query( $featured_args );
?>

<?php if ( $featured_query->have_posts() ) : ?>
	<?php
	$featured_query->the_post();
	$featured_id    = get_the_ID();
	$featured_image = get_the_post_thumbnail_url( $featured_id, 'full' );
	$featured_title = get_the_title();
	$featured_desc  = get_the_excerpt();
	?>

	<!-- Hero Section -->
	<section class="bantu-hero" style="
		background: linear-gradient(90deg, rgba(10, 14, 39, 0.9) 0%, rgba(10, 14, 39, 0.7) 50%, rgba(10, 14, 39, 0) 100%),
		url('<?php echo esc_attr( $featured_image ); ?>') center/cover no-repeat;
		height: 80vh;
		display: flex;
		align-items: center;
		padding: 2rem;
		margin-bottom: 4rem;
		position: relative;
		overflow: hidden;
	">
		<div class="bantu-hero-content" style="max-width: 500px; z-index: 2;">
			<div style="margin-bottom: 2rem;">
				<span style="
					display: inline-block;
					background-color: #e50914;
					color: #ffffff;
					padding: 0.5rem 1rem;
					border-radius: 4px;
					font-size: 0.75rem;
					font-weight: 700;
					text-transform: uppercase;
					letter-spacing: 2px;
				">New Release</span>
			</div>

			<h1 style="
				font-size: clamp(2.5rem, 6vw, 4rem);
				font-weight: 700;
				color: #ffffff;
				margin-bottom: 1.5rem;
				line-height: 1.2;
				text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
			">
				<?php echo esc_html( $featured_title ); ?>
			</h1>

			<p style="
				color: #b3b3b3;
				font-size: 1.1rem;
				line-height: 1.6;
				margin-bottom: 2rem;
				max-width: 400px;
			">
				<?php echo esc_html( wp_trim_words( $featured_desc, 30 ) ); ?>
			</p>

			<div style="display: flex; gap: 1rem; flex-wrap: wrap;">
				<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="
					display: inline-flex;
					align-items: center;
					gap: 0.5rem;
					background-color: #e50914;
					color: #ffffff;
					padding: 0.75rem 2rem;
					border-radius: 4px;
					font-weight: 600;
					font-size: 1rem;
					text-decoration: none;
					transition: background-color 0.3s ease;
					border: none;
					cursor: pointer;
				" onmouseover="this.style.backgroundColor='#ff1f29'" onmouseout="this.style.backgroundColor='#e50914'">
					▶ Play Now
				</a>

				<button class="btn btn-secondary" style="
					display: inline-flex;
					align-items: center;
					gap: 0.5rem;
					background-color: rgba(255, 255, 255, 0.2);
					color: #ffffff;
					padding: 0.75rem 2rem;
					border-radius: 4px;
					font-weight: 600;
					font-size: 1rem;
					border: 1px solid rgba(255, 255, 255, 0.3);
					cursor: pointer;
					transition: background-color 0.3s ease;
				" onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.2)'">
					ℹ More Info
				</button>
			</div>
		</div>

		<!-- Fade overlay at bottom -->
		<div style="
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			height: 200px;
			background: linear-gradient(180deg, rgba(10, 14, 39, 0) 0%, rgba(10, 14, 39, 1) 100%);
		"></div>
	</section>

	<?php wp_reset_postdata(); ?>
<?php endif; ?>
