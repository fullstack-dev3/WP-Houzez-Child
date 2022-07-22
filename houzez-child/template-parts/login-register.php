<?php

$terms_conditions = houzez_option('login_terms_condition');
$facebook_login = houzez_option('facebook_login');
$yahoo_login = houzez_option('yahoo_login');
$google_login = houzez_option('google_login');
$enable_password = houzez_option('enable_password');
$user_show_roles = houzez_option('user_show_roles');
$show_hide_roles = houzez_option('show_hide_roles');

$allowed_html_array = array(
    'a' => array(
        'href' => array(),
        'title' => array()
    )
);
?>
<div class="tab-content">
    <div class="tab-pane fade in active">
        <div id="houzez_messages" class="houzez_messages message"></div>
        <form>
            <div class="form-group field-group">
                <div class="input-user input-icon">
                    <input id="login_username" name="username" placeholder="<?php esc_html_e('Username or Email','houzez'); ?>" type="text" />
                </div>
                <div class="input-pass input-icon">
                    <input id="password" name="password" placeholder="<?php esc_html_e('Password','houzez'); ?>" type="password" />
                </div>
            </div>

            <?php get_template_part('template-parts/google', 'reCaptcha'); ?>

            <div class="forget-block clearfix">
                <div class="form-group pull-left">
                    <div class="checkbox">
                        <label>
                            <input name="remember" id="remember" type="checkbox">
                            <?php esc_html_e( 'Remember me', 'houzez' ); ?>
                        </label>
                    </div>
                </div>
                <div class="form-group pull-right">
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#pop-reset-pass"><?php esc_html_e( 'Lost your password?', 'houzez' ); ?></a>
                </div>
            </div>

            <?php wp_nonce_field( 'houzez_login_nonce', 'houzez_login_security' ); ?>
            <input type="hidden" name="action" id="login_action" value="houzez_login">
            <button type="submit" class="fave-login-button btn btn-primary btn-block"><?php esc_html_e('Login','houzez');?></button>
        </form>
        <?php if( $facebook_login != 'no' || $google_login != 'no' || $yahoo_login != 'no' ) { ?>
            <hr>
            <?php if( $facebook_login != 'no' ) { ?>
                <button class="facebook-login btn btn-social btn-bg-facebook btn-block"><i class="fa fa-facebook"></i> <?php esc_html_e( 'login with facebook', 'houzez' ); ?></button>
            <?php } ?>
            <?php if( $yahoo_login != 'no' ) { ?>
                <button class="yahoo-login btn btn-social btn-bg-yahoo btn-block"><i class="fa fa-yahoo"></i> <?php esc_html_e( 'login with yahoo', 'houzez' ); ?></button>
            <?php } ?>
            <?php if( $google_login != 'no' ) { ?>
                <button class="google-login btn btn-social btn-bg-google-plus btn-block"><i class="fa fa-google-plus"></i> <?php esc_html_e( 'login with google', 'houzez' ); ?></button>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="tab-pane fade">
        <?php if( get_option('users_can_register') ) { ?>
        <div id="houzez_messages_register" class="houzez_messages_register message"></div>
        <form>
            <div class="form-group field-group">
                <div class="input-user input-icon">
                    <input id="register_username" name="username" type="text" placeholder="<?php esc_html_e('Username','houzez'); ?>" />
                </div>
                <div class="input-email input-icon">
                    <input id="useremail" name="useremail" type="email" placeholder="<?php esc_html_e('Email','houzez'); ?>" />
                </div>

                <?php if( $enable_password == 'yes' ) { ?>
                    <div class="input-pass input-icon">
                        <input id="register_pass" name="register_pass" placeholder="<?php esc_html_e('Password','houzez'); ?>" type="password" />
                    </div>
                    <div class="input-pass input-icon">
                        <input id="register_pass_retype" name="register_pass_retype" placeholder="<?php esc_html_e('Retype Password','houzez'); ?>" type="password" />
                    </div>
                <?php } ?>
            </div>
            <div class="form-group">
                <?php if ( $user_show_roles != 0 ) : ?>
                    <select required="required" name="role" class="selectpicker" data-live-search="false" data-live-search-style="begins">
                        <option value=""> <?php esc_html_e('Select Type', 'houzez'); ?> </option>
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
                        if( $show_hide_roles['buyer'] != 1 ) {
                            echo '<option value="houzez_buyer"> ' . houzez_option('buyer_role') . '  </option>';
                        }
                        if( $show_hide_roles['seller'] != 1 ) {
                            echo '<option value="houzez_seller"> ' . houzez_option('seller_role') . '  </option>';
                        }
                        if( $show_hide_roles['manager'] != 1 ) {
                            echo '<option value="houzez_manager"> ' . houzez_option('manager_role') . ' </option>';
                        }
                        ?>
                    </select>
                <?php endif; ?>
            </div>
            
            <?php get_template_part('template-parts/google', 'reCaptcha'); ?>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input name="term_condition" id="term_condition" type="checkbox">
                        <?php echo sprintf( wp_kses(__( 'I agree with your <a href="%s">Terms & Conditions</a>', 'houzez' ), $allowed_html_array), get_permalink($terms_conditions) ); ?>
                    </label>
                </div>
            </div>
            <?php wp_nonce_field( 'houzez_register_nonce', 'houzez_register_security' ); ?>
            <input type="hidden" name="action" value="houzez_register_redirect" id="register_action">
            <button type="submit" class="fave-register-button btn btn-primary btn-block"><?php esc_html_e('Register','houzez');?></button>
        </form>
        <?php } else {
            esc_html_e('User registration is disabled in this website.', 'houzez');
        } ?>
    </div>

</div>
