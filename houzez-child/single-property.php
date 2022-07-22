<?php get_header();

global $post, $property_nav, $property_layout, $property_top_area, $current_user, $all_meta_for_user, $logged_in_to_view, $map_in_section;
$current_user = wp_get_current_user();
$all_meta_for_user = get_user_meta( $current_user->ID );
$post_meta_data       = get_post_custom($post->ID);
$houzez_prop_id              = get_post_meta( get_the_ID(), 'fave_property_id', true );
$prop_images          = get_post_meta( get_the_ID(), 'fave_property_images', false );
$prop_address         = get_post_meta( get_the_ID(), 'fave_property_map_address', true );
$prop_featured        = get_post_meta( get_the_ID(), 'fave_featured', true );
$prop_video_img       = get_post_meta( get_the_ID(), 'fave_video_image', true );
$prop_video_url       = get_post_meta( get_the_ID(), 'fave_video_url', true );
$property_location    = get_post_meta( get_the_ID(), 'fave_property_location',true);
$virtual_tour         = get_post_meta( $post->ID, 'fave_virtual_tour', true );
$property_map         = get_post_meta( get_the_ID(), 'fave_property_map',true);
$property_streetView  = get_post_meta( get_the_ID(), 'fave_property_map_street_view',true);
$prop_floor_plan      = get_post_meta( get_the_ID(), 'fave_floor_plans_enable', true );
$floor_plans          = get_post_meta( get_the_ID(), 'floor_plans', true );
$enable_multi_units   = get_post_meta( get_the_ID(), 'fave_multiunit_plans_enable', true );
$multi_units          = get_post_meta( get_the_ID(), 'fave_multi_units', true );
$prop_features        = wp_get_post_terms( get_the_ID(), 'property_feature', array("fields" => "all"));
$sticky_sidebar = houzez_option('sticky_sidebar');
$prop_price = get_post_meta( get_the_ID(), 'fave_property_price', true );
$prop_size = get_post_meta( get_the_ID(), 'fave_property_size', true );
$bedrooms = get_post_meta( get_the_ID(), 'fave_property_bedrooms', true );
$bathrooms = get_post_meta( get_the_ID(), 'fave_property_bathrooms', true );
$year_built = get_post_meta( get_the_ID(), 'fave_property_year', true );
$garage = get_post_meta( get_the_ID(), 'fave_property_garage', true );
$garage_size = get_post_meta( get_the_ID(), 'fave_property_garage_size', true );
$single_top_area = get_post_meta( get_the_ID(), 'fave_single_top_area', true );
$single_content_area = get_post_meta( get_the_ID(), 'fave_single_content_area', true );
$property_top_area = houzez_option('prop-top-area');
$property_layout = houzez_option('prop-content-layout');
$property_reviews = houzez_option('property_reviews');
$enable_next_prev_prop = houzez_option('enable_next_prev_prop');
$map_in_section = houzez_option('map_in_section');
if(isset($_GET['mapinsection']) && $_GET['mapinsection'] == 'yes') {
    $map_in_section = 1;
}
$logged_in_to_view = get_post_meta( $post->ID, 'fave_loggedintoview', true );
$houzez_prop_detail = false;

if( !empty( $houzez_prop_id ) ||
    !empty( $prop_price ) ||
    !empty( $prop_size ) ||
    !empty( $bedrooms ) ||
    !empty( $bathrooms ) ||
    !empty( $year_built ) ||
    !empty( $garage )
) {
    $houzez_prop_detail = true;
}

$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'houzez-single-big-size' );
$tab_height = $featured_image[2];

if( $tab_height < 600 ) {
    $tab_height = '600';
}


if( !empty( $single_top_area ) && $single_top_area != 'global' ) {
    $property_top_area = $single_top_area;
}

if( !empty( $single_content_area ) && $single_content_area != 'global' ) {
    $property_layout = $single_content_area;
}

/* For demo purpose only */
if( isset( $_GET['s_top'] ) ) {
    $property_top_area = $_GET['s_top'];
}
if( isset( $_GET['s_layout'] ) ) {
    $property_layout = $_GET['s_layout'];
}

// Print property
$fave_print_page_link = houzez_get_template_link('template/template-print.php');
$print_link = add_query_arg( 'propID', $post->ID, $fave_print_page_link );
houzez_count_property_views( $post->ID );
?>

<?php if( have_posts() ): while( have_posts() ): the_post(); ?>
    <!--start detail top-->

    <?php
     if( get_post_status() == 'expired') { ?>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <main class="site-main" role="main" style="min-height: 400px">
                    <div class="error-404-page text-center">
                        
                        <p><?php esc_html_e('This listing is expired!', 'houzez'); ?></p>
                        
                    </div><!-- 404-page text-center -->
                </main><!-- site-main -->
            </div><!-- col-lg-8 col-md-8 col-sm-8 col-xs-12 -->
        </div><!-- .row --> 

    <?php
    } else {
    ?>

    <?php
    if( $property_top_area == 'v1' ) {
        get_template_part('property-details/toparea', 'v1');
    } elseif ( $property_top_area == 'v2' ) {
        get_template_part('property-details/toparea', 'v2');
    } elseif ( $property_top_area == 'v3' ) {
        get_template_part('property-details/toparea', 'v3');
    } elseif ( $property_top_area == 'v5' ) {
        get_template_part('property-details/toparea', 'v5');
    }
    ?>
    <!--end detail top-->

    <?php
    if ( $property_top_area == 'v4' ) {
        get_template_part( 'property-details/slideshow-for-v4');
    }
    ?>

    <!--start detail content-->
    <section class="section-detail-content">

        <?php if( $property_layout == 'v2' ) { ?>

        <?php
        if($logged_in_to_view == 1 && !is_user_logged_in()) { ?>

            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                        <?php get_template_part( 'property-details/login_required'); ?>
                    </div>
                </div>
            </div>

        <?php } else { ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                    <div class="detail-bar detail-bar-full houzez-single-property-v2">
                    <?php get_template_part( 'property-details/single-property', 'v2'); ?>

                    <?php get_template_part( 'property-details/v2/property', 'similar' ); ?>

                    <?php if( $property_reviews != 0 ) { get_template_part( 'property-details/property', 'ratings' ); } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php } else { ?>

        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 container-contentbar">

                    <?php get_template_part('property-details/detail-nav'); ?>

                    <?php
                    if( $logged_in_to_view == 1 && !is_user_logged_in()) {

                        get_template_part( 'property-details/login_required');

                    } else { ?>

                    <div class="detail-bar">
                    	<?php
                    		$terms = get_the_terms(get_the_ID(), 'property_lifestyle');

                    		if ($terms) {
                    	?>
                    	<div class="lifestyle">
                		<?php
                			foreach ($terms as $term) {
                                echo '<a href="' . site_url() . '/' . $term->taxonomy . '/' . $term->slug . '">';
                				echo '<span>' . $term->name . '</span>';
                                echo '</a>';
                			}
                		?>
                    	</div>
                    	<?php
                    		}
                    	?>

                        <?php
                        if ( $property_top_area == 'v3' ) {
                            get_template_part( 'property-details/slideshow');
                        }

                        if( $property_layout == 'tabs' ) {
                            get_template_part( 'property-details/single-property', 'tabs');
                        } else if( $property_layout == 'tabs-vertical' ) {
                            get_template_part( 'property-details/single-property', 'tabs-vertical');
                        } else {
                            get_template_part( 'property-details/single-property', 'simple');
                        }

                        ?>

                        <?php //get_template_part( 'property-details/property', 'similer' ); ?>

                        <?php if( $property_reviews != 0 ) { get_template_part( 'property-details/property', 'ratings' ); } ?>

                        <?php if( $enable_next_prev_prop != 0 ) { get_template_part( 'property-details/next', 'prev' ); } ?>

                    </div>
                    <?php } ?>
                </div>
                <?php if( $property_top_area != 'v5') { ?>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 col-md-offset-0 col-sm-offset-3 container-sidebar <?php if( $sticky_sidebar['single_property'] != 0 ){ echo 'houzez_sticky'; }?>">
                    <?php get_sidebar('property'); ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </section>
    <!--end detail content-->

    </div> <!--Start in header, end #section-body-->

    <!--start lightbox-->
    <?php get_template_part( 'property-details/lightbox' ); ?>
    <!-- End Lightbox-->
    <?php } // end expired else?>

<?php endwhile; endif; ?>

<?php get_footer('../houzez/single-property'); ?>