<?php
/**
 * Template Name: User Dashboard Membership Info
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $houzez_local, $current_user;

wp_get_current_user();
$userID = $current_user->ID;

$packages_page_link = houzez_get_template_link('template-advanced-package.php');
$package_id = houzez_get_user_package_id( $userID );

get_header();

get_template_part( 'template-parts/dashboard', 'menu' );
?>
<div class="user-dashboard-right dashboard-with-panel">

    <?php get_template_part( 'template-parts/dashboard-title' ); ?>

    <div class="dashboard-content-area dashboard-fix">
        <div class="container">

            <div class="row dashboard-inner-main">
                <div class="col-lg-10 col-md-9 col-sm-12 dashboard-inner-left">
                    <div class="membership-package-block">
                        <?php houzez_get_user_current_package( $userID ); ?>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 dashboard-inner-right">
                    <?php
                    if( !empty($package_id) ) {
                        echo '<a href="' . esc_url($packages_page_link) . '" class="plan-link btn btn-primary btn-block"> ' . esc_html__('Change Membership Plan', 'houzez') . ' </a>';
                        $stripe_profile_user    =   get_user_meta($userID,'fave_stripe_user_profile',true);
                        $subscription_id        =   get_user_meta( $userID, 'houzez_stripe_subscription_id', true );
                        $enable_stripe_status   =   houzez_option('enable_stripe');

                        if( $stripe_profile_user != '' && $subscription_id != '' && $enable_stripe_status != 0 ) {
                            echo '<a id="houzez_stripe_cancel" data-message="'.esc_html__('Done: Subscription will be cancelled at the end of current period', 'houzez').'" class="plan-link btn btn-secondary btn-block">'.esc_html__('Cancel Stripe Subscription', 'houzez').'</a>';
                            echo '<span id="stripe_cancel_success"></span>';
                        }
                    } else {
                        echo '<a href="' . esc_url($packages_page_link) . '" class="plan-link btn btn-primary btn-block"> ' . esc_html__('Get Membership Plan', 'houzez') . ' </a>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>