<?php

$google_map_address = get_post_meta( get_the_ID(), 'fave_property_map_address', true );

$property_location    = get_post_meta( get_the_ID(), 'fave_property_location',true);
$fave_perspective    = get_post_meta( get_the_ID(), 'fave_perspective',true);

?>

<div id="solar" class="property-solar detail-block target-block">
	<div class="detail-title">
        <h2 class="title-left"><?php esc_html_e( 'Solar Perspective', 'houzez' ); ?></h2>
        <input type="hidden" class="map-location" value="<?php echo $property_location; ?>" />
        <input type="hidden" class="map-perspective" value="<?php echo $fave_perspective; ?>" />
        <input type="hidden" class="solar-dir" value="<?php echo get_stylesheet_directory_uri(); ?>/solar/" />
    </div>
    <div class="block row">
    	<div class="col-md-6">
    		<h3 class="text-center">12:00 PM</h3>
            <div class="text-center">
                <img class="solar12" />
            </div>
    	</div>
    	<div class="col-md-6">
    		<h3 class="text-center">16:00 PM</h3>
            <div class="text-center">
                <img class="solar16" />
            </div>
    	</div>
    </div>
</div>