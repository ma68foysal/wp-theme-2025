<?php
/**
 * The main template file
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 */

get_header();
?>

<main id="main" class="site-content">
	<!-- Hero Section -->
	<?php get_template_part( 'template-parts/home-hero' ); ?>

	<!-- Main Container for Content Rows -->
	<div class="container" style="max-width: 100%; padding: 0;">
		<!-- Featured Content Rows by Category -->
		<?php get_template_part( 'template-parts/content-rows' ); ?>

		<!-- Browse All Videos Section -->
		<div style="margin-top: 6rem; padding: 4rem 2rem; background-color: #1a1f3a; border-radius: 8px; text-align: center;">
			<h2 style="color: #ffffff; margin-bottom: 1rem;">Browse All Videos</h2>
			<p style="color: #b3b3b3; margin-bottom: 2rem;">Explore our complete library of content</p>
			<a href="<?php echo esc_url( home_url( '/videos' ) ); ?>" class="btn btn-primary">View All Videos</a>
		</div>
	</div>
</main>

<?php
get_footer();
?>
