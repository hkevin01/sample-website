<!DOCTYPE html>
<html lang="<?php bloginfo( 'language' ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<!-- 1. ACCESSIBILITY TOOLBAR (first in DOM order, before hero/title) -->
	<a href="#main-content" class="skip-link">Skip to Content</a>
	<div class="accessibility-bar" role="toolbar" aria-label="Accessibility tools">
		<span>Accessibility Controls:</span>
		<div class="toolbar-controls">
			<button class="toolbar-btn" id="font-dec" aria-label="Decrease Text Size">A-</button>
			<button class="toolbar-btn" id="font-inc" aria-label="Increase Text Size">A+</button>
			<button class="toolbar-btn" id="contrast-toggle" aria-label="Toggle High Contrast Mode" aria-pressed="false">Contrast Mode</button>
		</div>
	</div>

	<?php
	$hero_image = get_theme_mod( 'hero_image' );
	$hero_class = 'hero' . ( $hero_image ? ' has-hero-image' : '' );
	$hero_style = $hero_image ? ' style="--hero-image:url(' . esc_url( $hero_image ) . ');"' : '';
	?>
	<!-- 2. TITLE & VISUAL GRAPHIC HERO -->
	<!-- <header> is already an implicit "banner" landmark here (not nested in <article>/<aside>), so no explicit role is needed -->
	<header class="<?php echo esc_attr( $hero_class ); ?>"<?php echo $hero_style; ?>>
		<h1><?php bloginfo( 'name' ); ?></h1>
		<p><?php echo esc_html( get_theme_mod( 'hero_tagline', 'Dialed-in Specialty Espresso & Community Hub in Fayetteville, West Virginia' ) ); ?></p>
	</header>

	<!-- 3. SMALL BUSINESS COMMON MENU OPTIONS BAR -->
	<nav class="menu-bar" aria-label="Main business navigation">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			// wp_nav_menu() automatically marks the active item with aria-current="page"
			// (WordPress core since 5.5) and a "current-menu-item" class — no extra code needed.
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'menu-nav',
			) );
		} else {
			$is_front = is_front_page();
			?>
			<ul class="menu-nav">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo $is_front ? ' aria-current="page"' : ''; ?>>Home</a></li>
				<li><a href="#menu-highlights">Menu</a></li>
				<li><a href="#services">Services</a></li>
				<li><a href="#gallery">Gallery</a></li>
				<li><a href="#shop">Merchandise</a></li>
				<li><a href="#footer">Contact Us</a></li>
			</ul>
			<?php
		}
		?>
	</nav>