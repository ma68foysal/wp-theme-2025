<?php
/**
 * The header for BANTU Plus SVOD
 * Displays the site logo, navigation, and user menu
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<div id="page" class="site">
		<header id="masthead" class="bantu-site-header">
			<div class="bantu-header-container">
				<!-- Logo & Branding -->
				<div class="bantu-header-logo">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bantu-logo-text">
							<span style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">BANTU</span>
							<span style="font-size: 1rem; color: white;"> Plus</span>
						</a>
						<?php
					}
					?>
				</div>

				<!-- Navigation Menu -->
				<nav class="bantu-navigation" role="navigation" aria-label="Primary Navigation">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'primary',
						'menu_class'      => 'bantu-nav-menu',
						'container'       => false,
						'fallback_cb'     => 'bantu_default_nav_menu',
						'items_wrap'      => '<ul class="%2$s">%3$s</ul>',
					) );
					?>
				</nav>

				<!-- Header Right (Search & User) -->
				<div class="bantu-header-right">
					<!-- Search Toggle (Mobile) -->
					<button class="bantu-search-toggle" aria-label="Toggle search" onclick="document.querySelector('.bantu-search-wrapper')?.scrollIntoView({behavior: 'smooth'});">
						🔍
					</button>

					<!-- User Menu -->
					<div class="bantu-user-menu">
						<?php if ( is_user_logged_in() ) : ?>
							<?php
							$current_user = wp_get_current_user();
							$user_avatar  = get_avatar_url( $current_user->ID, array( 'size' => 40 ) );
							?>
							<div class="bantu-user-menu-wrapper">
								<button class="bantu-user-menu-toggle" aria-label="User menu" aria-expanded="false">
									<img src="<?php echo esc_attr( $user_avatar ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>" class="bantu-user-avatar">
									<span class="bantu-user-name"><?php echo esc_html( $current_user->display_name ); ?></span>
									<span class="bantu-menu-arrow">▼</span>
								</button>

								<div class="bantu-user-menu-dropdown" style="display: none;">
									<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="bantu-menu-item">
										<span>👤</span> My Profile
									</a>
									<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="bantu-menu-item">
										<span>📺</span> Watch History
									</a>
									<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="bantu-menu-item">
										<span>❤️</span> My Favorites
									</a>
									<a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="bantu-menu-item">
										<span>⚙️</span> Settings
									</a>
									<hr style="border: 0; border-top: 1px solid var(--color-border); margin: 0.5rem 0;">
									<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="bantu-menu-item">
										<span>🚪</span> Sign Out
									</a>
								</div>
							</div>
						<?php else : ?>
							<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="bantu-btn bantu-btn-secondary" style="margin-right: 0.5rem;">Sign In</a>
							<a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="bantu-btn bantu-btn-primary">Sign Up</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</header>

		<div id="content" class="site-content">
