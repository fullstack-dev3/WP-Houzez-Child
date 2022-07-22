<?php
/**
 * Template Name: Packages
 */
global $houzez_local, $current_user;

wp_get_current_user();

get_header();

get_template_part( 'template-parts/dashboard', 'menu' ); ?>

<div class="user-dashboard-right dashboard-with-panel">

    <?php get_template_part( 'template-parts/dashboard-title' ); ?>

    <div class="dashboard-content-area">
        <div class="container">

            <?php get_template_part('template-parts/create-listing-top'); ?>

            <div class="houzez-module package-table-module">

            <?php
            if( have_posts() ):
                while( have_posts() ): the_post();
                    $content = get_the_content();
                endwhile;
            endif;
            
            wp_reset_postdata();

            if( !empty($content) ) {
                the_content();
            } else {
                $args = array(
                    'post_type'       => 'houzez_packages',
                    'posts_per_page'  => -1,
                    'meta_query'      =>  array(
                        array(
                            'key' => 'fave_package_visible',
                            'value' => 'yes',
                            'compare' => '=',
                        )
                    )
                );

                $fave_qry = new WP_Query($args);

                $total_packages = $first_pkg_column = '';
                $total_packages = $fave_qry->found_posts;

                if( $total_packages == 3 ) {
                    $pkg_classes = 'col-md-4 col-sm-4 col-xs-12';
                } else if( $total_packages == 4 ) {
                    $pkg_classes = 'col-md-3 col-sm-6';
                } else if( $total_packages == 2 ) {
                    $pkg_classes = 'col-md-4 col-sm-6';
                } else if( $total_packages == 1 ) {
                    $pkg_classes = 'col-md-4 col-sm-12';
                } else {
                    $pkg_classes = 'col-md-3 col-sm-6';
                }

                $i = 0;
                while( $fave_qry->have_posts() ): $fave_qry->the_post(); $i++;

                    $pack_listings           = get_post_meta( get_the_ID(), 'fave_package_listings', true );
                    $pack_unlimited_listings = get_post_meta( get_the_ID(), 'fave_unlimited_listings', true );
                    $pack_encrypt_doc = get_post_meta( get_the_ID(), 'fave_encrypt_doc', true );

                    $pack_payment_option1 = get_post_meta( get_the_ID(), 'fave_payment_option1', true );
                    $pack_payment_option2 = get_post_meta( get_the_ID(), 'fave_payment_option2', true );
                    $pack_payment_option3 = get_post_meta( get_the_ID(), 'fave_payment_option3', true );
                    $pack_payment_option4 = get_post_meta( get_the_ID(), 'fave_payment_option4', true );
                    
                    $process_link = '';
                    if ($pack_encrypt_doc == 1) {
                        $upload_page_link = houzez_get_template_link('template-document-upload.php');
                        $process_link = add_query_arg( 'selected_package', get_the_ID(), $upload_page_link );
                    } else {
                        $payment_page_link = houzez_get_template_link('template-advanced-payment.php');
                        $process_link = add_query_arg( 'selected_package', get_the_ID(), $payment_page_link );
                    }


                    if( $i == 1 && $total_packages == 2 ) {
                        $first_pkg_column = 'col-md-offset-2 col-sm-offset-0';
                    } else if (  $i == 1 && $total_packages == 1  ) {
                        $first_pkg_column = 'col-md-offset-4 col-sm-offset-0';
                    } else {
                        $first_pkg_column = '';
                    }

                    ?>

                    <div class="<?php echo esc_attr( $pkg_classes.' '.$first_pkg_column ); ?>">
                        <div class="package-block">
                            <h3 class="package-title"><?php the_title(); ?></h3>
                            <ul class="package-list">
                            <?php if( $pack_unlimited_listings == 1 ) { ?>
                                <li>
                                    <span><?php echo $houzez_local['unlimited_listings']; ?></span>
                                </li>
                            <?php } else { ?>
                                <?php if ($pack_listings != '' && $pack_listings > 0) { ?>
                                <li>
                                    <?php if ($pack_listings == 1) { ?>
                                    <span><?php echo esc_attr('One Listing'); ?></span>
                                    <?php } else if ($pack_listings > 1 && $pack_listings < 6) { ?>
                                    <span><?php echo esc_attr('1-5 Listings'); ?></span>
                                    <?php } else if ($pack_listings > 5 && $pack_listings < 11) { ?>
                                    <span><?php echo esc_attr('6-10 Listings'); ?></span>
                                    <?php } else { ?>
                                    <span><?php echo esc_attr($pack_listings . ' Listings'); ?></span>
                                    <?php }?>
                                </li>                                                
                                <?php }?>
                            <?php } ?>
                            <?php if ($pack_encrypt_doc == 1) { ?>
                                <li>
                                    <span>
                                        <?php echo esc_attr('Encryption and Document Storage');?>
                                    </span>
                                </li>
                            <?php } ?>
                            </ul>

                            <div class="package-content">
                                <?php
                                    if (($pack_payment_option1 != '' && $pack_payment_option1 > 0) ||
                                        ($pack_payment_option2 != '' && $pack_payment_option2 > 0) ||
                                        ($pack_payment_option3 != '' && $pack_payment_option3 > 0) ||
                                        ($pack_payment_option4 != '' && $pack_payment_option4 > 0)) {
                                ?>
                                <p><?php echo esc_attr('Paid Listing Options'); ?></p>
                                <?php } ?>

                                <?php if ($pack_payment_option1 != '' && $pack_payment_option1 > 0) { ?>
                                <p>
                                    <?php echo esc_attr('One-Time(60 Days, €' . $pack_payment_option1 . ')'); ?>
                                </p>
                                <?php } ?>

                                <?php if ($pack_payment_option2 != '' && $pack_payment_option2 > 0) { ?>
                                <p>
                                    <?php echo esc_attr('Monthly (€' . $pack_payment_option2 . ') on a recurring basis.'); ?>
                                </p>
                                <?php } ?>

                                <?php if ($pack_payment_option3 != '' && $pack_payment_option3 > 0) { ?>
                                <p>
                                    <?php echo esc_attr('Quarterly (€' . $pack_payment_option3 . ') on a recurring basis.'); ?>
                                </p>
                                <?php } ?>

                                <?php if ($pack_payment_option4 != '' && $pack_payment_option4 > 0) { ?>
                                <p>
                                    <?php echo esc_attr('Semi Annually (€' . $pack_payment_option4 . ') on a recurring basis.'); ?>
                                </p>
                                <?php } ?>
                            </div>

                            <div class="package-link">
                                <a href="<?php echo esc_url($process_link); ?>" class="btn btn-primary btn-lg">
                                    <?php echo $houzez_local['get_started']; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>