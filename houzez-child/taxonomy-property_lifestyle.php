<?php
/**
 * Taxonomy Property Lifestyle
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 08/01/16
 * Time: 4:26 PM
 */
get_header();

global $post, $taxonomy_title, $taxonomy_name, $listing_view;

$listing_view = houzez_option('taxonomy_posts_layout');

if( $listing_view == 'grid-view-3-col' ) {
    $listing_view_class = 'grid-view grid-view-3-col';
} else if( $listing_view == 'listing-style-3' ) {
    $listing_view_class = 'grid-view';
} else if( $listing_view == 'listing-style-2-grid-view' ) {
    $listing_view_class = 'grid-view listing-style-2-grid-view';
} else if( $listing_view == 'listing-style-2-grid-view-3-col' ) {
    $listing_view_class = 'grid-view grid-view-3-col listing-style-2-grid-view';
} else if( $listing_view == 'listing-style-2' ) {
    $listing_view_class = 'list-view listing-style-2';
} else {
    $listing_view_class = $listing_view;
}

$prop_featured = get_post_meta( get_the_ID(), 'fave_featured', true );
$prop_week     = get_post_meta( get_the_ID(), 'fave_week', true );

// Title
$current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
$taxonomy_title = $current_term->name;
$sticky_sidebar = houzez_option('sticky_sidebar');
$taxonomy_name = get_query_var( 'taxonomy' );

$taxonomy_layout = houzez_option('taxonomy_layout');
$taxonomy_num_posts = houzez_option('taxonomy_num_posts');

if( $taxonomy_layout == 'no-sidebar' ) {
    $content_classes = 'col-lg-12 col-md-12 col-sm-12';
} else if( $taxonomy_layout == 'left-sidebar' ) {
    $content_classes = 'col-lg-8 col-md-8 col-sm-12 col-xs-12 list-grid-area container-contentbar';
} else if( $taxonomy_layout == 'right-sidebar' ) {
    $content_classes = 'col-lg-8 col-md-8 col-sm-12 col-xs-12 list-grid-area pull-left container-contentbar';
} else {
    $content_classes = 'col-lg-8 col-md-8 col-sm-12 col-xs-12 list-grid-area container-contentbar';
}

$number_of_prop = $taxonomy_num_posts;
if(!$number_of_prop){
    $number_of_prop = 9;
}
?>

<div class="page-title breadcrumb-top">
    <div class="row">
        <div class="col-sm-12">
            <?php get_template_part( 'inc/breadcrumb' ); ?>

            <div class="page-title-left lifestyle-tag">
                <h2><?php echo esc_attr( $taxonomy_title ); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="<?php echo esc_attr($content_classes); ?>">
        <div id="content-area">

            <!--start property items-->
            <div class="property-listing lifestyle <?php echo esc_attr($listing_view_class); ?>">

                <?php
                $sort_args = array('posts_per_page' => $number_of_prop, 'post_status' => 'publish');
                $sort_args = houzez_prop_sort($sort_args);

                global $wp_query;
                $args = array_merge( $wp_query->query_vars, $sort_args );

                query_posts( $args );

                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();

                ?>
                <div class="item-wrap infobox_trigger">
				    <div class="property-item row">
				        <div class="col-md-4">
				            <figure class="item-thumb">

				                <?php if( $prop_featured == 1 ) { ?>
				                    <span class="label-featured label">
				                        <?php echo esc_html__( 'Featured', 'houzez' ); ?>
				                    </span>
				                <?php } ?>
				                <?php if( $prop_week == 1 ) { ?>
				                    <span class="label-week label">
				                        <?php echo esc_html__( 'Property of the Week', 'houzez' ); ?>
				                    </span>
				                <?php } ?>

				                <div class="label-wrap label-right">
				                    <?php get_template_part('template-parts/listing', 'status' ); ?>
				                </div>

				                <a href="<?php the_permalink() ?>" class="hover-effect">
				                    <?php
				                    if( has_post_thumbnail( $post->ID ) ) {
				                        the_post_thumbnail( 'houzez-property-thumb-image-v2' );
				                    }else{
				                        houzez_image_placeholder( 'houzez-property-thumb-image-v2' );
				                    }
				                    ?>
				                </a>

				                <ul class="actions">
				                    <?php if( $disable_favorite != 0 ) { ?>
				                    <li>
				                        <span class="add_fav" data-placement="top" data-toggle="tooltip" data-original-title="<?php esc_html_e('Favorite', 'houzez'); ?>" data-propid="<?php echo intval( $post->ID ); ?>">
				                            <i class="fa fa-heart"></i>
				                        </span>
				                    </li>
				                    <?php } ?>

				                    <?php if( $disable_photo_count != 0 ) { ?>
				                    <li>
				                        <span data-toggle="tooltip" data-placement="top" title="(<?php echo count( $prop_images ); ?>) <?php echo $houzez_local['photos']; ?>">
				                            <i class="fa fa-camera"></i>
				                        </span>
				                    </li>
				                    <?php } ?>
				                </ul>
				            </figure>
				        </div>
				        <div class="col-md-8">
				        	<div class="row">				        		
					            <div class="col-md-9 item-title">
					            	<a href="<?php the_permalink() ?>" class="hover-effect">
					                <?php
					                    echo '<h2 class="property-title">'. esc_attr( get_the_title() ). '</h2>';
					                ?>
					            	</a>
					            </div>
					            <div class="col-md-3 item-price-block">
					                <span class="item-price">
					                    <?php echo houzez_listing_price_v1(); ?>
					                </span>
					            </div>
				        	</div>

				            <div class="item-detail">
				                <p><?php echo wp_trim_words( get_the_content(), 20 ); ?></p>
				            </div>

				            <div class="item-info">
				                <?php 
				                    $propID = get_the_ID();
				                    $prop_bed     = get_post_meta( get_the_ID(), 'fave_property_bedrooms', true );
				                    $prop_bath     = get_post_meta( get_the_ID(), 'fave_property_bathrooms', true );
				                    $prop_size     = get_post_meta( $propID, 'fave_property_size', true );

				                    if (empty($prop_bed)) $prop_bed = 0;
				                    if (empty($prop_bath)) $prop_bath = 0;
				                    if (empty($prop_size)) $prop_size = 0;
				                ?>
				                <ul class="item-amenities">
				                    <li>
				                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/rooms.png">
				                        <span><?php echo $prop_bed; ?></span>
				                    </li>
				                    <li>
				                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/bathtub.png">
				                        <span><?php echo $prop_bath; ?></span>
				                    </li>
				                    <li>
				                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/house.png">
				                        <span><?php echo $prop_size; ?> m²</span>
				                    </li>
				                    <li>
				                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn btn-primary">
				                            <?php echo esc_html__( 'Details >', 'houzez' ); ?>
				                        </a>
				                    </li>
				                </ul>
				            </div>
				        </div>
				    </div>
				</div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                    ?>
                    <h4><?php esc_html_e('Sorry No Result Found', 'houzez') ?></h4>
                    <?php
                endif;
                ?>

            </div>
            <!--end property items-->

            <hr>

            <!--start Pagination-->
            <?php houzez_pagination( $wp_query->max_num_pages, $range = 2 ); ?>
            <!--start Pagination-->

        </div>
    </div><!-- end container-content -->

    <?php if( $taxonomy_layout != 'no-sidebar' ) { ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 col-md-offset-0 col-sm-offset-3 container-sidebar <?php if( $sticky_sidebar['property_listings'] != 0 ){ echo 'houzez_sticky'; }?>">
        <?php get_sidebar('property'); ?>
    </div> <!-- end container-sidebar -->
    <?php } ?>

</div>

<?php get_footer(); ?>