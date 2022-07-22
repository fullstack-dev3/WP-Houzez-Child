<?php

if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $post, $current_user;
wp_get_current_user();
$userID = $current_user->ID;

$ac_addon = $ac_payment = $ac_thankyou = '';

if ( is_page_template( 'template-user-dashboard-package.php' ) ) {
	$ac_addon = 'active';
}
if ( is_page_template( 'template-addon-payment.php' ) ) {
    $ac_payment = 'active';
}

?>
<ol class="pay-step-bar">
	<li class="pay-step-block <?php echo esc_attr( $ac_addon ); ?>">
        <span><?php esc_html_e( 'Select Add On', 'houzez' ); ?></span>
    </li>
    <li class="pay-step-block <?php echo esc_attr( $ac_payment ); ?>">
    	<span><?php esc_html_e( 'Payment', 'houzez' ); ?></span>
    </li>
    <li class="pay-step-block <?php echo esc_attr( $ac_thankyou ); ?>">
    	<span><?php esc_html_e( 'Done', 'houzez' ); ?></span>
    </li>
</ol>