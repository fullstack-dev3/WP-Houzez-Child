<?php
/**
 * Template Name: Addtional Package Payment
 */

if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

$dashboard_listings = houzez_get_template_link_2('template-user-dashboard-properties.php');
$listing = add_query_arg( 'prop_status', 'package', $dashboard_listings );

$dashboard_package = houzez_get_template_link_2('template-user-dashboard-package.php');

if ( !isset($_GET['option']) || $_GET['option'] == '' || 
	 !isset($_GET['post']) || $_GET['post'] == '' ||
	 ($_GET['option'] != 'featured' && $_GET['option'] != 'week') ) {

  if (!isset($_GET['state']))
    wp_redirect( $listing );
}

get_header();

$panel_class = 'dashboard-with-panel';
$houzez_loggedin = true;
$column_classes = 'col-lg-10 col-md-9 col-sm-12 dashboard-inner-left';
$sidebar_classes = 'col-lg-2 col-md-3 col-sm-12 dashboard-inner-right';

$stripe_processor_link = houzez_get_template_link('template/template-stripe-charge.php');

get_template_part( 'template-parts/dashboard', 'menu' );
?>

<div class="user-dashboard-right <?php echo esc_attr($panel_class);?>">

  <?php get_template_part( 'template-parts/dashboard-title' ); ?>

  <div class="dashboard-content-area">
    <div class="container">

    	<?php get_template_part('template-parts/add-package-option'); ?>

    	<div class="row dashboard-inner-main">
        <div class="<?php echo esc_attr($column_classes); ?>">
          <div class="membership-content class-for-register-msg">
            <form name="houzez_checkout" method="post" class="houzez_payment_form" action="<?php echo $stripe_processor_link; ?>">
              <div class="info-title">
                  <h2 class="info-title-left"> <?php echo $houzez_local['payment_method']; ?> </h2>
              </div>

              <?php
                get_template_part('template-parts/addon-package/payment-methods');
              ?>
            </form>
          </div>
        </div>
        <div class="<?php echo esc_attr($sidebar_classes);?>">
          <div class="dashboard-sidebar">
            <div class="dashboard-sidebar-inner">
              <div class="payment-side-block">
                <?php
                  get_template_part('template-parts/addon-package/price');
                ?>
              </div>
            </div>
          </div>
        </div>
    	</div>
    </div>
  </div>
</div>

<?php get_footer(); ?>