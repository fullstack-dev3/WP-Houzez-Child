<?php

global $floor_plans;
?>

<?php if( !empty( $floor_plans ) ) { ?>
<div id="floor_plan" class="property-plans detail-block target-block">
    <div class="detail-title">
        <h2 class="title-left"><?php esc_html_e( 'Floor plans', 'houzez' ); ?></h2>
    </div>
    <div class="block">
        <ul class="nav nav-tabs">
            <?php for( $i = 0; $i < sizeof($floor_plans); $i++) { ?>
            <li <?php if ($i == 0) { ?>class="active"<?php } ?>>
                <a  href="#<?php echo $i; ?>" data-toggle="tab">
                    <?php echo esc_attr( $floor_plans[$i]['fave_plan_title'] ); ?>
                </a>
            </li>
            <?php } ?>
        </ul>

        <div class="tab-content row">
            <?php for( $i = 0; $i < sizeof($floor_plans); $i++) {
                $price_postfix = '';

                if( !empty( $floor_plans[$i]['fave_plan_price_postfix'] ) ) {
                    $price_postfix = ' / '.$floor_plans[$i]['fave_plan_price_postfix'];
                }

                $filetype = wp_check_filetype($floor_plans[$i]['fave_plan_image']);
            ?>
            <div class="tab-pane col-md-12 <?php if ($i == 0) { ?>active<?php } ?>" id="<?php echo $i; ?>">
                <?php if( $filetype['ext'] && $filetype['ext'] != 'pdf' ) {?>
                <div class="col-md-6">
                    <a href="<?php echo esc_url( $floor_plans[$i]['fave_plan_image'] ); ?>" 
                        data-fancy="property_gallery">
                        <img src="<?php echo esc_url( $floor_plans[$i]['fave_plan_image'] ); ?>" 
                            alt="Floor Plan" width="400" height="436">
                    </a>
                </div>
                <div class="col-md-6">
                <?php } else { ?>
                <div class="col-md-12">
                <?php } ?>
                    <h2><?php echo esc_attr( $floor_plans[$i]['fave_plan_title'] ); ?></h2>

                    <?php if( !empty( $floor_plans[$i]['fave_plan_description'] ) ) { ?>
                        <p><?php echo esc_attr( $floor_plans[$i]['fave_plan_description'] ); ?></p>
                    <?php } ?>

                    <ul class="items">
                        <?php if( !empty( $floor_plans[$i]['fave_plan_rooms'] ) ) { ?>
                        <li>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/rooms.png">
                            <span>
                                <?php esc_html_e( 'Rooms :', 'houzez' ); ?>
                                <br />
                                <?php echo $floor_plans[$i]['fave_plan_rooms']; ?>
                            </span>
                        </li>
                        <?php } ?>

                        <?php if( !empty( $floor_plans[$i]['fave_plan_bathrooms'] ) ) { ?>
                        <li>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/bathtub.png">
                            <span>
                                <?php esc_html_e( 'Baths :', 'houzez' ); ?>
                                <br />
                                <?php echo $floor_plans[$i]['fave_plan_bathrooms']; ?>
                            </span>
                        </li>
                        <?php } ?>

                        <?php if( !empty( $floor_plans[$i]['fave_plan_size'] ) ) { ?>
                        <li>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/house.png">
                            <span>
                                <?php esc_html_e( 'Size :', 'houzez' ); ?>
                                <br />
                                <?php echo $floor_plans[$i]['fave_plan_size']; ?>
                            </span>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

</div>
<?php } ?>