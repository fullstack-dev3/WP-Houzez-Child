<?php

global $post_meta_data;

$prop_id = get_post_meta( get_the_ID(), 'fave_property_id', true );
$prop_price = get_post_meta( get_the_ID(), 'fave_property_price', true );
$prop_size = get_post_meta( get_the_ID(), 'fave_property_size', true );
$land_area = get_post_meta( get_the_ID(), 'fave_property_land', true );
$bedrooms = get_post_meta( get_the_ID(), 'fave_property_bedrooms', true );
$bathrooms = get_post_meta( get_the_ID(), 'fave_property_bathrooms', true );
$year_built = get_post_meta( get_the_ID(), 'fave_property_year', true );
$garage = get_post_meta( get_the_ID(), 'fave_property_garage', true );
$property_status = houzez_taxonomy_simple('property_status');
$property_type = houzez_taxonomy_simple('property_type');
$garage_size = get_post_meta( get_the_ID(), 'fave_property_garage_size', true );
$additional_features_enable = get_post_meta( get_the_ID(), 'fave_additional_features_enable', true );
$additional_features = get_post_meta( get_the_ID(), 'additional_features', true );
$prop_details = false;

if( !empty( $prop_id ) ||
    !empty( $prop_price ) ||
    !empty( $prop_size ) ||
    !empty( $land_area ) ||
    !empty( $bedrooms ) ||
    !empty( $bathrooms ) ||
    !empty( $year_built ) ||
    !empty( $property_status ) ||
    !empty( $property_type ) ||
    !empty( $garage )
) {
    $prop_details = true;
}

$hide_detail_prop_fields = houzez_option('hide_detail_prop_fields');

if( $prop_details ) {
?>
<div id="detail" class="detail-list detail-block target-block property-details">
    <div class="detail-title">
        <h2 class="title-left"><?php esc_html_e( 'Detail', 'houzez' ); ?></h2>
    </div>
    <div class="detail-content">
        <ul class="list-three-col">
            <li>
                <strong><?php echo esc_html__('Property ID:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $prop_id ) && $hide_detail_prop_fields['prop_id'] != 1)
                        echo houzez_propperty_id_prefix($prop_id);
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Price:', 'houzez') ?></strong>
                <?php
                    if (!empty( $prop_price ) && $hide_detail_prop_fields['sale_rent_price'] != 1)
                        echo houzez_listing_price();
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Property Size:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $prop_size ) && $hide_detail_prop_fields['area_size'] != 1)
                        echo houzez_property_size('after');
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Bedrooms:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $bedrooms ) && $hide_detail_prop_fields['bedrooms'] != 1)
                        echo esc_attr($bedrooms);
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Bathrooms:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $bathrooms ) && $hide_detail_prop_fields['bathrooms'] != 1)
                        echo esc_attr($bathrooms);
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Garage:', 'houzez') ?></strong>
                <?php
                    if (!empty( $garage ) && $hide_detail_prop_fields['garages'] != 1)
                        echo esc_attr($garage);
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Garage Size:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $garage_size ) && $hide_detail_prop_fields['garages'] != 1)
                        echo esc_attr( $garage_size );
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Land Area:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $land_area ) && $hide_detail_prop_fields['land_area'] != 1)
                        echo houzez_property_land_area('after');
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Year Built:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $year_built ) && $hide_detail_prop_fields['year_built'] != 1)
                        echo esc_attr($year_built);
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Property Type:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $property_type ) && ($hide_detail_prop_fields['prop_type']) != 1)
                        echo esc_attr($property_type);
                ?>
            </li>
            <li>
                <strong><?php echo esc_html__('Property Status:', 'houzez'); ?></strong>
                <?php
                    if (!empty( $property_status ) && ($hide_detail_prop_fields['prop_status']) != 1 )
                        echo esc_attr($property_status);
                ?>
            </li>
            <?php
            if(class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields(); 

                if(!empty($fields_array)) {
                    foreach ( $fields_array as $value ) {
                        $data_value = get_post_meta( get_the_ID(), 'fave_'.$value->field_id, true );
                        $field_title = $value->label;
                        if (function_exists('icl_translate') ){
                            $field_title = icl_translate('houzez_cfield', 'houzez_custom_field_'.sanitize_title($field_title), $field_title );
                                              
                        }
                        if(!empty($data_value) && $hide_detail_prop_fields[$value->field_id] != 1) {
                            echo '<li class="'.$value->field_id.'"><strong>'.$field_title.':</strong> '.esc_attr( $data_value ).'</li>';
                        }
                    }
                }
            }

            ?>
        </ul>
    </div>

    <?php if( $additional_features_enable != 'disable' && !empty( $additional_features[0]['fave_additional_feature_title'] ) && $hide_detail_prop_fields['additional_details'] != 1 ) { ?>
        <div class="detail-title-inner">
            <h4 class="title-inner"><?php esc_html_e( 'Additional details', 'houzez' ); ?></h4>
        </div>
        <ul class="list-three-col">
            <?php
            foreach( $additional_features as $ad_del ):
                echo '<li><strong>'.esc_attr( $ad_del['fave_additional_feature_title'] ).':</strong> '.esc_attr( $ad_del['fave_additional_feature_value'] ).'</li>';
            endforeach;
            ?>
        </ul>
    <?php } ?>
</div>
<?php } ?>