<?php

global $houzez_local, $prop_data, $prop_meta_data;
global $hide_add_prop_fields, $required_fields, $is_multi_steps;
$is_multi_currency = houzez_option('multi_currency');
$default_multi_currency = houzez_option('default_multi_currency');
?>
<div class="account-block <?php echo esc_attr($is_multi_steps);?>">
    <div class="add-title-tab">
        <h3><?php echo $houzez_local['prop_des_price']; ?></h3>
        <div class="add-expand"></div>
    </div>
    <div class="add-tab-content">
        <div class="add-tab-row push-padding-bottom">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="prop_title"><?php echo $houzez_local['prop_title'].houzez_required_field( $required_fields['title'] ); ?> </label>
                        <input type="text" id="prop_title" class="form-control" value="<?php print sanitize_text_field( $prop_data->post_title ); ?>" name="prop_title" placeholder="<?php echo $houzez_local['prop_title_placeholder']; ?>"/>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="description"><?php echo $houzez_local['prop_des']; ?></label>
                        <?php 
                        // default settings - Kv_front_editor.php
                        $content = $prop_data->post_content;
                        $editor_id = 'prop_des';
                        $settings =   array(
                            'wpautop' => true, // use wpautop?
                            'media_buttons' => false, // show insert/upload button(s)
                            'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
                            'textarea_rows' => get_option('default_post_edit_rows', 18), // rows="..."
                            'tabindex' => '',
                            'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
                            'editor_class' => '', // add extra class(es) to the editor textarea
                            'teeny' => false, // output the minimal editor config used in Press This
                            'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
                            'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                            'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                        );
                        wp_editor( $content, $editor_id, $settings ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="add-tab-row push-padding-bottom">
            <div class="row">

                <?php if( $hide_add_prop_fields['prop_type'] != 1 ) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="prop_type"><?php echo $houzez_local['prop_type'].houzez_required_field( $required_fields['prop_type'] ); ?></label>
                        <select name="prop_type" id="prop_type" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                            <?php houzez_get_taxonomies_for_edit_listing( $prop_data->ID, 'property_type'); ?>
                        </select>
                    </div>
                </div>
                <?php } ?>

                <?php if( $hide_add_prop_fields['prop_status'] != 1 ) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="prop_status"><?php echo $houzez_local['prop_status'].houzez_required_field( $required_fields['prop_status'] ); ?></label>
                        <select name="prop_status" id="prop_status" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                            <?php houzez_get_taxonomies_for_edit_listing( $prop_data->ID, 'property_status'); ?>
                        </select>
                    </div>
                </div>
                <?php } ?>

                <?php if( $hide_add_prop_fields['prop_lifestyle'] != 1 ) { ?>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="prop_lifestyles">Lifestyle</label>
                            <select name="prop_lifestyles" id="prop_lifestyles" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                                <?php houzez_get_taxonomies_for_edit_listing( $prop_data->ID, 'property_lifestyle'); ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
        <div class="add-tab-row push-padding-bottom">
            <div class="row">

                <?php if( $is_multi_currency == 1 && class_exists('Houzez_Currencies')) { ?>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="prop_price_prefix"><?php echo $houzez_local['currency_label']; ?></label>
                            <select name="currency" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                                <option value=""><?php echo $houzez_local['choose_currency']; ?></option>
                                <?php
                                $currency_val = '';
                                if( isset( $prop_meta_data['fave_currency'] ) ) { 
                                    $currency_val = sanitize_text_field( $prop_meta_data['fave_currency'][0] ); 
                                }
                                $currencies = Houzez_Currencies::get_form_fields();
                                foreach ($currencies as $currency) { ?>

                                    <option <?php selected($currency->currency_code, $currency_val); ?> value="<?php esc_attr_e($currency->currency_code); ?>"><?php esc_attr_e($currency->currency_code); ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                
                <?php if( $hide_add_prop_fields['sale_rent_price'] != 1 ) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="prop_price"> <?php echo $houzez_local['prop_sale_rent_price'].houzez_required_field( $required_fields['sale_rent_price'] );
                            print esc_html(get_option('houzez_currency_symbol', '')) . ' ';?>  </label>
                        <input type="text" id="prop_price" class="form-control" name="prop_price" value="<?php if( isset( $prop_meta_data['fave_property_price'] ) ) { echo sanitize_text_field( $prop_meta_data['fave_property_price'][0] ); } ?>" placeholder="<?php echo $houzez_local['prop_sale_rent_price_placeholder']; ?>">
                    </div>
                </div>
                <?php } ?>

                <?php if( $hide_add_prop_fields['second_price'] != 1 ) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="prop_sec_price"><?php echo $houzez_local['prop_second_price'].houzez_required_field( $required_fields['prop_second_price']); ?></label>
                        <input type="text" id="prop_sec_price" class="form-control" value="<?php if( isset( $prop_meta_data['fave_property_sec_price'] ) ) { echo sanitize_text_field( $prop_meta_data['fave_property_sec_price'][0] ); } ?>" name="prop_sec_price" placeholder="<?php echo $houzez_local['prop_second_price_placeholder']; ?>">
                    </div>
                </div>
                <?php } ?>

                <?php if( $hide_add_prop_fields['price_postfix'] != 1 ) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="prop_label"><?php echo $houzez_local['prop_price_label'].houzez_required_field( $required_fields['price_label'] ); ?></label>
                        <input type="text" id="prop_label" class="form-control" name="prop_label" value="<?php if( isset( $prop_meta_data['fave_property_price_postfix'] ) ) { echo sanitize_text_field( $prop_meta_data['fave_property_price_postfix'][0] ); } ?>" placeholder="<?php echo $houzez_local['prop_price_label_placeholder']; ?>" >
                    </div>
                </div>
                <?php } ?>

                <?php if( $hide_add_prop_fields['price_prefix'] != 1 ) { ?>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="prop_price_prefix"><?php echo $houzez_local['prop_price_prefix']; ?></label>
                            <input type="text" id="prop_price_prefix" class="form-control" name="prop_price_prefix" value="<?php if( isset( $prop_meta_data['fave_property_price_prefix'] ) ) { echo sanitize_text_field( $prop_meta_data['fave_property_price_prefix'][0] ); } ?>" placeholder="<?php echo $houzez_local['prop_price_prefix_placeholder']; ?>" >
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
