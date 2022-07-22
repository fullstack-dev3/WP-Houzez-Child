<?php
/**
 * Template for property units
 */
global $post,
       $edit_link,
       $prop_address,
       $payment_status,
       $dashboard_listings,
       $houzez_local,
       $property_status_text,
       $price_per_submission,
       $price_featured_submission,
       $currency, $paid_submission_type;

$prop_featured = get_post_meta( get_the_ID(), 'fave_featured', true );
$prop_week = get_post_meta( get_the_ID(), 'fave_week', true );
$houzez_local['week'] = 'Property of the Week';

$post_id    = get_the_ID();
$edit_link  = add_query_arg( 'edit_property', $post_id, $edit_link ) ;
$delete_link  = add_query_arg( 'property_id', $post_id, $dashboard_listings ) ;
$property_status = get_post_status ( $post->ID );
$property_status_text = $property_status;
$payment_status = get_post_meta( $post_id, 'fave_payment_status', true );

$paid_submission_type  = houzez_option('enable_paid_submission');
$price_per_submission = houzez_option('price_listing_submission');
$price_featured_submission = houzez_option('price_featured_listing_submission');
$price_per_submission = floatval($price_per_submission);
$price_featured_submission = floatval($price_featured_submission);
$currency = houzez_option('currency_paid_submission');

$add_floor_plans = houzez_get_template_link_2('template/user_dashboard_floor_plans.php');
$payment_page = houzez_get_template_link('template/template-payment.php');
$payment_page_link = add_query_arg( 'prop-id', $post_id, $payment_page );
$payment_page_link_featured = add_query_arg( 'upgrade_id', $post_id, $payment_page );
$add_floor_plans_link = add_query_arg( 'listing_id', $post_id, $add_floor_plans );

$add_multiunits = houzez_get_template_link_2('template/user_dashboard_multi_units.php');
$add_multiunits_link = add_query_arg( 'listing_id', $post_id, $add_multiunits );

$dashboard_package = houzez_get_template_link_2('template-user-dashboard-package.php');

if( $property_status == 'publish' ) {
    $property_status = '<span class="label label-success">'.esc_html__('Approved', 'houzez').'</span>';
} elseif( $property_status == 'on_hold' ) {
    $property_status = '<span class="label label-success">'.$houzez_local['on_hold'].'</span>';
} elseif( $property_status == 'pending' ) {
    $property_status = '<span class="label label-warning">'.esc_html__('Under Approved', 'houzez').'</span>';
}  elseif( $property_status == 'expired' ) {
    $property_status = '<span class="label label-danger">'.esc_html__('Expired', 'houzez').'</span>';
    $payment_status_label = '<span class="label label-danger">'.esc_html__('Expired', 'houzez').'</span>';
} else {
    $property_status = '';
}

if( $property_status_text != 'expired' ) {
    if ($paid_submission_type != 'no' && $paid_submission_type != 'membership' && $paid_submission_type != 'free_paid_listing' ) {
        if ($payment_status == 'paid') {
            $payment_status_label = '<span class="label label-success">' . esc_html__('PAID', 'houzez') . '</span>';
        } elseif ($payment_status == 'not_paid') {
            $payment_status_label = '<span class="label label-warning">' . esc_html__('NOT PAID', 'houzez') . '</span>';
        } else {
            $payment_status_label = '';
        }
    } else {
        $payment_status_label = '';
    }
}
?>

<div class="item-wrap">
    <div class="media my-property">
        <div class="media-left">
            <div class="figure-block">
                <figure class="item-thumb">
                    <a href="<?php the_permalink() ?>">
                        <?php
                        if( has_post_thumbnail( ) ) {
                            the_post_thumbnail( 'houzez-widget-prop' );
                        }else{
                            houzez_image_placeholder( 'houzez-widget-prop' );
                        }
                        ?>
                    </a>
                    <?php if( $prop_featured != 0 ) { ?>
                        <span class="label-featured label"><?php echo $houzez_local['featured'] ?></span>
                    <?php } ?>
                    <?php if( $prop_week != 0 ) { ?>
                        <span class="label-week label"><?php echo $houzez_local['week'] ?></span>
                    <?php } ?>
                </figure>
            </div>
        </div>
        <div class="media-body media-middle">
            <div class="my-description">
                <h4 class="my-heading">
                    <a href="<?php the_permalink() ?>"><?php the_title(); ?>
                        <?php echo $payment_status_label; ?>
                    </a>
                </h4>
                <?php if( !empty( $prop_address )) { ?>
                    <address class="address"><?php echo esc_attr( $prop_address ); ?></address>
                <?php } ?>
                <div class="status">
                    <p>
                        <span><strong><?php esc_html_e( 'Status:', 'houzez' ); ?></strong> <?php echo houzez_taxonomy_simple( 'property_status' ); ?></span>
                        <span><strong><?php esc_html_e( 'Price:', 'houzez' ); ?></strong> <?php echo houzez_listing_price(); ?></span>
                        <?php
                        $listing_area_size = houzez_get_listing_area_size( $post_id );
                        if( !empty( $listing_area_size ) ) {
                            echo '<span>';
                            echo '<strong>'.houzez_get_listing_size_unit($post_id) . ': </strong> ' . houzez_get_listing_area_size($post_id);
                            echo '</span>';
                        }
                        ?>
                        <span><?php echo houzez_taxonomy_simple('property_type'); ?></span>
                    </p>
                    <?php if( houzez_user_role_by_post_id($post_id) != 'administrator' && get_post_status ( $post_id ) == 'publish' ) { ?>
                        <p class="expiration_date"><strong><?php echo esc_html__('Expiration:', 'houzez'); ?></strong> <?php houzez_listing_expire(); ?></p>
                    <?php } ?>
                </div>
            </div>
            <div class="my-actions">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php esc_html_e('Actions', 'houzez');?> <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu actions-dropdown">
                        <li><a href="<?php echo esc_url($edit_link); ?>"><i class="fa fa-edit"></i> <?php esc_html_e('Edit', 'houzez');?></a></li>
                        <li><a class="delete-property" data-id="<?php echo $post->ID; ?>" data-nonce="<?php echo wp_create_nonce('delete_my_property_nonce') ?>" onclicks="return confirm('<?php esc_html_e( 'Are you sure you want to delete?', 'houzez' ); ?>')" href="#"><i class="fa fa-close"></i> <?php esc_html_e('Delete', 'houzez');?></a></li>

                        <li><a href="#" class="clone-property" data-property="<?php echo $post->ID; ?>"><i class="fa fa-edit"></i> <?php esc_html_e('Duplicate', 'houzez');?></a></li>

                        <?php if(houzez_is_published( $post->ID )) { ?>
                        <li><a href="#" class="put-on-hold" data-property="<?php echo $post->ID; ?>"><i class="fa fa-stop"></i> <?php esc_html_e('Put On Hold', 'houzez');?></a></li>
                        <?php } elseif (houzez_on_hold( $post->ID )) { ?>
                            <li><a href="#" class="put-on-hold" data-property="<?php echo $post->ID; ?>"><i class="fa fa-play"></i> <?php esc_html_e('Go Live', 'houzez');?></a></li>
                        <?php } ?>

                        <?php if( houzez_check_post_status( $post->ID ) ) { ?>

                            <?php if( !empty($add_floor_plans) ) { ?>
                                <li><a href="<?php echo $add_floor_plans_link; ?>"><i class="fa fa-book"></i> <?php esc_html_e( 'Floor Plans', 'houzez' ); ?></a></li>
                            <?php } ?>
                            <?php if( !empty($add_multiunits) ) { ?>
                                <li><a href="<?php echo $add_multiunits_link; ?>"><i class="fa fa-th-large"></i> <?php esc_html_e( 'Multi Units / Sub Properties', 'houzez' ); ?></a></li>
                            <?php } ?>

                        <?php } ?>

                    </ul>
                </div>
                <?php
                if( $paid_submission_type == 'per_listing' && $property_status_text != 'expired' ) {
                    echo '<div class="btn-group">';
                    if ($payment_status != 'paid') {
                        echo '<a href="' . esc_url($payment_page_link) . '" class="btn pay-btn">' . esc_html__('Pay Now', 'houzez') . '</a>';
                    } else {
                        if( $prop_featured != 1 && $property_status_text == 'publish' ) {
                            echo '<a href="' . esc_url($payment_page_link_featured) . '" class="btn pay-btn">' . esc_html__('Upgrade to Featured', 'houzez') . '</a>';
                        }
                    }
                    echo '</div>';
                }

                if( $property_status_text == 'expired' && ( $paid_submission_type == 'per_listing') ) {
                    echo '<div class="btn-group">';
                        echo '<a href="' . esc_url($payment_page_link) . '" class="btn pay-btn">'.esc_html__( 'Re-List', 'houzez' ).'</a>';
                    echo '</div>';
                }

                if( $property_status_text == 'expired' && ( $paid_submission_type == 'free_paid_listing' || $paid_submission_type == 'no' ) ) {
                    echo '<div class="btn-group">';
                        echo '<a href="#" data-property="'.$post->ID.'" class="relist-free btn pay-btn">'.esc_html__( 'Re-List', 'houzez' ).'</a>';
                    echo '</div>';
                }

                if( houzez_check_post_status( $post->ID ) ) {

                    if (isset($_GET['prop_status']) && $_GET['prop_status'] == 'package') {
                        if ( $paid_submission_type == 'membership' ) {
                            echo '<div class="btn-group">';
                            echo '<a href="'.esc_url($dashboard_package).'?option=featured&post='.intval( $post->ID ).'" class="btn pay-btn">' . esc_html__('Set as Featured', 'houzez') . '</a>';
                            echo '</div>';
                        }

                        if ( $paid_submission_type == 'membership' ) {
                            echo '<div class="btn-group">';
                            echo '<a href="'.esc_url($dashboard_package).'?option=week&post='.intval( $post->ID ).'" class="btn btn-primary">' . esc_html__('Property of the week', 'houzez') . '</a>';
                            echo '</div>';
                        }
                        
                    }

                    if ( $paid_submission_type == 'membership' && $prop_featured == 1 ) {
                        echo '<div class="btn-group">';
                        echo '<a href="#" data-proptype="membership" data-propid="'.intval( $post->ID ).'" class="remove-prop-featured btn pay-btn">' . esc_html__('Remove From Featured', 'houzez') . '</a>';
                        echo '</div>';
                    }

                    if ( $paid_submission_type == 'membership' && $prop_week == 1 ) {
                        echo '<div class="btn-group">';
                        echo '<a href="#" data-propid="'. intval( $post->ID ) .'" class="remove-prop-week btn btn-primary">' . esc_html__('Remove From Week', 'houzez') . '</a>';
                        echo '</div>';
                    }

                    if( $property_status_text == 'expired' && $paid_submission_type == 'membership' ) {
                        echo '<div class="btn-group">';
                            echo '<a href="#" data-propid="'.intval( $post->ID ).'" class="resend-for-approval btn pay-btn">' . esc_html__('Reactivate Listing', 'houzez') . '</a>';
                        echo '</div>';
                    }

                    //Paid Featured
                    if( $paid_submission_type == 'free_paid_listing' && $property_status_text == 'publish' ) {
                        echo '<div class="btn-group">';
                        if( $prop_featured != 1 ) {
                            echo '<a href="' . esc_url($payment_page_link_featured) . '" class="btn pay-btn">' . esc_html__('Upgrade to Featured', 'houzez') . '</a>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

