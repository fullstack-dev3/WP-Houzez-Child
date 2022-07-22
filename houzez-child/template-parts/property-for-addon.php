<?php

global $post, $prop_images, $houzez_local;
$post_meta_data     = get_post_custom($post->ID);
$prop_images        = get_post_meta( get_the_ID(), 'fave_property_images', false );
$prop_address       = get_post_meta( get_the_ID(), 'fave_property_map_address', true );
$prop_featured      = get_post_meta( get_the_ID(), 'fave_featured', true );
$prop_week          = get_post_meta( get_the_ID(), 'fave_week', true );
$listing_agent = houzez_get_property_agent( $post->ID );
$disable_agent = houzez_option('disable_agent');
$disable_date = houzez_option('disable_date');

$disable_favorite = houzez_option('disable_favorite');
$disable_photo_count = houzez_option('disable_photo_count');

?>

<div id="ID-<?php the_ID(); ?>" class="item-wrap infobox_trigger prop_addon">
    <div class="property-item row">
        <div class="figure-block">
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
                        the_post_thumbnail( 'houzez-prop_image1440_610' );
                    }else{
                        houzez_image_placeholder( 'houzez-prop_image1440_610' );
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
        <div class="item-body">
            <div class="item-title">
                <?php
                    echo '<h2 class="property-title">'. esc_attr( get_the_title() ). '</h2>';
                ?>
            </div>

            <div class="item-detail">
                <?php if( $prop_week == 1 ) { ?>
                    <p><?php echo wp_trim_words( get_the_content(), 50 ); ?></p>
                <?php } else { ?>
                    <p><?php echo wp_trim_words( get_the_content(), 15 ); ?></p>
                <?php } ?>
            </div>

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
            </ul>

            <div class="item-price-block">
                <span class="item-price">
                    <?php echo houzez_listing_price_v1(); ?>
                </span>
            </div>
            <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn btn-primary">View</a>
        </div>
    </div>
</div>