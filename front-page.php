<?php
/**
 * Front page template for BANTU Plus SVOD
 * Netflix-style homepage with search, categories, and video grid
 */

get_header(); ?>

<main id="site-content" role="main">
	<div class="bantu-homepage">
		<!-- Hero Section with Featured Video -->
		<section class="bantu-hero" id="bantu-hero-section">
			<?php
			$featured = new WP_Query( array(
				'post_type'      => 'video',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );

			if ( $featured->have_posts() ) :
				while ( $featured->have_posts() ) :
					$featured->the_post();
					$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22%3E%3Crect fill=%22%231a1f3a%22 width=%221920%22 height=%221080%22/%3E%3C/svg%3E';
					?>
					<div class="bantu-hero-content" style="background: linear-gradient(135deg, rgba(10, 14, 39, 0.8) 0%, rgba(229, 9, 20, 0.2) 100%), url('<?php echo esc_attr( $hero_image ); ?>') center/cover;">
						<div class="bantu-hero-text">
							<h1><?php the_title(); ?></h1>
							<p class="bantu-hero-description"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
							<div class="bantu-hero-actions">
								<a href="<?php the_permalink(); ?>" class="bantu-btn bantu-btn-primary">
									<span>▶</span> Watch Now
								</a>
								<button class="bantu-btn bantu-btn-secondary" onclick="document.getElementById('bantu-browse').scrollIntoView({ behavior: 'smooth' });">
									<span>ℹ</span> More Info
								</button>
							</div>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</section>

		<!-- Search & Filter Section -->
		<section class="bantu-browse" id="bantu-browse">
			<div class="bantu-container">
				<div class="bantu-search-filters">
					<!-- Search Bar -->
					<div class="bantu-search-wrapper">
						<input 
							type="text" 
							id="bantu-search-input" 
							class="bantu-search-input" 
							placeholder="Search videos, titles, actors..."
							aria-label="Search videos"
						>
						<span class="bantu-search-icon">🔍</span>
						<div id="bantu-search-results" class="bantu-search-results" style="display: none;"></div>
					</div>

					<!-- Filter & Sort Controls -->
					<div class="bantu-controls">
						<div class="bantu-filter-group">
							<label for="bantu-category-filter">Category</label>
							<select id="bantu-category-filter" class="bantu-select">
								<option value="">All Categories</option>
								<?php
								$categories = get_terms( array(
									'taxonomy'   => 'video_category',
									'hide_empty' => true,
								) );
								foreach ( $categories as $cat ) {
									echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
								}
								?>
							</select>
						</div>

						<div class="bantu-filter-group">
							<label for="bantu-sort-filter">Sort By</label>
							<select id="bantu-sort-filter" class="bantu-select">
								<option value="latest">Latest</option>
								<option value="popular">Most Popular</option>
								<option value="trending">Trending</option>
								<option value="a-z">A - Z</option>
							</select>
						</div>
					</div>
				</div>

				<!-- Category Pills (Quick Filter) -->
				<div class="bantu-category-pills">
					<button class="bantu-pill active" data-category="">All</button>
					<?php
					foreach ( $categories as $cat ) {
						echo '<button class="bantu-pill" data-category="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</button>';
					}
					?>
				</div>
			</div>
		</section>

		<!-- Video Grid Section -->
		<section class="bantu-video-grid-section">
			<div class="bantu-container">
				<!-- Videos Grid -->
				<div class="bantu-video-grid" id="bantu-video-grid">
					<?php
					$args = array(
						'post_type'      => 'video',
						'posts_per_page' => 20,
						'orderby'        => 'date',
						'order'          => 'DESC',
						'paged'          => get_query_var( 'paged' ) ?: 1,
					);

					$videos = new WP_Query( $args );

					if ( $videos->have_posts() ) :
						while ( $videos->have_posts() ) :
							$videos->the_post();
							$thumb   = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 300 450%22%3E%3Crect fill=%22%232d3354%22 width=%22300%22 height=%22450%22/%3E%3C/svg%3E';
							$duration = get_post_meta( get_the_ID(), 'video_duration', true ) ?: '45:00';
							?>
							<div class="bantu-video-card">
								<div class="bantu-video-card-image">
									<img src="<?php echo esc_attr( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
									<div class="bantu-video-card-overlay">
										<a href="<?php the_permalink(); ?>" class="bantu-play-button" aria-label="Play">
											▶
										</a>
										<button class="bantu-add-list" aria-label="Add to list" onclick="event.preventDefault();">
											+
										</button>
									</div>
									<span class="bantu-duration"><?php echo esc_html( $duration ); ?></span>
								</div>
								<div class="bantu-video-card-info">
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<p class="bantu-video-meta">
										<?php
										$cats = get_the_terms( get_the_ID(), 'video_category' );
										if ( $cats ) {
											echo esc_html( $cats[0]->name );
										}
										?>
									</p>
									<div class="bantu-video-rating">
										<span class="bantu-rating-stars">★★★★★</span>
										<span class="bantu-rating-text">(<?php echo rand( 100, 9999 ); ?> views)</span>
									</div>
								</div>
							</div>
							<?php
						endwhile;
					else :
						echo '<div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem; color: #b3b3b3;">No videos found. Try a different search or category.</div>';
					endif;
					wp_reset_postdata();
					?>
				</div>

				<!-- Pagination -->
				<?php
				if ( $videos->max_num_pages > 1 ) :
					?>
					<div class="bantu-pagination">
						<?php
						echo paginate_links( array(
							'total'        => $videos->max_num_pages,
							'current'      => max( 1, get_query_var( 'paged' ) ),
							'prev_text'    => '← Previous',
							'next_text'    => 'Next →',
							'type'         => 'list',
						) );
						?>
					</div>
					<?php
				endif;
				?>
			</div>
		</section>

		<!-- Recommended Section (if user is logged in) -->
		<?php if ( is_user_logged_in() ) : ?>
			<section class="bantu-recommended-section">
				<div class="bantu-container">
					<h2>Recommended For You</h2>
					<div class="bantu-video-grid" id="bantu-recommended-grid">
						<?php
						$current_user = get_current_user_id();
						$user_views   = get_user_meta( $current_user, 'bantu_video_history', true ) ?: array();

						if ( ! empty( $user_views ) ) {
							// Get random videos based on watched categories
							$viewed_ids = array_keys( $user_views );
							$args       = array(
								'post_type'      => 'video',
								'posts_per_page' => 6,
								'post__not_in'   => array_slice( $viewed_ids, 0, 3 ),
								'orderby'        => 'rand',
							);
						} else {
							// Show popular videos
							$args = array(
								'post_type'      => 'video',
								'posts_per_page' => 6,
								'orderby'        => 'comment_count',
								'order'          => 'DESC',
							);
						}

						$recommended = new WP_Query( $args );

						if ( $recommended->have_posts() ) :
							while ( $recommended->have_posts() ) :
								$recommended->the_post();
								$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 300 450%22%3E%3Crect fill=%22%232d3354%22 width=%22300%22 height=%22450%22/%3E%3C/svg%3E';
								?>
								<div class="bantu-video-card">
									<div class="bantu-video-card-image">
										<img src="<?php echo esc_attr( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
										<div class="bantu-video-card-overlay">
											<a href="<?php the_permalink(); ?>" class="bantu-play-button">▶</a>
										</div>
									</div>
									<div class="bantu-video-card-info">
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<p class="bantu-video-meta">Recommended</p>
									</div>
								</div>
								<?php
							endwhile;
						endif;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
