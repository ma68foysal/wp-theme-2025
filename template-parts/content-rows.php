<?php
/**
 * Netflix-style content rows by category
 */

// Get all video categories
$categories = get_terms( array(
	'taxonomy'   => 'video_category',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
	'number'     => 8,
) );

if ( empty( $categories ) || is_wp_error( $categories ) ) {
	return;
}

?>

<?php foreach ( $categories as $category ) : ?>
	<?php
	$cat_videos = new WP_Query( array(
		'post_type'      => 'video',
		'tax_query'      => array(
			array(
				'taxonomy' => 'video_category',
				'field'    => 'term_id',
				'terms'    => $category->term_id,
			),
		),
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	?>

	<?php if ( $cat_videos->have_posts() ) : ?>
		<!-- Content Row -->
		<div style="margin-bottom: 4rem;">
			<h2 style="
				font-size: 1.5rem;
				font-weight: 600;
				color: #ffffff;
				margin-bottom: 1.5rem;
				margin-left: 2rem;
			">
				<?php echo esc_html( $category->name ); ?>
			</h2>

			<div class="bantu-content-row" style="
				display: flex;
				gap: 1rem;
				overflow-x: auto;
				padding: 0 2rem;
				scroll-behavior: smooth;
				-webkit-overflow-scrolling: touch;
			">
				<?php
				while ( $cat_videos->have_posts() ) {
					$cat_videos->the_post();
					$video_id = get_the_ID();
					$thumbnail = get_the_post_thumbnail_url( $video_id, 'medium' );
					$duration  = get_post_meta( $video_id, 'video_duration', true ) ?: '0';
					?>

					<!-- Video Card (Horizontal Scroll) -->
					<a href="<?php the_permalink(); ?>" class="bantu-video-card-horizontal" style="
						flex: 0 0 250px;
						position: relative;
						border-radius: 8px;
						overflow: hidden;
						cursor: pointer;
						transition: transform 0.3s ease, box-shadow 0.3s ease;
						display: flex;
						flex-direction: column;
					" onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 16px rgba(229, 9, 20, 0.5)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
						<?php if ( $thumbnail ) : ?>
							<img src="<?php echo esc_attr( $thumbnail ); ?>" alt="<?php the_title_attribute(); ?>" style="
								width: 100%;
								height: 140px;
								object-fit: cover;
								display: block;
							">
						<?php else : ?>
							<div style="
								width: 100%;
								height: 140px;
								background-color: #2d3354;
								display: flex;
								align-items: center;
								justify-content: center;
								color: #b3b3b3;
							">
								No Image
							</div>
						<?php endif; ?>

						<!-- Overlay on Hover -->
						<div style="
							position: absolute;
							top: 0;
							left: 0;
							right: 0;
							bottom: 0;
							background: linear-gradient(180deg, rgba(0, 0, 0, 0) 60%, rgba(0, 0, 0, 0.8) 100%);
							display: flex;
							align-items: flex-end;
							padding: 1rem;
							opacity: 0;
							transition: opacity 0.3s ease;
						" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
							<div>
								<h4 style="
									color: #ffffff;
									font-size: 0.95rem;
									font-weight: 600;
									margin: 0 0 0.5rem 0;
									line-height: 1.3;
								">
									<?php echo esc_html( wp_trim_words( get_the_title(), 5, '' ) ); ?>
								</h4>
								<p style="
									color: #b3b3b3;
									font-size: 0.75rem;
									margin: 0;
								">
									<?php echo esc_html( $duration ); ?> min
								</p>
							</div>
						</div>
					</a>

					<?php
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	<?php endif; ?>

<?php endforeach; ?>
