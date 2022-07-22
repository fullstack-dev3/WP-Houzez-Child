<?php

global $current_user, $hide_add_prop_fields, $required_fields, $is_multi_steps;

wp_get_current_user();
$userID = $current_user->ID;
$user_show_roles = houzez_option('user_show_roles');
$show_hide_roles = houzez_option('show_hide_roles');
$enable_paid_submission = houzez_option('enable_paid_submission');
$remaining_listings = houzez_get_remaining_listings( $userID );
$select_packages_link = houzez_get_template_link('template-advanced-package.php');
$show_submit_btn = houzez_option('submit_form_type');
$allowed_html = array(
    'i' => array(
        'class' => array()
    ),
    'strong' => array(),
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array()
    )
);

if( is_page_template( 'template/submit_property.php' ) ) {

    if ($enable_paid_submission == 'membership' && $remaining_listings != -1 && $remaining_listings < 1 && is_user_logged_in() ) {
        if (!houzez_user_has_membership($userID)) {
            print '<div class="user_package_status">
                    <h4>' . esc_html__('You don\'t have any package! You need to buy your package.', 'houzez') . '</h4>
                    <a class="btn btn-primary" href="' . $select_packages_link . '">' . esc_html__('Get Package', 'houzez') . '</a>
                    </div>';
        } else {
            print '<div class="user_package_status"><h4>' . esc_html__('Your current package doesn\'t let you publish more properties! You need to upgrade your membership.', 'houzez') . '</h4>
            <a class="btn btn-primary" href="' . $select_packages_link . '">' . esc_html__('Upgrade Package', 'houzez') . '</a>
            </div>';
        }
    } else { ?>

        <form autocomplete="off" id="submit_property_form" name="new_post" method="post" action="#" enctype="multipart/form-data" class="add-frontend-property">

            <div class="validate-errors alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo wp_kses(__( '<strong>Error!</strong> Please fill out the following required fields.', 'houzez' ), $allowed_html); ?>
            </div>
            <div class="validate-errors-gal alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo wp_kses(__( '<strong>Error!</strong> Upload at least one image.', 'houzez' ), $allowed_html); ?>
            </div>

            <div class="submit-form-wrap">
            <?php
            $layout = houzez_option('property_form_sections');
            $layout = $layout['enabled'];

            if ($layout): foreach ($layout as $key=>$value) {

                switch($key) {

                    case 'description-price':
                        get_template_part('template-parts/submit-property/description-price');
                        break;

                    case 'media':
                        get_template_part('template-parts/submit-property/media');
                        break;

                    case 'details':
                        get_template_part('template-parts/submit-property/details');
                        break;

                    case 'energy_class':
                        get_template_part('template-parts/submit-property/energy-class');
                        break;

                    case 'features':
                        get_template_part('template-parts/submit-property/features');
                        break;

                    case 'location':
                        get_template_part('template-parts/submit-property/location');
                        break;

                    case 'virtual_tour':
                        get_template_part('template-parts/submit-property/virtual-tour');
                        break;

                    case 'floorplans':
                        get_template_part('template-parts/submit-property/floorplans');
                        break;

                    case 'multi-units':
                        get_template_part('template-parts/submit-property/multi-units');
                        break;

                    case 'agent_info':
                        if(houzez_show_agent_box()) {
                            get_template_part('template-parts/submit-property/agent-info');
                        }
                        break;

                    case 'private_note':
                        get_template_part('template-parts/submit-property/private-note');
                        break;

                    case 'attachments':
                        get_template_part('template-parts/submit-property/attachments');
                        break;
                }

            }
            endif;
            ?>

            <?php if ( !is_user_logged_in() ) { ?>
                <div class="account-block class-for-register-msg form-step">
                    <div class="add-title-tab">
                        <h3><?php esc_html_e( 'Do you have an account?', 'houzez' ); ?></h3>
                        <div class="add-expand"></div>
                    </div>
                    <div class="add-tab-content">
                        <div class="add-tab-row push-padding-bottom">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p><?php _e( "If you don't have an account you can create one below by entering your email address. Your account details will be confirmed via email. Otherwise you can ", 'houzez' ); ?></p>
                                    <div class="form-group step-login-btn">
                                        <a href="#" class="login-here"><?php esc_html_e('Login here', 'houzez');?></a>
                                        <a href="#" class="register-here" style="display: none"><?php esc_html_e('Register here', 'houzez');?></a>
                                    </div>
                                    <div class="form-group">
                                        <div class="houzez_messages_register message"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="tab-content">
                                        <div class="tab-pane fade in active step-tab-register">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="username"><?php esc_html_e('Username', 'houzez'); ?>* </label>
                                                        <input type="text" id="username" name="username" class="form-control" placeholder="<?php esc_html_e( 'Enter your username', 'houzez' ); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="user_email"><?php esc_html_e('Email Address', 'houzez'); ?>* </label>
                                                        <input type="email" id="user_email" class="form-control" name="user_email" placeholder="<?php esc_html_e( 'Enter your email address', 'houzez' ); ?>">
                                                    </div>
                                                </div>
                                                <?php if ( $user_show_roles != 0 ) : ?>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="user_email"><?php esc_html_e('Select Role', 'houzez'); ?>* </label>
                                                        <select name="user_role" id="user_role" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                                                            <?php
                                                            if( $show_hide_roles['agent'] != 1 ) {
                                                                echo '<option value="houzez_agent"> '.houzez_option('agent_role').' </option>';
                                                            }
                                                            if( $show_hide_roles['agency'] != 1 ) {
                                                                echo '<option value="houzez_agency"> ' . houzez_option('agency_role') . ' </option>';
                                                            }
                                                            if( $show_hide_roles['owner'] != 1 ) {
                                                                echo '<option value="houzez_owner"> ' . houzez_option('owner_role') . '  </option>';
                                                            }
                                                            if( $show_hide_roles['seller'] != 1 ) {
                                                                echo '<option value="houzez_seller"> ' . houzez_option('seller_role') . '  </option>';
                                                            }
                                                            if( $show_hide_roles['manager'] != 1 ) {
                                                                echo '<option value="houzez_manager"> ' . houzez_option('manager_role') . ' </option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade step-tab-login">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="sp_username"><?php esc_html_e('Username', 'houzez'); ?>* </label>
                                                        <input type="text" id="sp_username" name="sp_username" class="form-control" placeholder="<?php esc_html_e( 'Enter Your Username', 'houzez' ); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="sp_password"><?php esc_html_e('Password', 'houzez'); ?>* </label>
                                                        <input type="password" id="sp_password" class="form-control" name="sp_password" placeholder="<?php esc_html_e( 'Enter Your Password', 'houzez' ); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <?php wp_nonce_field( 'houzez_register_nonce2', 'houzez_register_security2' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if($show_submit_btn == 'one_step') { ?>
            <div class="account-block text-right">
                <button id="add_new_property" type="submit" class="btn btn-primary"><?php esc_html_e('Submit Property', 'houzez'); ?></button>
            </div>
            <?php } ?>

            <?php wp_nonce_field('submit_property', 'property_nonce'); ?>

            <input type="hidden" name="action" value="add_property"/>
            <input type="hidden" name="prop_featured" value="0"/>
            <input type="hidden" name="prop_payment" value="not_paid"/>
            <?php if ( !is_user_logged_in() ) { ?>
                <input type="hidden" name="user_submit_has_no_membership" value="yes"/>
            <?php } ?>



            <div class="steps-nav">
                <div class="btn-left btn-back action">
                    <button type="button" class="btn"><i class="fa fa-angle-left"></i></button> <span><?php esc_html_e('Back', 'houzez'); ?></span>
                </div>
                <div class="btn-right btn-next action">
                    <span><?php esc_html_e('Next', 'houzez'); ?></span> <button type="button" class="btn"><i class="fa fa-angle-right"></i></button>
                </div>
                <div class="btn-right action btn-submit btn-step-submit">
                    <span><?php esc_html_e('Submit Property', 'houzez'); ?></span> <button id="add_new_property" type="submit" class="btn"><i class="fa fa-angle-right"></i></button>
                </div>
            </div>
            </div>
        </form>

        <?php
    }
}