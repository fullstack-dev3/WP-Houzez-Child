<?php
/**
 * Template Name: Payment Page
 */
$selected_package_id = isset( $_GET['selected_package'] ) ? $_GET['selected_package'] : '';
$property_id = isset( $_GET['prop-id'] ) ? $_GET['prop-id'] : '';
$upgrade_id = isset( $_GET['upgrade_id'] ) ? $_GET['upgrade_id'] : '';

if (!isset( $_GET['selected_package'] ) && isset($_GET['state']) && $_GET['state'] != '')
    $selected_package_id = $_GET['state'];

if( empty( $selected_package_id ) && empty( $property_id ) && empty( $upgrade_id ) ) {
    wp_redirect( home_url() );
}
set_time_limit (600);

$houzez_need_register = false;
if ( !is_user_logged_in() ) {
    $houzez_need_register = true;
}

get_header();
global $houzez_local, $current_user;

wp_get_current_user();
$user_id                 = $current_user->ID;
$user_pack_id            = get_the_author_meta( 'package_id' , $user_id );
$user_package_activation = get_the_author_meta( 'package_activation' , $user_id );
$user_registered         = get_the_author_meta( 'user_registered' , $user_id );

$is_membership = 0;
$paid_submission_type = esc_html ( houzez_option('enable_paid_submission','') );
$membership_currency = houzez_option( 'currency_paid_submission' );
$currency_symbol = houzez_option( 'currency_symbol' );
$where_currency = houzez_option( 'currency_position' );
$enable_wireTransfer = houzez_option('enable_wireTransfer');
$enable_paypal = houzez_option('enable_paypal');
$enable_stripe = houzez_option('enable_stripe');
$user_show_roles = houzez_option('user_show_roles');
$show_hide_roles = houzez_option('show_hide_roles');
$enable_paid_submission = houzez_option('enable_paid_submission');
$packages_page_link = houzez_get_template_link('template/template-packages.php');
$stripe_processor_link = houzez_get_template_link('template/template-stripe-charge.php');

$panel_class = '';
$houzez_loggedin = false;
if ( is_user_logged_in() ) {
    get_template_part('template-parts/dashboard', 'menu');
    $panel_class = 'dashboard-with-panel';
    $houzez_loggedin = true;
    $column_classes = 'col-lg-10 col-md-9 col-sm-12 dashboard-inner-left';
    $sidebar_classes = 'col-lg-2 col-md-3 col-sm-12 dashboard-inner-right';
} else {
    $column_classes = 'col-lg-9 col-md-9 col-sm-12 dashboard-inner-left';
    $sidebar_classes = 'col-lg-3 col-md-3 col-sm-12 dashboard-inner-right';
}
?>

<div class="user-dashboard-right <?php echo esc_attr($panel_class);?>">

    <?php get_template_part( 'template-parts/dashboard-title' ); ?>

    <div class="dashboard-content-area">
        <div class="container">

            <?php get_template_part('template-parts/create-listing-top'); ?>

            <div class="row dashboard-inner-main">
                <div class="<?php echo esc_attr($column_classes); ?>">
                    <div class="membership-content class-for-register-msg">
                        <form name="houzez_checkout" method="post" class="houzez_payment_form" action="<?php echo $stripe_processor_link; ?>">
                            <?php if ( $houzez_need_register ) { ?>
                                <div class="info-title">
                                    <h2 class="info-title-left"> <?php esc_html_e('Account Information', 'houzez'); ?> </h2>
                                    <p class="already-account pull-right"> <?php esc_html_e('Already have an account?', 'houzez'); ?> <a href="#" data-toggle="modal" data-target="#pop-login"><strong><?php esc_html_e('Login here', 'houzez'); ?></strong></a></p>
                                </div>
                                <div class="info-detail">
                                    <div class="houzez_messages_register message"></div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="first_name"><?php esc_html_e('First Name', 'houzez'); ?></label>
                                                <input type="text" name="first_name" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="last_name"> <?php esc_html_e('Last Name', 'houzez'); ?> </label>
                                                <input type="text" name="last_name" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="last_name"> <?php esc_html_e('Username *', 'houzez'); ?> </label>
                                                <input type="text" name="username" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="useremail"> <?php esc_html_e('Email Address *', 'houzez'); ?> </label>
                                                <input type="email" name="useremail" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="password"> <?php esc_html_e('Password *', 'houzez'); ?> </label>
                                                <input type="password" name="register_pass" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="confirmpassword"> <?php esc_html_e('Confirm Password *', 'houzez'); ?> </label>
                                                <input type="password" name="register_pass_retype" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <?php if ( $user_show_roles != 0 ) : ?>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                
                                                <label for="role"> <?php esc_html_e('Select Type *', 'houzez'); ?> </label>
                                                <select name="role" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                                                    <option value=""> <?php esc_html_e('Select Type', 'houzez'); ?> </option>
                                                    <?php
                                                    if( $show_hide_roles['agent'] != 1 ) {
                                                        echo '<option value="houzez_agent"> '.houzez_option('agent_role').' </option>';
                                                    }
                                                    if( $show_hide_roles['agency'] != 1 ) {
                                                        echo '<option value="houzez_agency"> ' . houzez_option('agency_role') . ' </option>';
                                                    }
                                                    if( $show_hide_roles['owner'] != 1 ) {
                                                        echo '<option value="houzez_owner"> ' . houzez_option('owner_role') . '  </option>';
                                                    }
                                                    if( $show_hide_roles['buyer'] != 1 ) {
                                                        echo '<option value="houzez_buyer"> ' . houzez_option('buyer_role') . '  </option>';
                                                    }
                                                    if( $show_hide_roles['seller'] != 1 ) {
                                                        echo '<option value="houzez_seller"> ' . houzez_option('seller_role') . '  </option>';
                                                    }
                                                    if( $show_hide_roles['manager'] != 1 ) {
                                                        echo '<option value="houzez_manager"> ' . houzez_option('manager_role') . ' </option>';
                                                    }
                                                    ?>
                                                </select>
                                                
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php wp_nonce_field( 'houzez_register_nonce2', 'houzez_register_security2' ); ?>
                                        <input type="hidden" name="action" value="houzez_register_user_with_membership">
                                    </div>
                                </div>
                            <?php } ?>

                            <?php get_template_part('template-parts/payment-options'); ?>

                            <div class="info-title">
                                <h2 class="info-title-left"> <?php echo $houzez_local['payment_method']; ?> </h2>
                            </div>

                            <?php
                            if( $enable_paid_submission == 'membership' ) {
                                get_template_part('template-parts/membership/payment-methods');
                            } else if ( $enable_paid_submission == 'per_listing' || $enable_paid_submission == 'free_paid_listing' ) {
                                get_template_part('template-parts/per-listing/payment-methods');
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <div class="<?php echo esc_attr($sidebar_classes);?>">
                    <div class="dashboard-sidebar">
                        <div class="dashboard-sidebar-inner">
                            <div class="payment-side-block">
                                <?php
                                if( $enable_paid_submission == 'membership' ) {
                                    get_template_part('template-parts/membership/price');
                                } else if ( $enable_paid_submission == 'per_listing' || $enable_paid_submission == 'free_paid_listing' ) {
                                    get_template_part('template-parts/per-listing/price');
                                }
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