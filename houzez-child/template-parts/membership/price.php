<h3 class="side-block-title"> <?php esc_html_e( 'Membership Package', 'houzez' ); ?> </h3>

<?php
$currency_symbol = '€';
$where_currency = houzez_option( 'currency_position' );
$select_packages_link = houzez_get_template_link('template-advanced-package.php');

if( isset( $_GET['selected_package'] ) || isset( $_GET['state'] ) ) {
    $selected_package_id     = isset( $_GET['selected_package'] ) ? $_GET['selected_package'] : '';

    $option = (isset($_GET['option'])) ? $_GET['option'] : 'option1';

    if (isset($_GET['state'])) {
        $value = explode(',', urldecode($_GET['state']));

        $option = $value[0];
        $selected_package_id = $value[1];
    }

    $pack_price              = get_post_meta( $selected_package_id, 'fave_payment_' . $option, true );

    $pack_listings           = get_post_meta( $selected_package_id, 'fave_package_listings', true );
    $pack_featured_listings  = get_post_meta( $selected_package_id, 'fave_package_featured_listings', true );
    $pack_unlimited_listings = get_post_meta( $selected_package_id, 'fave_unlimited_listings', true );
    $pack_billing_period     = get_post_meta( $selected_package_id, 'fave_billing_time_unit', true );
    $pack_billing_frquency   = get_post_meta( $selected_package_id, 'fave_billing_unit', true );
    $fave_package_popular    = get_post_meta( $selected_package_id, 'fave_package_popular', true );

    if( $pack_billing_frquency > 1 ) {
        $pack_billing_period .='s';
    }
    
    $package_price = $currency_symbol . ' ' . $pack_price;

    ?>
    <ul class="pkg-total-list">
        <?php if ( is_user_logged_in() ) { ?>
        <li>
            <span id="houzez_package_name" class="pull-left"><?php echo get_the_title( $selected_package_id ); ?></span>
            <span class="pull-right"><a href="<?php echo esc_url( $select_packages_link ); ?>"><?php esc_html_e( 'Change Package', 'houzez' ); ?></a></span>
        </li>
        <?php } else { ?>
            <li>
                <span id="houzez_package_name" class="pull-left"><?php esc_html_e( 'Package Name', 'houzez' ); ?></span>
                <span class="pull-right"><a><?php echo get_the_title( $selected_package_id ); ?></a></span>
            </li>
        <?php } ?>
        <li>
            <span class="pull-left"><?php esc_html_e( 'Package Time:', 'houzez' ); ?></span>
            <span class="pull-right"><strong><?php echo esc_attr( $pack_billing_frquency ).' '.HOUZEZ_billing_period( $pack_billing_period ); ?></strong></span>
        </li>
        <li>
            <span class="pull-left"><?php esc_html_e( 'Listing Included:', 'houzez' ); ?></span>
            <span class="pull-right">
                <?php if( $pack_unlimited_listings == 1 ) { ?>
                    <strong><?php esc_html_e( 'Unlimited Listings', 'houzez' ); ?></strong>
                <?php } else { ?>
                    <strong><?php echo esc_attr( $pack_listings ); ?></strong>
                <?php } ?>
            </span>
        </li>
        <li>
            <span class="pull-left"><?php esc_html_e( 'Featured Listing Included:', 'houzez' ); ?></span>
            <span class="pull-right"><strong><?php echo esc_attr( $pack_featured_listings ); ?></strong></span>
        </li>
        <li>
            <span class="pull-left"><?php esc_html_e( 'Total Price:', 'houzez' ); ?></span>
            <span class="pull-right"><?php echo esc_attr( $package_price ); ?></span>
        </li>
    </ul>
<?php } ?>
