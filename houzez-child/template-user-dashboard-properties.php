<?php
/**
 * Template Name: User Dashboard Properties
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $houzez_local, $prop_featured, $current_user, $post;

wp_get_current_user();
$userID         = $current_user->ID;
$user_login     = $current_user->user_login;
$edit_link      = houzez_dashboard_add_listing();
$paid_submission_type = esc_html ( houzez_option('enable_paid_submission','') );
$packages_page_link = houzez_get_template_link('template-advanced-package.php');

get_header();

$meta_query = array();

if( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'approved' ) {
    $qry_status = 'publish';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'package' ) {
    $qry_status = 'publish';

    array_push($meta_query, array(
        'key' => 'fave_featured',
        'value' => 1,
        'compare' => '=',
    ));

    array_push($meta_query, array(
        'key' => 'fave_week',
        'value' => 1,
        'compare' => '=',
    ));

    $meta_query['relation'] = 'OR';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'pending' ) {
    $qry_status = 'pending';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'expired' ) {
    $qry_status = 'expired';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'draft' ) {
    $qry_status = 'draft';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'on_hold' ) {
    $qry_status = 'on_hold';
} else {
    $qry_status = 'any';
}

$sortby = '';

if( isset( $_GET['sortby'] ) ) {
    $sortby = $_GET['sortby'];
}

$no_of_prop   =  '10';
$paged        = (get_query_var('paged')) ? get_query_var('paged') : 1;

if (empty($meta_query)) {
    $args = array(
        'post_type'      => 'property',
        'author'         => $userID,
        'paged'          => $paged,
        'posts_per_page' => $no_of_prop,
        'post_status'    => array( $qry_status )
    );

    if( isset ( $_GET['keyword'] ) ) {
        $keyword = trim( $_GET['keyword'] );
        if ( ! empty( $keyword ) ) {
            $args['s'] = $keyword;
        }
    }

    $args = houzez_prop_sort ( $args );
    $prop_qry = new WP_Query($args);
} else {
    $args = array(
        'post_type'      => 'property',
        'author'         => $userID,
        'posts_per_page' => -1,
        'post_status'    => array( $qry_status ),
        'meta_query'     => $meta_query
    );

    $featured = new WP_Query($args);

    $ids = array();
    if ($featured->have_posts())
        $ids = wp_list_pluck( $featured->posts, 'ID' );

    $args = array(
        'post_type'      => 'property',
        'author'         => $userID,
        'paged'          => $paged,
        'posts_per_page' => $no_of_prop,
        'post_status'    => array( $qry_status ),
        'post__not_in'   => $ids
    );

    if( isset ( $_GET['keyword'] ) ) {
        $keyword = trim( $_GET['keyword'] );
        if ( ! empty( $keyword ) ) {
            $args['s'] = $keyword;
        }
    }

    $args = houzez_prop_sort ( $args );
    $prop_qry = new WP_Query($args);
}

?>
<?php get_template_part( 'template-parts/dashboard', 'menu' ); ?>

    <div class="user-dashboard-right dashboard-with-panel">

        <?php get_template_part( 'template-parts/dashboard-title' ); ?>

        <div class="dashboard-content-area dashboard-fix">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="my-profile-search">
                            <div class="profile-top-left">
                                <form method="get" action="">
                                    <input type="hidden" name="prop_status" value="<?php echo isset($_GET['prop_status']) ? $_GET['prop_status'] : '';?>">
                                    <div class="single-input-search">
                                        <input class="form-control" name="keyword" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '';?>" placeholder="<?php echo esc_html__('Search listing', 'houzez'); ?>" type="text">
                                        <button type="submit"></button>
                                    </div>
                                </form>
                            </div>
                            <div class="profile-top-right">
                                <div class="sort-tab text-right">
                                    <?php esc_html_e( 'Sort by:', 'houzez' ); ?>
                                    <select id="sort_properties" class="selectpicker bs-select-hidden" title="" data-live-search-style="begins" data-live-search="false">
                                        <option value=""><?php esc_html_e( 'Default Order', 'houzez' ); ?></option>
                                        <option <?php if( $sortby == 'a_price' ) { echo "selected"; } ?> value="a_price"><?php esc_html_e( 'Price (Low to High)', 'houzez' ); ?></option>
                                        <option <?php if( $sortby == 'd_price' ) { echo "selected"; } ?> value="d_price"><?php esc_html_e( 'Price (High to Low)', 'houzez' ); ?></option>
                                        <option <?php if( $sortby == 'featured' ) { echo "selected"; } ?> value="featured"><?php esc_html_e( 'Featured', 'houzez' ); ?></option>
                                        <option <?php if( $sortby == 'a_date' ) { echo "selected"; } ?> value="a_date"><?php esc_html_e( 'Date Old to New', 'houzez' ); ?></option>
                                        <option <?php if( $sortby == 'd_date' ) { echo "selected"; } ?> value="d_date"><?php esc_html_e( 'Date New to Old', 'houzez' ); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="my-property-listing">

                            <?php if( $prop_qry->have_posts() ) { ?>

                                <div class="row grid-row">
                                    <?php

                                    while ($prop_qry->have_posts()): $prop_qry->the_post();

                                        get_template_part('template-parts/dashboard_property_unit');

                                    endwhile;
                                    ?>
                                </div>
                                <?php
                            } else {
                                print '<h4>'.$houzez_local['properties_not_found'].'</h4>';
                            }?>

                            <hr>
                            
                            <!--start Pagination-->
                            <?php houzez_pagination( $prop_qry->max_num_pages, $range = 2 ); ?>
                            <!--start Pagination-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>