<?php
global $houzez_options, $houzez_local;
$houzez_local = houzez_get_localization();
/**
 * @package Houzez
 * @since Houzez 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
<div id="fb-root"></div>

<?php get_template_part( '../houzez/inc/header/login-register-popup' ); ?>
<?php if ( !is_page_template( '../houzez/template/template-splash.php' ) ) { 

	global $current_user, $post;
	wp_get_current_user();
	$userID  =  $current_user->ID;
	$user_custom_picture =  get_the_author_meta( 'fave_author_custom_picture' , $userID );
	$header_layout = houzez_option('header_4_width');
	$main_menu_sticky = houzez_option('main-menu-sticky');
	$header_4_menu_align = houzez_option('header_4_menu_align');
	$top_bar = houzez_option('top_bar');

	$trans_class = '';
	if(!is_author() && !is_404()) {
	    $fave_main_menu_trans = get_post_meta( $post->ID, 'fave_main_menu_trans', true );
	    if( $fave_main_menu_trans == 'yes' ) {
	        $trans_class = 'houzez-header-transparent';
	    }
	}

	if( $top_bar != 0 ) {
	    get_template_part('../houzez/inc/header/top', 'bar');
	}
	$menu_righ_no_user = '';
	$create_lisiting_enable = houzez_option('create_lisiting_enable');
	$header_login = houzez_option('header_login');
	if( $header_4_menu_align == 'nav-right' && $header_login != 'yes' && $create_lisiting_enable != 1 ) {
	    $menu_righ_no_user = 'menu-right-no-user';
	}
	$houzez_user_logout = '';
	if( ! is_user_logged_in() ) {
	    $houzez_user_logout = 'houzez-user-logout';
	    if( $header_login != 'yes' ) {
	        $houzez_user_logout = 'houzez-disabled-login';
	    }
	    if( $create_lisiting_enable != 1 ) {
	        $houzez_user_logout = 'houzez-disabled-create-listing';
	    }
	    if( $header_login != 'yes' && $create_lisiting_enable != 1 ) {
	        $houzez_user_logout = '';
	    }
	}
	if( houzez_is_dashboard() ||
        is_page_template('template-addon-payment.php') ||
        is_page_template('template-user-dashboard-package.php') ||
        is_page_template('template-user-dashboard-membership.php') ||
        is_page_template('template-user-dashboard-properties.php') ||
        is_page_template('template-advanced-package.php') ||
        is_page_template('template-advanced-payment.php') ||
        is_page_template('template-document-upload.php') ) {
	    $header_layout = 'container-fluid';
	}
	?>
	<!--start section header-->
	<header id="header-section" class="houzez-header-main header-section-4 <?php echo esc_attr( $header_4_menu_align ).' '.esc_attr($trans_class).' '.esc_attr($menu_righ_no_user).' '.esc_attr($houzez_user_logout); ?>" data-sticky="<?php echo esc_attr( $main_menu_sticky ); ?>">
	    <div class="<?php echo sanitize_html_class( $header_layout ); ?>">
	        <div class="header-left">

	            <div class="logo logo-desktop">
	                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php if (is_front_page()) { ?>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo_white.png" alt="logo">
                        <?php } else { ?>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo_color.png" alt="logo">
                        <?php } ?>
                    </a>
	            </div>

	            <nav class="navi main-nav">
	                <?php
	                // Pages Menu
	                if ( has_nav_menu( 'main-menu' ) ) :
	                    wp_nav_menu( array (
	                        'theme_location' => 'main-menu',
	                        'container' => '',
	                        'container_class' => '',
	                        'menu_class' => '',
	                        'menu_id' => 'main-nav',
	                        'depth' => 4
	                    ));
	                endif;
	                ?>
	            </nav>
	        </div>

	        <?php if( class_exists('Houzez_login_register') ): ?>
	            <?php if( $header_login != 'no' || $create_lisiting_enable != 0 ): ?>
	                <div class="header-right">	                	
	                    <?php get_template_part('../houzez/inc/header/login', 'nav'); ?>
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
	            <?php endif; ?>
	        <?php endif; ?>
	    </div>

	</header>
	<!--end section header-->

<?php
get_template_part( 'template-parts/mobile-header' );
?>

<?php 
    if (!is_front_page() && !is_404() &&
        get_page_template_slug() != 'template/submit_property.php' &&
        get_page_template_slug() != 'template/template-payment.php' &&
        get_page_template_slug() != 'template/user_dashboard_profile.php' &&
        get_page_template_slug() != 'template-document-upload.php' &&
        get_page_template_slug() != 'template-advanced-package.php' &&
        get_page_template_slug() != 'template-advanced-payment.php' &&
        get_page_template_slug() != 'template-addon-payment.php' &&
        get_page_template_slug() != 'template-user-dashboard-package.php' &&
        get_page_template_slug() != 'template-user-dashboard-membership.php' &&
        get_page_template_slug() != 'template-user-dashboard-properties.php' &&
        get_page_template_slug() != 'template/user_dashboard_favorites.php' &&
        get_page_template_slug() != 'template/user_dashboard_saved_search.php' &&
        get_page_template_slug() != 'template/user_dashboard_invoices.php' &&
        get_page_template_slug() != 'template/user_dashboard_messages.php' &&
        get_page_template_slug() != 'template/user_dashboard_membership.php'
    ) {
    
    $search_template = home_url() . '/advanced-search';

    $measurement_unit_adv_search = houzez_option('measurement_unit_adv_search');
    if( $measurement_unit_adv_search == 'sqft' ) {
        $measurement_unit_adv_search = houzez_option('measurement_unit_sqft_text');
    } elseif( $measurement_unit_adv_search == 'sq_meter' ) {
        $measurement_unit_adv_search = houzez_option('measurement_unit_square_meter_text');
    }
    
    $hide_empty = false;

    $houzez_local = houzez_get_localization();

    $status = 'for-sale';
    $lifestyle = $location = $type = '';
    $min_price = '1,000';
    $max_price = '500,000';
    
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
    }
    if (isset($_GET['lifestyle'])) {
        $lifestyle = $_GET['lifestyle'];
    }
    if (isset($_GET['location'])) {
        $location = $_GET['location'];
    }
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    }
    if (isset($_GET['min-price']) && $_GET['min-price'] != '') {
        $min_price = $_GET['min-price'];
    }
    if (isset($_GET['max-price']) && $_GET['max-price'] != '') {
        $max_price = $_GET['max-price'];
    }
?>
<input type="hidden" id="min_price" value="<?php echo str_replace(',', '', $min_price); ?>" />
<input type="hidden" id="max_price" value="<?php echo str_replace(',', '', $max_price); ?>" />

<div class="advanced-search advanced-search-module houzez-adv-price-range">
    <?php if (empty($search_title)) { ?>
        <h3 class="advance-title"><?php echo esc_html__('Search Properties for Sale'); ?></h3>
    <?php } else { ?>
        <h3 class="advance-title"><?php echo esc_attr($search_title); ?></h3>
    <?php } ?>

    <form autocomplete="off" method="get" action="<?php echo esc_url($search_template); ?>">
        <div class="row">
            <input type="hidden" id="type" name="status" value="<?php echo $status; ?>" />
            <div class="col-md-2 col-sm-6 buy-btn">
                <?php if ($status == 'for-sale') { ?>
                <button type="button" class="btn btn-primary btn-type"><?php echo esc_html__('Buy'); ?></button>
                <?php } else { ?>
                <button type="button" class="btn btn-type"><?php echo esc_html__('Buy'); ?></button>
                <?php }?>
            </div>
            <div class="col-md-2 col-sm-6 rent-btn">
                <?php if ($status == 'for-rent') { ?>
                <button type="button" class="btn btn-primary btn-type"><?php echo esc_html__('Rent'); ?></button>
                <?php } else { ?>
                <button type="button" class="btn btn-type"><?php echo esc_html__('Rent'); ?></button>
                <?php }?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-sm-8 has-search">
                <span class="fa fa-search form-control-feedback"></span>
                <input type="text" name="city" class="form-control" 
                    placeholder="<?php echo esc_html__('Neighborhood, City'); ?>" />
            </div>
            <div class="col-md-2 col-sm-4">
                <button type="submit" class="btn btn-secondary">
                    <?php echo strtoupper($houzez_local['search']); ?>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 col-sm-12 select-advanced-main">
                <div class="col-md-3 col-sm-6">
                    <?php 
                        $lifestyle = isset($_GET['lifestyle']) ? $_GET['lifestyle'] : '';
                    ?>
                    <select class="selectpicker bs-select-hidden" name="lifestyle">
                    <?php
                        echo '<option value="">' . esc_html__('Lifestyle') . '</option>';

                        $prop_lifestyle = get_terms(
                            array(
                                "property_lifestyle"
                            ),
                            array(
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'hide_empty' => $hide_empty,
                                'parent' => 0
                            )
                        );
                        houzez_hirarchical_options('property_lifestyle', $prop_lifestyle, $lifestyle);
                    ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?php 
                        $region = isset($_GET['region']) ? $_GET['region'] : '';
                    ?>
                    <select class="selectpicker bs-select-hidden" name="region">
                    <?php
                        echo '<option value="">' . esc_html__('Location') . '</option>';

                        $prop_region = get_terms(
                            array(
                                "property_region"
                            ),
                            array(
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'hide_empty' => $hide_empty,
                                'parent' => 0
                            )
                        );
                        houzez_hirarchical_options('property_region', $prop_region, $region);
                    ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?php 
                        $type = isset($_GET['type']) ? $_GET['type'] : '';
                    ?>
                    <select class="selectpicker bs-select-hidden" name="type">
                    <?php
                        echo '<option value="">' . esc_html__('Property Type') . '</option>';

                        $prop_type = get_terms(
                            array(
                                "property_type"
                            ),
                            array(
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'hide_empty' => $hide_empty,
                                'parent' => 0
                            )
                        );
                        houzez_hirarchical_options('property_type', $prop_type, $type);
                    ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?php 
                        $searched_currency = isset($_GET['currency']) ? $_GET['currency'] : '';
                        $currencies = Houzez_Currencies::get_currency_codes();
                    ?>
                    <select class="selectpicker bs-select-hidden" name="currency">
                        <option value=""><?php echo esc_html__('Fiat/Crypto') ?></option>
                    <?php
                        foreach($currencies as $currency) {
                            echo '<option '.selected( $currency->currency_code, $searched_currency, false).' value="'.$currency->currency_code.'">'.$currency->currency_code.'</option>';
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="col-md-5 col-sm-12 range-advanced-main">
                <div class="range-text col-md-3">
                    <input type="hidden" name="min-price" class="min-price-range-hidden range-input" readonly >
                    <input type="hidden" name="max-price" class="max-price-range-hidden range-input" readonly >
                    <span class="range-title"><?php echo $houzez_local['price_range']; ?></span>
                </div>
                <div class="range-wrap col-md-9">
                    <span class="min-price-range"></span>
                    <div class="price-range"></div>
                    <span class="max-price-range"></span>
                </div>
            </div>
        </div>
    </form>
</div>
<?php } ?>

<?php
if ( is_page_template( '../houzez/template/property-listings-map.php' ) ||
     is_page_template( 'template-map-search.php' ) ) {
    $section_body .= 'houzez-body-half ';
}
if( houzez_is_landing_page() ) { $section_body .='landing-page';}
?>

<div id="section-body" class="<?php echo esc_attr( $section_body ); ?>">

	<?php
		get_template_part( '../houzez/template-parts/page-headers/page-header' );
	?>

	<?php if( houzez_container_needed() && 
        !is_page_template('template-user-dashboard-package.php') &&
        !is_page_template('template-user-dashboard-membership.php') &&
        !is_page_template('template-user-dashboard-properties.php') &&
        !is_page_template('template-addon-payment.php') &&
        !is_page_template('template-advanced-package.php') &&
        !is_page_template('template-advanced-payment.php') &&
        !is_page_template('template-document-upload.php') &&
        !is_page_template('template-map-search.php') ) { ?>
	<div class="container">
	<?php } ?>

<?php } // End splash template if  ?>

