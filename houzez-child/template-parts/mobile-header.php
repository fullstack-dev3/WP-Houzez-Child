<?php
$mobile_menu_sticky = houzez_option('mobile-menu-sticky');
$create_lisiting_enable = houzez_option('create_lisiting_enable');
$header_onepage = '';
if ( is_page_template( 'template/template-onepage.php' ) ) {
	$header_onepage = 'header-single-page';
}
?>

<div class="header-mobile houzez-header-mobile <?php echo esc_attr($header_onepage); ?>"  data-sticky="<?php echo esc_attr( $mobile_menu_sticky ); ?>">
	<div class="container">
		<div class="header-logo logo-mobile">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php if (is_front_page()) { ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo_white.png" alt="logo">
                <?php } else { ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo_color.png" alt="logo">
                <?php } ?>
            </a>
		</div>
		
		<div class="header-user">
			<?php if( class_exists('Houzez_login_register') ): ?>
				<?php if( houzez_option('header_login') != 'no' || $create_lisiting_enable != 0 ): ?>
					<?php get_template_part('inc/header/login-nav', 'mobile'); ?>
				<?php endif; ?>
			<?php endif; ?>
			<ul class="account-action">
                	<li>
                		<a href="<?php echo get_site_url(); ?>">
                			<i class="fa fa-map-marker"></i>
                		</a>
                	</li>
                	<li>
                		<a href="<?php echo get_site_url(); ?>">
                			<i class="fa fa-search"></i>
                		</a>
                	</li>
                	<li>
                		<select>
                			<option value="en">EN</option>
                			<option value="es">ES</option>
                			<option value="de">DE</option>
                		</select>
                	</li>
                </ul>
		</div>

		<div class="mobile-nav">
			<span class="nav-trigger"><i class="fa fa-navicon"></i></span>
			<div class="nav-dropdown main-nav-dropdown"></div>
		</div>
	</div>
</div>