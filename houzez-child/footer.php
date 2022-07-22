<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content.
 *
 * @package Houzez
 * @since Houzez 1.0
 */
$copy_rights = houzez_option('copy_rights');
global $houzez_local;
?>

<?php if ( houzez_is_footer() ) { ?>

    <?php if( houzez_container_needed() && 
            !is_page_template('template-user-dashboard-package.php') &&
            !is_page_template('template-user-dashboard-membership.php') &&
            !is_page_template('template-user-dashboard-properties.php') &&
            !is_page_template('template-addon-payment.php') &&
            !is_page_template('template-advanced-package.php') &&
            !is_page_template('template-advanced-payment.php') &&
            !is_page_template('template-document-upload.php') &&
            !is_page_template('template-map-search.php') ) { ?>
    </div> <!--.container Start in header-->
    <?php } ?>
</div> <!--Start in header end #section-body-->

<?php get_template_part('template-parts/scroll-to-top'); ?>

<!--start footer section-->
<footer id="footer-section">
    <?php if (!is_page_template('template-user-dashboard-package.php') &&
            !is_page_template('template-user-dashboard-membership.php') &&
            !is_page_template('template-user-dashboard-properties.php') &&
            !is_page_template('template-addon-payment.php') &&
            !is_page_template('template-advanced-package.php') &&
            !is_page_template('template-advanced-payment.php') &&
            !is_page_template('template-document-upload.php') &&
            !is_page_template('template-map-search.php')) {

    if ( !is_active_sidebar( 'footer-sidebar-1' )
        && ! is_active_sidebar( 'footer-sidebar-2' )
        && ! is_active_sidebar( 'footer-sidebar-3' )
        && ! is_active_sidebar( 'footer-sidebar-4' )
        && ! is_active_sidebar( 'footer-sidebar-5' )
        && ! is_active_sidebar( 'footer-sidebar-6' ) )
        return;

    $footer_cols = houzez_option('footer_cols');
    if( $footer_cols == 'three_cols' ) {
        $f_3_classes = 'col-md-6 col-sm-12';
        $footer = 'footer footer-v2';
    } else {
        $f_3_classes = 'col-md-3 col-sm-6';
        $footer = 'footer';
    }
    ?>
    <div class="<?php echo esc_attr( $footer ); ?>">
        <div class="container">
            <div class="row">

                <?php
                if( $footer_cols === 'one_col' ) {
                    if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                        echo '<div class="col-md-12 col-sm-12">';
                            dynamic_sidebar( 'footer-sidebar-1' );
                        echo '</div>';
                    }
                } elseif( $footer_cols === 'two_col' ) {
                    if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                        echo '<div class="col-md-6 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-1' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                        echo '<div class="col-md-6 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-2' );
                        echo '</div>';
                    }
                } elseif( $footer_cols === 'three_cols_middle' ) {
                    if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                        echo '<div class="col-md-4 col-sm-12 col-xs-12">';
                            dynamic_sidebar( 'footer-sidebar-1' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                        echo '<div class="col-md-4 col-sm-12 col-xs-12">';
                            dynamic_sidebar( 'footer-sidebar-2' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                        echo '<div class="col-md-4 col-sm-12 col-xs-12">';
                            dynamic_sidebar( 'footer-sidebar-3' );
                        echo '</div>';
                    }
                } elseif( $footer_cols === 'three_cols' ) {
                    if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                        echo '<div class="col-md-3 col-sm-6 col-xs-12">';
                            dynamic_sidebar( 'footer-sidebar-1' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                        echo '<div class="col-md-3 col-sm-6 col-xs-12">';
                            dynamic_sidebar( 'footer-sidebar-2' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                        echo '<div class="col-md-6 col-sm-12 col-xs-12">';
                            dynamic_sidebar( 'footer-sidebar-3' );
                        echo '</div>';
                    }
                } elseif( $footer_cols === 'four_cols' ) {
                    if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                        echo '<div class="col-md-3 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-1' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                        echo '<div class="col-md-3 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-2' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                        echo '<div class="col-md-3 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-3' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-4' ) ) {
                        echo '<div class="col-md-3 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-4' );
                        echo '</div>';
                    }
                } elseif( $footer_cols === 'six_cols' ) {
                    if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                        echo '<div class="col-md-2 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-1' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                        echo '<div class="col-md-2 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-2' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                        echo '<div class="col-md-2 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-3' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-4' ) ) {
                        echo '<div class="col-md-2 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-4' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-5' ) ) {
                        echo '<div class="col-md-2 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-5' );
                        echo '</div>';
                    }
                    if ( is_active_sidebar( 'footer-sidebar-6' ) ) {
                        echo '<div class="col-md-2 col-sm-6">';
                            dynamic_sidebar( 'footer-sidebar-6' );
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <div class="footer-bottom">

    	<div class="container">
            <div class="row">
                <?php if( !empty($copy_rights) ) { ?>
                <div class="col-md-3 col-sm-3">
                    <div class="footer-col">
                        <p><?php echo ( $copy_rights ); ?></p>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-6 col-sm-6">
                    <div class="footer-col">
                        <div class="navi">
	                        <?php
							// Pages Menu
							if ( has_nav_menu( 'footer-menu' ) ) :
								wp_nav_menu( array (
									'theme_location' => 'footer-menu',
									'container' => '',
									'container_class' => '',
									'menu_class' => '',
									'menu_id' => 'footer-menu',
									'depth' => 1
								));
							endif;
							?>
						</div>

                    </div>
                </div>
                <?php if( houzez_option('social-footer') != '0' ) {
                 if( houzez_option('fs-facebook') != '' || houzez_option('fs-twitter') != '' || houzez_option('fs-linkedin') != '' || houzez_option('fs-googleplus') != '' || houzez_option('fs-instagram') != '' || houzez_option('fs-pinterest') != '' ) { ?>
                <div class="col-md-3 col-sm-3">
                    <div class="footer-col foot-social">
                        <p>
                            <?php echo $houzez_local['follow_us']; ?>
                           
                            <?php if( houzez_option('fs-facebook') != '' ){ ?>
					        	<a target="_blank" href="<?php echo esc_url(houzez_option('fs-facebook')); ?>"><i class="fa fa-facebook-square"></i></a>
					        <?php } ?>

					        <?php if( houzez_option('fs-twitter') != '' ){ ?>
					            <a target="_blank" href="<?php echo esc_url(houzez_option('fs-twitter')); ?>"><i class="fa fa-twitter-square"></i></a>
					        <?php } ?>

					        <?php if( houzez_option('fs-linkedin') != '' ){ ?>
					            <a target="_blank" href="<?php echo esc_url(houzez_option('fs-linkedin')); ?>"><i class="fa fa-linkedin-square"></i></a>
					        <?php } ?>

					        <?php if( houzez_option('fs-googleplus') != '' ){ ?>
					            <a target="_blank" href="<?php echo esc_url(houzez_option('fs-googleplus')); ?>"><i class="fa fa-google-plus-square"></i></a>
					        <?php } ?>

					        <?php if( houzez_option('fs-instagram') != '' ){ ?>
					            <a target="_blank" href="<?php echo esc_url(houzez_option('fs-instagram')); ?>"><i class="fa fa-instagram"></i></a>
					        <?php } ?>

					        <?php if( houzez_option('fs-pinterest') != '' ){ ?>
					            <a target="_blank" href="<?php echo esc_url(houzez_option('fs-pinterest')); ?>"><i class="fa fa-pinterest"></i></a>
					        <?php } ?>

					        <?php if( houzez_option('fs-yelp') != '' ){ ?>
                                <a target="_blank" href="<?php echo esc_url(houzez_option('fs-yelp')); ?>"><i class="fa fa-yelp"></i></a>
                            <?php } ?>
                            <?php if( houzez_option('fs-youtube') != '' ){ ?>
                                <a target="_blank" href="<?php echo esc_url(houzez_option('fs-youtube')); ?>"><i class="fa fa-youtube"></i></a>
                            <?php } ?>
                        </p>
                    </div>
                </div>
                <?php }
                } ?>

            </div>
        </div>

    </div><!-- End footer bottom -->

</footer>
<!--end footer section-->
<?php } else { // End splash template if ?>
    </div> <!--Start in header end #section-body-->
<?php } ?>

<?php wp_footer(); ?>

</body>
</html>