<?php
add_action('admin_head', 'custom_styles');
function custom_styles() {
  echo '<style>
    .form-field input, .form-field textarea {
        width: auto !important;
    }
    .rwmb-button-wrapper .rwmb-input {
        text-align: center;
    }
    #fave_billing_unit_add,
    .payment_option {
        margin-top: 25px;
    }
    .payment {
        width: 50%;
    }
    .payment:not(.selected) {
        display: none;
    }
    .wp-core-ui .payment_option button.button {
        background: none !important;
        border: none;
        border-radius: 0;
        box-shadow: none;
        color: #ff0000;
        height: 25px;
        padding: 0 3px;
    }
    .wp-core-ui .payment_option button.button:hover {
        border-bottom: 1px solid #ff0000;
    }
  </style>';
}

add_action( 'wp_ajax_nopriv_houzez_register_redirect', 'houzez_register_redirect', 200 );
add_action( 'wp_ajax_houzez_register_redirect', 'houzez_register_redirect', 200 );

function houzez_register_redirect() {
    check_ajax_referer('houzez_register_nonce', 'houzez_register_security');

    $allowed_html = array();

    $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
    $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
    $term_condition    = wp_kses( $_POST['term_condition'], $allowed_html );
    $enable_password = houzez_option('enable_password');
    $response = $_POST["g-recaptcha-response"];

    $user_role = get_option( 'default_role' );

    if( isset( $_POST['role'] ) && $_POST['role'] != '' ){
        $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
    } else {
        $user_role = $user_role;
    }

    $term_condition = ( $term_condition == 'on') ? true : false;

    if( !$term_condition ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'houzez-login-register') ) );
        wp_die();
    }

    if( empty( $usermane ) ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'houzez-login-register') ) );
        wp_die();
    }
    if( strlen( $usermane ) < 3 ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'houzez-login-register') ) );
        wp_die();
    }
    if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez-login-register') ) );
        wp_die();
    }
    if( username_exists( $usermane ) ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('This username is already registered.', 'houzez-login-register') ) );
        wp_die();
    }
    if( empty( $email ) ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'houzez-login-register') ) );
        wp_die();
    }

    if( email_exists( $email ) ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez-login-register') ) );
        wp_die();
    }

    if( !is_email( $email ) ) {
        echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez-login-register') ) );
        wp_die();
    }

    if( $enable_password == 'yes' ){
        $user_pass         = trim( sanitize_text_field(wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
        $user_pass_retype  = trim( sanitize_text_field(wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

        if ($user_pass == '' || $user_pass_retype == '' ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('One of the password field is empty!', 'houzez-login-register') ) );
            wp_die();
        }

        if ($user_pass !== $user_pass_retype ){
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'houzez-login-register') ) );
            wp_die();
        }
    }

    houzez_google_recaptcha_callback();

    if($enable_password == 'yes' ) {
        $user_password = $user_pass;
    } else {
        $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
    }
    $user_id = wp_create_user( $usermane, $user_password, $email );

    if ( is_wp_error($user_id) ) {
        echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
        wp_die();
    } else {

        wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

        if( $enable_password =='yes' ) {
            echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now!', 'houzez-login-register') ) );
        } else {
            echo json_encode( array( 'success' => true, 'msg' => esc_html__('An email with the generated password was sent!', 'houzez-login-register') ) );
        }

        $user_as_agent = houzez_option('user_as_agent');

        if( $user_as_agent == 'yes' ) {
            if ($user_role == 'houzez_agent' || $user_role == 'author') {
                houzez_register_as_agent($usermane, $email, $user_id);

            } else if ($user_role == 'houzez_agency') {
                houzez_register_as_agency($usermane, $email, $user_id);
            }
        }
        houzez_wp_new_user_notification( $user_id, $user_password );

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
    }
    wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_scripts', 100 );

function my_scripts() {
    wp_dequeue_script( 'houzez_ajax_calls' );
    wp_deregister_script( 'houzez_ajax_calls' );

    global $paged, $post, $current_user;

    $property_lat = $property_map = $property_streetView = $is_singular_property = $login_redirect = '';
    $property_lng = $google_map_needed = $fave_main_menu_trans = $header_map_selected_city = $current_template = $markerPricePins = '';
    $advanced_search_rent_status = $advanced_search_price_range_rent_status = 'for-rent';

    wp_get_current_user();
    $userID = $current_user->ID;

    if (is_rtl()) {
        $houzez_rtl = "yes";
    } else {
        $houzez_rtl = "no";
    }

    $after_login_redirect = houzez_option('login_redirect');

    if ($after_login_redirect == 'same_page') {

        if (is_tax()) {
            $login_redirect = get_term_link(get_query_var('term'), get_query_var('taxonomy'));
        } else {
            if (is_home() || is_front_page()) {
                $login_redirect = site_url();
            } else {
                if (!is_404() && !is_search() && !is_author()) {
                    $login_redirect = get_permalink($post->ID);
                }
            }
        }

    } else {
        $login_redirect = houzez_option('login_redirect_link');
    }

    if (!is_404() && !is_search() && !is_tax() && !is_author()) {
        $fave_main_menu_trans = get_post_meta($post->ID, 'fave_main_menu_trans', true);
        $header_map_selected_city = get_post_meta($post->ID, 'fave_map_city', false);
        $current_template = get_page_template_slug($post->ID);
    }

    $simple_logo = houzez_option('custom_logo', '', 'url');

    if (is_singular('property')) {
        $property_location = get_post_meta(get_the_ID(), 'fave_property_location', true);
        if (!empty($property_location)) {
            $lat_lng = explode(',', $property_location);
            $property_lat = $lat_lng[0];
            $property_lng = $lat_lng[1];

            $property_map = get_post_meta(get_the_ID(), 'fave_property_map', true);
            $property_streetView = get_post_meta(get_the_ID(), 'fave_property_map_street_view', true);
        }
        $is_singular_property = 'yes';
    }

    if (taxonomy_exists('property_status')) {
        $term_exist = get_term_by('id', $advanced_search_rent_status_id, 'property_status');
        if ($term_exist) {
            $advanced_search_rent_status = get_term($advanced_search_rent_status_id, 'property_status');
            if (!is_wp_error($advanced_search_rent_status)) {
                $advanced_search_rent_status = $advanced_search_rent_status->slug;
            }
        }

        $term_exist_2 = get_term_by('id', $advanced_search_rent_status_id_price_range, 'property_status');
        if ($term_exist_2) {
            $advanced_search_price_range_rent_status = get_term($advanced_search_rent_status_id_price_range, 'property_status');
            if (!is_wp_error($advanced_search_price_range_rent_status)) {
                $advanced_search_price_range_rent_status = $advanced_search_price_range_rent_status->slug;
            }
        }
    }

    $currency_symbol = '';

    $advanced_search_widget_min_price = houzez_option('advanced_search_widget_min_price');
    if (empty($advanced_search_widget_min_price)) {
        $advanced_search_widget_min_price = '0';
    }
    $advanced_search_widget_max_price = houzez_option('advanced_search_widget_max_price');
    if (empty($advanced_search_widget_max_price)) {
        $advanced_search_widget_max_price = '2500000';
    }


    $advanced_search_min_price_range_for_rent = houzez_option('advanced_search_min_price_range_for_rent');
    if (empty($advanced_search_min_price_range_for_rent)) {
        $advanced_search_min_price_range_for_rent = '0';
    }
    $advanced_search_max_price_range_for_rent = houzez_option('advanced_search_max_price_range_for_rent');
    if (empty($advanced_search_max_price_range_for_rent)) {
        $advanced_search_max_price_range_for_rent = '6000';
    }
    
    $advanced_search_widget_min_area = houzez_option('advanced_search_widget_min_area');
    if (empty($advanced_search_widget_min_area)) {
        $advanced_search_widget_min_area = '0';
    }

    $advanced_search_widget_max_area = houzez_option('advanced_search_widget_max_area');
    if (empty($advanced_search_widget_max_area)) {
        $advanced_search_widget_max_area = '600';
    }

    $googlemap_zoom_level = houzez_option('googlemap_zoom_level');
    $googlemap_pin_cluster = houzez_option('googlemap_pin_cluster');
    $googlemap_zoom_cluster = houzez_option('googlemap_zoom_cluster');

    $map_cluster = houzez_option('map_cluster', '', 'url');
    if (!empty($map_cluster)) {
        $clusterIcon = $map_cluster;
    } else {
        $clusterIcon = get_template_directory_uri() . '/images/map/cluster-icon.png';
    }

    if (is_page_template('template/property-listings-map.php') || is_page_template('template/submit_property.php') || is_page_template('template/submit_property_without_login.php') || $header_type == 'property_map' || is_singular('property') || is_singular('houzez_agency') || $content_has_map_shortcode || $enable_radius_search != 0) {
        $google_map_needed = 'yes';
    }

    if (is_front_page()) {
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
    }

    $search_result_page = houzez_option('search_result_page');
    $search_keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';
    $search_feature = isset($_GET['feature']) ? ($_GET['feature']) : $meta_features;
    $search_country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
    $search_state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : $meta_states;
    $search_city = isset($_GET['location']) ? sanitize_text_field($_GET['location']) : $meta_locations;
    $search_area = isset($_GET['area']) ? sanitize_text_field($_GET['area']) : $meta_area;
    $search_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : $meta_status;
    $search_label = isset($_GET['label']) ? sanitize_text_field($_GET['label']) : $meta_labels;
    $search_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : $meta_types;
    $search_bedrooms = isset($_GET['bedrooms']) ? sanitize_text_field($_GET['bedrooms']) : '';
    $search_bathrooms = isset($_GET['bathrooms']) ? sanitize_text_field($_GET['bathrooms']) : '';
    $search_min_price = isset($_GET['min-price']) ? sanitize_text_field($_GET['min-price']) : $meta_min_price;
    $search_max_price = isset($_GET['max-price']) ? sanitize_text_field($_GET['max-price']) : $meta_max_price;
    $search_currency = isset($_GET['currency']) ? sanitize_text_field($_GET['currency']) : '';
    $search_min_area = isset($_GET['min-area']) ? sanitize_text_field($_GET['min-area']) : '';
    $search_max_area = isset($_GET['max-area']) ? sanitize_text_field($_GET['max-area']) : '';
    $search_property_id = isset($_GET['property_id']) ? sanitize_text_field($_GET['property_id']) : '';
    $search_publish_date = isset($_GET['publish_date']) ? sanitize_text_field($_GET['publish_date']) : '';

    $prop_no_halfmap = 10;
    if (is_page_template(array('template/property-listings-map.php'))) {
        $prop_no_halfmap = get_post_meta($post->ID, 'fave_prop_no_halfmap', true);
    }

    $search_location = isset($_GET['search_location']) ? esc_attr($_GET['search_location']) : false;
    $use_radius = 'on';
    $search_lat = isset($_GET['lat']) ? (float)$_GET['lat'] : false;
    $search_long = isset($_GET['lng']) ? (float)$_GET['lng'] : false;
    $search_radius = isset($_GET['radius']) ? (int)$_GET['radius'] : false;

    $sort_by = isset($_GET['sortby']) ? sanitize_text_field($_GET['sortby']) : $sort_halfmap;

    $measurement_unit_adv_search = houzez_option('measurement_unit_adv_search');
    if ($measurement_unit_adv_search == 'sqft') {
        $measurement_unit_adv_search = houzez_option('measurement_unit_sqft_text');
    } elseif ($measurement_unit_adv_search == 'sq_meter') {
        $measurement_unit_adv_search = houzez_option('measurement_unit_square_meter_text');
    }

    $thousands_separator = houzez_option('thousands_separator');

    $property_top_area = houzez_option('prop-top-area');
    if (isset($_GET['s_top'])) {
        $property_top_area = $_GET['s_top'];
    }

    $keyword_field = houzez_option('keyword_field');
    $keyword_autocomplete = houzez_option('keyword_autocomplete');

    $houzez_default_radius = houzez_option('houzez_default_radius');
    if (isset($_GET['radius'])) {
        $houzez_default_radius = $_GET['radius'];
    }

    $enable_radius_search = houzez_option('enable_radius_search');
    $enable_radius_search_halfmap = houzez_option('enable_radius_search_halfmap');

    $houzez_primary_color = houzez_option('houzez_primary_color');

    $geo_country_limit = houzez_option('geo_country_limit');
    $geocomplete_country = '';
    if ($geo_country_limit != 0) {
        $geocomplete_country = houzez_option('geocomplete_country');
    }

    $houzez_logged_in = 'yes';
    if (!is_user_logged_in()) {
        $houzez_logged_in = 'no';
    }

    $custom_fields_array = array();
    if(class_exists('Houzez_Fields_Builder')) {
        $fields_array = Houzez_Fields_Builder::get_form_fields();
        
        if(!empty($fields_array)){
            foreach ( $fields_array as $value ){
                $field_title = $value->label;
                $field = $value->field_id;
                if($value->is_search == 'yes') {
                    $custom_fields_array[$field] = isset($_GET[$field]) ? sanitize_text_field($_GET[$field]) : '';
                }
                
            }
        }
    }

    $markerPricePins = houzez_option('markerPricePins');
    if(isset($_GET['marker']) && $_GET['marker'] == 'pricePins') {
        $markerPricePins = 'yes';
    }

    $enable_reCaptcha = houzez_option('enable_reCaptcha');

    wp_enqueue_script('houzez_ajax_calls', get_stylesheet_directory_uri() . '/js/houzez_ajax_calls.js', array('jquery'));
    wp_localize_script('houzez_ajax_calls', 'HOUZEZ_ajaxcalls_vars',
        array(
            'admin_url' => get_admin_url(),
            'houzez_rtl' => $houzez_rtl,
            'redirect_type' => $after_login_redirect,
            'login_redirect' => $login_redirect,
            'login_loading' => esc_html__('Sending user info, please wait...', 'houzez'),
            'direct_pay_text' => esc_html__('Processing, Please wait...', 'houzez'),
            'user_id' => $userID,
            'transparent_menu' => $fave_main_menu_trans,
            'simple_logo' => $simple_logo,
            'property_lat' => $property_lat,
            'property_lng' => $property_lng,
            'property_map' => $property_map,
            'property_map_street' => $property_streetView,
            'is_singular_property' => $is_singular_property,
            'process_loader_refresh' => 'fa fa-spin fa-refresh',
            'process_loader_spinner' => 'fa fa-spin fa-spinner',
            'process_loader_circle' => 'fa fa-spin fa-circle-o-notch',
            'process_loader_cog' => 'fa fa-spin fa-cog',
            'success_icon' => 'fa fa-check',
            'set_as_featured' => esc_html__('Set as Featured', 'houzez'),
            'remove_featured' => esc_html__('Remove From Featured', 'houzez'),
            'prop_featured' => esc_html__('Featured', 'houzez'),
            'featured_listings_none' => esc_html__('You have used all the "Featured" listings in your package.', 'houzez'),
            'prop_sent_for_approval' => esc_html__('Sent for Approval', 'houzez'),
            'paypal_connecting' => esc_html__('Connecting to paypal, Please wait... ', 'houzez'),
            'mollie_connecting' => esc_html__('Connecting to mollie, Please wait... ', 'houzez'),
            'bitcoin_connecting' => esc_html__('Connecting to bitcoin, Please wait... ', 'houzez'),
            'confirm' => esc_html__('Are you sure you want to delete?', 'houzez'),
            'confirm_featured' => esc_html__('Are you sure you want to make this a featured listing?', 'houzez'),
            'confirm_featured_remove' => esc_html__('Are you sure you want to remove from featured listing?', 'houzez'),
            'confirm_relist' => esc_html__('Are you sure you want to relist this property?', 'houzez'),
            'delete_property' => esc_html__('Processing, please wait...', 'houzez'),
            'delete_confirmation' => esc_html__('Are you sure you want to delete?', 'houzez'),
            'not_found' => esc_html__("We didn't find any results", 'houzez'),
            'for_rent' => $advanced_search_rent_status,
            'for_rent_price_range' => $advanced_search_price_range_rent_status,
            'currency_symbol' => $currency_symbol,
            'advanced_search_widget_min_price' => $advanced_search_widget_min_price,
            'advanced_search_widget_max_price' => $advanced_search_widget_max_price,
            'advanced_search_min_price_range_for_rent' => $advanced_search_min_price_range_for_rent,
            'advanced_search_max_price_range_for_rent' => $advanced_search_max_price_range_for_rent,
            'advanced_search_widget_min_area' => $advanced_search_widget_min_area,
            'advanced_search_widget_max_area' => $advanced_search_widget_max_area,
            'advanced_search_price_slide' => houzez_option('adv_search_price_slider'),
            'fave_page_template' => basename(get_page_template()),
            'google_map_style' => houzez_option('googlemap_stype'),
            'googlemap_default_zoom' => $googlemap_zoom_level,
            'googlemap_pin_cluster' => $googlemap_pin_cluster,
            'googlemap_zoom_cluster' => $googlemap_zoom_cluster,
            'map_icons_path' => get_template_directory_uri() . '/images/map/',
            'infoboxClose' => get_template_directory_uri() . '/images/map/close.png',
            'clusterIcon' => $clusterIcon,
            'google_map_needed' => $google_map_needed,
            'paged' => $paged,
            'search_result_page' => $search_result_page,
            'search_keyword' => stripslashes($search_keyword),
            'search_country' => $search_country,
            'search_state' => $search_state,
            'search_city' => $search_city,
            'search_feature' => $search_feature,
            'search_area' => $search_area,
            'search_status' => $search_status,
            'search_label' => $search_label,
            'search_type' => $search_type,
            'search_bedrooms' => $search_bedrooms,
            'search_bathrooms' => $search_bathrooms,
            'search_min_price' => $search_min_price,
            'search_max_price' => $search_max_price,
            'search_currency' => $search_currency,
            'search_min_area' => $search_min_area,
            'search_max_area' => $search_max_area,
            'search_property_id' => $search_property_id,
            'search_publish_date' => $search_publish_date,
            'search_no_posts' => $prop_no_halfmap,

            'search_location' => $search_location,
            'use_radius' => $use_radius,
            'search_lat' => $search_lat,
            'search_long' => $search_long,
            'search_radius' => $search_radius,

            'transportation' => esc_html__('Transportation', 'houzez'),
            'supermarket' => esc_html__('Supermarket', 'houzez'),
            'schools' => esc_html__('Schools', 'houzez'),
            'libraries' => esc_html__('Libraries', 'houzez'),
            'pharmacies' => esc_html__('Pharmacies', 'houzez'),
            'hospitals' => esc_html__('Hospitals', 'houzez'),
            'sort_by' => $sort_by,
            'measurement_updating_msg' => esc_html__('Updating, Please wait...', 'houzez'),
            'autosearch_text' => esc_html__('Searching...', 'houzez'),
            'currency_updating_msg' => esc_html__('Updating Currency, Please wait...', 'houzez'),
            'currency_position' => houzez_option('currency_position'),
            'submission_currency' => houzez_option('currency_paid_submission'),
            'wire_transfer_text' => esc_html__('To be paid', 'houzez'),
            'direct_pay_thanks' => esc_html__('Thank you. Please check your email for payment instructions.', 'houzez'),
            'direct_payment_title' => esc_html__('Direct Payment Instructions', 'houzez'),
            'direct_payment_button' => esc_html__('SEND ME THE INVOICE', 'houzez'),
            'direct_payment_details' => houzez_option('direct_payment_instruction'),
            'measurement_unit' => $measurement_unit_adv_search,
            'header_map_selected_city' => $header_map_selected_city,
            'thousands_separator' => $thousands_separator,
            'current_tempalte' => $current_template,
            'monthly_payment' => esc_html__('Monthly Payment', 'houzez'),
            'weekly_payment' => esc_html__('Weekly Payment', 'houzez'),
            'bi_weekly_payment' => esc_html__('Bi-Weekly Payment', 'houzez'),
            'compare_button_url' => houzez_get_template_link_2('template/template-compare.php'),
            'template_thankyou' => houzez_get_template_link('template/template-thankyou.php'),
            'compare_page_not_found' => esc_html__('Please create page using compare properties template', 'houzez'),
            'property_detail_top' => esc_attr($property_top_area),
            'keyword_search_field' => $keyword_field,
            'keyword_autocomplete' => $keyword_autocomplete,
            'houzez_date_language' => $houzez_date_language,
            'houzez_default_radius' => $houzez_default_radius,
            'enable_radius_search' => $enable_radius_search,
            'enable_radius_search_halfmap' => $enable_radius_search_halfmap,
            'houzez_primary_color' => $houzez_primary_color,
            'geocomplete_country' => $geocomplete_country,
            'houzez_logged_in' => $houzez_logged_in,
            'ipinfo_location' => houzez_option('ipinfo_location'),
            'gallery_autoplay' => houzez_option('gallery_autoplay'),
            'stripe_page' => houzez_get_template_link('template/template-stripe-charge.php'),
            'twocheckout_page' => houzez_get_template_link('template/template-2checkout.php'),
            'custom_fields' => json_encode($custom_fields_array),
            'markerPricePins' => esc_attr($markerPricePins),
            'houzez_reCaptcha' => $enable_reCaptcha
        )
    );

    wp_enqueue_script( 'numeric', get_stylesheet_directory_uri() . '/js//numeric-1.2.6.js', array('jquery') );
    wp_enqueue_script( 'solar', get_stylesheet_directory_uri() . '/js/solar.js', array('jquery') );
    wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery') );

    if (is_page_template( 'template-map-search.php' )) {
        $googlemap_api_key = houzez_option('googlemap_api_key');

        $minify_js = houzez_option('minify_js');
        $js_minify_prefix = '';

        if ($minify_js != 0) {
            $js_minify_prefix = '.min';
        }

        wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?libraries=places&language=' . get_locale() . '&key=' . esc_html($googlemap_api_key), array('jquery'), '1.0', false);
        wp_enqueue_script('google-map-info-box', get_template_directory_uri() . '/js/infobox' . $js_minify_prefix . '.js', array('google-map'), '1.1.9', false);
        wp_enqueue_script('google-map-marker-clusterer', get_template_directory_uri() . '/js/markerclusterer' . $js_minify_prefix . '.js', array('google-map'), '2.1.1', false);
        wp_enqueue_script('oms.min.js', get_template_directory_uri() . '/js/oms.min.js', array('google-map'), '1.12.2', false);

        wp_enqueue_script( 'richmarker', get_stylesheet_directory_uri() . '/js/richmarker.js', array('jquery') );
        
        wp_enqueue_script( 'map', get_stylesheet_directory_uri() . '/js/map.js', array('jquery') );
    }
}

add_action('admin_enqueue_scripts', 'custom_scripts');
if (is_admin() ){
    function custom_scripts(){
        wp_enqueue_script('ftmetajs', get_template_directory_uri() .'/js/admin/init.js', array('jquery','media-upload','thickbox'));
        wp_enqueue_style( 'houzez-admin.css', get_template_directory_uri(). '/css/admin/admin.css', array(), HOUZEZ_THEME_VERSION, 'all' );

        wp_enqueue_script('houzez-admin-ajax', get_template_directory_uri() .'/js/admin/houzez-admin-ajax.js', array('jquery'));
        wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/admin.js', array('jquery') );
        wp_localize_script('houzez-admin-ajax', 'houzez_admin_vars',
            array( 'ajaxurl'            => admin_url('admin-ajax.php'),
                'paid_status'        =>  __('Paid','houzez')

            )
        );

        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }

        if ( isset( $_GET['taxonomy'] ) && ( $_GET['taxonomy'] == 'property_lifestyle' || $_GET['taxonomy'] == 'property_region' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'houzez_taxonomies', get_template_directory_uri().'/js/admin/metaboxes-taxonomies.js', array( 'jquery', 'wp-color-picker' ), 'houzez' );
        }
    }
}

/**
 * Override function to display price with currency symbol
 */
function houzez_listing_price() {
    global $wpdb;

    $currency_code = get_post_meta( get_the_ID(), 'fave_currency', true);

    $result = $wpdb->get_results(" SELECT currency_symbol FROM " . $wpdb->prefix . "houzez_currencies where currency_code='$currency_code'");

    if (sizeof($result) > 0)
        $symbol = $result[0]->currency_symbol;
    else
        $symbol = '€';

    $sale_price = get_post_meta( get_the_ID(), 'fave_property_price', true );
    $sale_price = number_format ( $sale_price , 0, '', ',' );

    $status = get_the_terms( get_the_ID(), 'property_status' );

    if ($status[0]->slug == 'for-rent')
        echo $symbol . $sale_price . '/mo';
    else
        echo $symbol . $sale_price;
}

function houzez_listing_price_v1() {
    global $wpdb;

    $currency_code = get_post_meta( get_the_ID(), 'fave_currency', true);

    $result = $wpdb->get_results(" SELECT currency_symbol FROM " . $wpdb->prefix . "houzez_currencies where currency_code='$currency_code'");

    if (sizeof($result) > 0)
        $symbol = $result[0]->currency_symbol;
    else
        $symbol = '€';

    $sale_price = get_post_meta( get_the_ID(), 'fave_property_price', true );
    $sale_price = number_format ( $sale_price , 0, '', ',' );
    
    $status = get_the_terms( get_the_ID(), 'property_status' );
    
    if ($status[0]->slug == 'for-rent')
        return $symbol . $sale_price . '/mo';
    else
        return $symbol . $sale_price;
}

/**
 * Rest API Initialization
 */
add_action('rest_api_init', 'register_api');
function register_api() {
    register_rest_route( 'v1', '/houzez_map_search', array(
      'methods' => 'GET',
      'callback' => 'houzez_map_search',
    ));

    register_rest_route( 'v1', '/houzez_map_search', array(
      'methods' => 'GET',
      'callback' => 'houzez_map_search',
    ));

    register_rest_route( 'v1', '/houzez_map_listing', array(
      'methods' => 'POST',
      'callback' => 'houzez_map_listing',
    ));

    register_rest_route( 'v1', '/houzez_make_prop_week', array(
      'methods' => 'POST',
      'callback' => 'houzez_make_prop_week',
    ));

    register_rest_route( 'v1', '/houzez_remove_prop_week', array(
      'methods' => 'POST',
      'callback' => 'houzez_remove_prop_week',
    ));

    register_rest_route( 'v1', '/houzez_doc_upload', array(
      'methods' => 'POST',
      'callback' => 'houzez_doc_upload',
    ));
}

/**
 * Theme Option Update for Redux options
 */
add_filter("redux/options/houzez_options/sections", 'update_redux_options');
function update_redux_options($sections){
    $i = 1;
    $index = 0;
    while ($index == 0) {
        if ($sections[$i]['id'] == 'mem-wire-payment') {
            $index = $i;
        }

        $i++;
    }

    for ($i = sizeof($sections); $i > $index; $i--) {
        $sections[$i + 3] = $sections[$i];
    }

    $sections[$index + 1] = array(
        'title' => 'Bitcoin',
        'id' => 'mem-bitcoin-payment',
        'desc' => '',
        'subsection' => true,
        'priority' => $index + 1,
        'fields' => array(
            array(
                'id' => 'enable_bitcoin',
                'type' => 'switch',
                'title' => 'Enable Bitcoin',
                'required' => array('enable_paid_submission', '!=', 'no'),
                'desc' => '',
                'subtitle' => '',
                'default' => 0,
                'on' => 'Enabled',
                'off' => 'Disabled',
                'section_id' => 'mem-bitcoin-payment'
            ),
            array(
                'id' => 'coinbaseID',
                'type' => 'text',
                'required' => array('enable_bitcoin', '=', '1'),
                'title' => 'Coinbase Client ID',
                'subtitle' => '',
                'desc' => '',
                'default' => '',
                'section_id' => 'mem-bitcoin-payment'
            )
        )
    );

    $sections[$index + 2] = array(
        'title' => 'Apple Pay',
        'id' => 'mem-apple-payment',
        'desc' => '',
        'subsection' => true,
        'priority' => $index + 2,
        'fields' => array(
            array(
                'id' => 'enable_applepay',
                'type' => 'switch',
                'title' => 'Enable Apple Pay',
                'required' => array('enable_paid_submission', '!=', 'no'),
                'desc' => '',
                'subtitle' => '',
                'default' => 0,
                'on' => 'Enabled',
                'off' => 'Disabled',
                'section_id' => 'mem-apple-payment'
            )
        )
    );

    $sections[$index + 3] = array(
        'title' => 'Google Pay',
        'id' => 'mem-google-payment',
        'desc' => '',
        'subsection' => true,
        'priority' => $index + 3,
        'fields' => array(
            array(
                'id' => 'enable_googlepay',
                'type' => 'switch',
                'title' => 'Enable Google Pay',
                'required' => array('enable_paid_submission', '!=', 'no'),
                'desc' => '',
                'subtitle' => '',
                'default' => 0,
                'on' => 'Enabled',
                'off' => 'Disabled',
                'section_id' => 'mem-google-payment'
            ),
            array(
                'id' => 'merchantID',
                'type' => 'text',
                'required' => array('enable_googlepay', '=', '1'),
                'title' => 'Google Merchant ID',
                'subtitle' => '',
                'desc' => '',
                'default' => '',
                'section_id' => 'mem-google-payment'
            )
        )
    );

    $search_field = 0;
    $search_index = 0;
    $property_section = 0;
    $footer_section = 0;
    $footer_cols = 0;

    for ($i = 1; $i < sizeof($sections) + 1; $i++) {
        if ($sections[$i]['id'] == 'adv-search-fields') {
            $search_field = $i;

            $fields = $sections[$i]['fields'];
            $keys = array_keys($fields);

            for ($j = $keys[0]; $j < $keys[sizeof($keys) - 1] + 1; $j++) {
                if ($fields[$j]['id'] == 'adv_show_hide') {
                    $search_index = $j;
                }
            }
        }

        if ($sections[$i]['id'] == 'property-section') {
            $property_section = $i;
        }

        if($sections[$i]['id'] == 'footer') {
            $footer_section = $i;

            foreach ($sections[$footer_section]['fields'] as $fields) {
                if ($fields['id'] == 'footer_cols') {
                    $footer_cols = $fields['priority'];
                }
            }
        }
    }

    $add_option = array(
        'lifestyle' => 'Lifestyle',
        'region' => 'Region'
    );

    $add_default = array(
        'lifestyle' => '1',
        'region' => '1'
    );

    $sections[$search_field]['fields'][$search_index]['options'] = 
        array_insert_after($sections[$search_field]['fields'][$search_index]['options'], 'type', $add_option);
    $sections[$search_field]['fields'][$search_index]['default'] = 
        array_insert_after($sections[$search_field]['fields'][$search_index]['default'], 'type', $add_default);

    unset($sections[$search_field]['fields'][$search_index]['options']['label']);
    unset($sections[$search_field]['fields'][$search_index]['default']['label']);

    $key_arr = array_keys($sections[$property_section]['fields']);
    $property_field_id = $key_arr[0];

    $sections[$property_section]['fields'][$property_field_id]['options']['enabled'] =
        array_insert_after($sections[$property_section]['fields'][$property_field_id]['options']['enabled'], 
            'floor_plans', array('solar_perspective' => 'Solar Perstpective'));

    $six_cols = array(
        'alt' => '6 Column',
        'img' => ReduxFramework::$_url . 'assets/img/4cl.png'
    );

    $sections[$footer_section]['fields'][$footer_cols]['options']['six_cols'] = $six_cols;
    
    return $sections;
}

function array_insert_after( array $array, $key, array $new ) {
    $keys = array_keys( $array );
    $index = array_search( $key, $keys );
    $pos = false === $index ? count( $array ) : $index + 1;
    
    return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
}

/**
 * Add, Remove, Update Meta box
 * For Package Creation, Solar Perstpective Creation
 */
add_filter('rwmb_meta_boxes', 'update_custom_metabox', 1000);
function update_custom_metabox($meta_boxes) {
    $options = array(
        'One-Time', 'Monthly (recurring basis)',
        'Quarterly (recurring basis)', 'Semi-Annually (recurring basis)'
    );

    for ($j = 0; $j < sizeof($meta_boxes); $j++) {
        // Package Creation
        if ($meta_boxes[$j]['pages'][0] == 'houzez_packages') {
            for ($i = sizeof($meta_boxes[$j]['fields']) + 12; $i > 2; $i--) {
                if ($i > 14) {
                    $meta_boxes[$j]['fields'][$i] = $meta_boxes[$j]['fields'][$i - 13];
                } else {
                    $index = floor($i / 3);

                    switch ($i) {
                        case 3:
                        case 6:
                        case 9:
                        case 12:
                            $meta_boxes[$j]['fields'][$i] = array(
                                'name' => 'Payment Option:',
                                'type' => 'custom_html',
                                'std' => '<span>' . $options[$index - 1] . '</span>',
                                'columns' => 4
                            );
                            break;
                        case 4:
                        case 7:
                        case 10:
                        case 13:
                            $meta_boxes[$j]['fields'][$i] = array(
                                'id' => 'fave_payment_option' . $index,
                                'name' => 'Amount',
                                'type' => 'number',
                                'std' => '',
                                'columns' => 4
                            );
                            break;
                        case 5:
                        case 8:
                        case 11:
                        case 14:
                            $meta_boxes[$j]['fields'][$i] = array(
                                'id' => 'fave_payment_btn' . $index,
                                'name' => '',
                                'type' => 'button',
                                'std' => 'Remove',
                                'class' => 'payment_option',
                                'columns' => 3
                            );
                            break;
                    }
                }
            }

            $meta_boxes[$j]['fields'][2] = $meta_boxes[$j]['fields'][1];

            $meta_boxes[$j]['fields'][1] = array(
                'id' => 'fave_billing_unit_add',
                'name' => '',
                'type' => 'button',
                'std' => 'Add',
                'columns' => 2
            );

            $meta_boxes[$j]['fields'][0]['name'] = 'Payment Options';
            $meta_boxes[$j]['fields'][0]['options'] = array(
                '' => 'Select from the following',
                'option1' => 'One-Time',
                'option2' => 'Monthly (recurring basis)',
                'option3' => 'Quarterly (recurring basis)',
                'option4' => 'Semi-Annually (recurring basis)'
            );

            $meta_boxes[$j]['fields'][0]['columns'] = 4;

            ksort($meta_boxes[$j]['fields']);

            $meta_boxes[$j]['fields'][sizeof($meta_boxes[$j]['fields']) - 1]['columns'] = 6;
            $meta_boxes[$j]['fields'][sizeof($meta_boxes[$j]['fields'])] = array(
                'id' => 'fave_encrypt_doc',
                'name' => 'Encryption and Document Storage',
                'type' => 'checkbox',
                'desc' => 'Enable',
                'std' => '',
                'columns' => 6
            );
        }

        // Solar Perspective Creation
        if ($meta_boxes[$j]['pages'][0] == 'property' && $meta_boxes[$j]['tabs']) {
            $perspective = array(
                'id' => 'fave_perspective',
                'name' => 'What direction does the front of the house face?',
                'type' => 'select',
                'options' => array(
                    '' => '',
                    'north' => 'North',
                    'northeast' => 'Northeast',
                    'east' => 'East',
                    'southeast' => 'Southeast',
                    'south' => 'South',
                    'southwest' => 'Southwest',
                    'west' => 'West',
                    'northwest' => 'Northwest'
                ),
                'std' => '',
                'columns' => 6,
                'tab' => 'property_details'
            );

            $k = 0;
            $fields = array();
            foreach ($meta_boxes[$j]['fields'] as $field) {
                $fields[$k++] = $field;
            }

            $meta_boxes[$j]['fields'] = $fields;

            for ($k = sizeof($meta_boxes[$j]['fields']); $k > 14; $k-- ) {
                $meta_boxes[$j]['fields'][$k] = $meta_boxes[$j]['fields'][$k - 1];
            }

            $meta_boxes[$j]['fields'][15] = $perspective;
        }
    }

    return $meta_boxes;
}

/**
 * Remove theme's template for custom templates
 */
function houzez_remove_page_templates( $templates ) {
    unset( $templates['template/template-packages.php'] );
    unset( $templates['template/template-payment.php'] );
    unset( $templates['template/user_dashboard_membership.php'] );
    unset( $templates['template/user_dashboard_properties.php'] );
    return $templates;
}
add_filter( 'theme_page_templates', 'houzez_remove_page_templates' );

/**
 * Homepage Advanced Search
 */
vc_remove_element('hz-advance-search');

if( !function_exists('houzez_advance_search_update') ) {
    function houzez_advance_search_update($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'search_title' => ''
        ), $atts));

        ob_start();

        $search_template = home_url() . '/advanced-search';
        $houzez_local = houzez_get_localization();
        $adv_search_price_slider = houzez_option('adv_search_price_slider');
        $hide_empty = false;
        ?>

        <input type="hidden" id="min_price" value="1000" />
        <input type="hidden" id="max_price" value="500000" />

        <div class="advanced-search advanced-search-module houzez-adv-price-range front">
            <h3 class="advance-title"><?php echo esc_html__('Search Properties for Sale'); ?></h3>

            <form autocomplete="off" method="get" action="<?php echo esc_url($search_template); ?>">
                <div class="row">
                    <input type="hidden" id="type" name="status" value="for-sale" />
                    <div class="col-md-2 col-sm-6 buy-btn">
                        <button type="button" class="btn btn-primary btn-type"><?php echo esc_html__('Buy'); ?></button>
                    </div>
                    <div class="col-md-2 col-sm-6 rent-btn">
                        <button type="button" class="btn btn-type"><?php echo esc_html__('Rent'); ?></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-sm-12 has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" name="city" class="form-control" 
                            placeholder="<?php echo esc_html__('Neighborhood, City'); ?>" />
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-secondary">
                            <?php echo strtoupper($houzez_local['search']); ?>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7 col-sm-12 select-advanced-main">
                        <div class="col-md-3 col-sm-6">
                            <select class="selectpicker bs-select-hidden" name="lifestyle">
                            <?php
                                echo '<option value="">' . esc_html__('Lifestyle') . '</option>';

                                $prop_lifestyle = get_terms(
                                    array(
                                        "property_lifestyle"
                                    ),
                                    array(
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                        'hide_empty' => $hide_empty,
                                        'parent' => 0
                                    )
                                );
                                houzez_hirarchical_options('property_lifestyle', $prop_lifestyle, '');
                            ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select class="selectpicker bs-select-hidden" name="region">
                            <?php
                                echo '<option value="">' . esc_html__('Location') . '</option>';

                                $prop_region = get_terms(
                                    array(
                                        "property_region"
                                    ),
                                    array(
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                        'hide_empty' => $hide_empty,
                                        'parent' => 0
                                    )
                                );
                                houzez_hirarchical_options('property_region', $prop_region, '');
                            ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select class="selectpicker bs-select-hidden" name="type">
                            <?php
                                echo '<option value="">' . esc_html__('Property Type') . '</option>';

                                $prop_type = get_terms(
                                    array(
                                        "property_type"
                                    ),
                                    array(
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                        'hide_empty' => $hide_empty,
                                        'parent' => 0
                                    )
                                );
                                houzez_hirarchical_options('property_type', $prop_type, '');
                            ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <?php 
                                $searched_currency = isset($_GET['currency']) ? $_GET['currency'] : '';
                                $currencies = Houzez_Currencies::get_currency_codes();
                            ?>
                            <select class="selectpicker bs-select-hidden" name="currency">
                                <option value=""><?php echo esc_html__('Fiat/Crypto') ?></option>
                            <?php
                                foreach($currencies as $currency) {
                                    echo '<option '.selected( $currency->currency_code, $searched_currency, false).' value="'.$currency->currency_code.'">'.$currency->currency_code.'</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 range-advanced-main">
                        <?php if( $adv_search_price_slider != 0 ) { ?>
                            <div class="range-text col-md-4 col-lg-3">
                                <input type="hidden" name="min-price" class="min-price-range-hidden range-input" readonly >
                                <input type="hidden" name="max-price" class="max-price-range-hidden range-input" readonly >
                                <span class="range-title"><?php echo $houzez_local['price_range']; ?></span>
                            </div>
                            <div class="range-wrap col-md-8 col-lg-9">
                                <span class="min-price-range"></span>
                                <div class="price-range-advanced"></div>
                                <span class="max-price-range"></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>

        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    add_shortcode('hz-advance-search-update', 'houzez_advance_search_update');
}

vc_map( array(
    "name"  =>  esc_html__( "Advanced Search", "houzez" ),
    "description"           => '',
    "base"                  => "hz-advance-search-update",
    'category'              => "By Favethemes",
    "class"                 => "",
    'admin_enqueue_js'      => "",
    'admin_enqueue_css'     => "",
    "icon"                  => "icon-advance-search",
    "params"                => array(
        array(
            "param_name" => "search_title",
            "type" => "textfield",
            "value" => '',
            "heading" => esc_html__("Title:", "houzez" ),
            "description" => esc_html__( "Enter section title", "houzez" ),
            "save_always" => true
        )
    )
) );

/**
 * Draw Map Search
 */
function houzez_map_search() {
    global $wp_query;

    $status = $_GET['status'];
    $city = $_GET['city'];
    $lifestyle = $_GET['lifestyle'];
    $region = $_GET['region'];
    $type = $_GET['type'];
    $currency = $_GET['currency'];
    $min_price = $_GET['min_price'];
    $max_price = $_GET['max_price'];
    $target = $_GET['target'];

    $search_query = array(
        'post_type' => 'property',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );

    if ( !empty($status) ) {
        $tax_query[] = array(
            'taxonomy' => 'property_status',
            'field' => 'slug',
            'terms' => $status
        );
    }

    if ( !empty($city) ) {
        $tax_query[] = array(
            'taxonomy' => 'property_city',
            'field' => 'slug',
            'terms' => $city
        );
    }

    if ( !empty($lifestyle) ) {
        $tax_query[] = array(
            'taxonomy' => 'property_lifestyle',
            'field' => 'slug',
            'terms' => $lifestyle
        );
    }

    if ( !empty($region) ) {
        $tax_query[] = array(
            'taxonomy' => 'property_region',
            'field' => 'slug',
            'terms' => $region
        );
    }

    if ( !empty($type) ) {
        $tax_query[] = array(
            'taxonomy' => 'property_type',
            'field' => 'slug',
            'terms' => $type
        );
    }

    $tax_count = count($tax_query);

    if ($tax_count > 0) {
        $tax_query['relation'] = 'AND';

        $search_query['tax_query'] = $tax_query;
    }

    if ( !empty($currency) ) {
        $meta_query[] = array(
            'key' => 'fave_currency',
            'value' => $currency,
            'type' => 'CHAR',
            'compare' => '=',
        );
    }

    if ( !empty($min_price) && !empty($min_price) ) {
        $min_price = doubleval( houzez_clean( $min_price ) );
        $max_price = doubleval( houzez_clean( $max_price ) );

        if ( $min_price > 0 && $max_price > $min_price ) {
            $meta_query[] = array(
                'key' => 'fave_property_price',
                'value' => array($min_price, $max_price),
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN',
            );
        }
    }

    $meta_count = count($meta_query);

    if ($meta_count > 0) {
        $meta_query['relation'] = 'AND';

        $search_query['meta_query'] = $meta_query;
    }

    $location_arr = array();
    $price_arr = array();
    $id_arr = array();

    $wp_query = new WP_Query( $search_query );

    if ( $wp_query->have_posts() ) {
        while ( $wp_query->have_posts() ) : $wp_query->the_post();     
            $week = get_post_meta(get_the_ID(), 'fave_week', true);
            $featured = get_post_meta(get_the_ID(), 'fave_featured', true);

            if ($week == '1' || $featured == '1') {
                $location = get_post_meta(get_the_ID(), 'fave_property_location', true);
                array_push($location_arr, $location);

                $price = get_post_meta(get_the_ID(), 'fave_property_price', true);
                $price = number_format ( $price , 0, '', ',' );

                $currency = get_post_meta(get_the_ID(), 'fave_currency', true);

                switch ($currency) {
                    case 'EUR':
                        $price = '€' . $price;
                        break;
                    case 'USD':
                        $price = '$' . $price;
                        break;
                    case 'GBP':
                        $price = '£' . $price;
                        break;
                    case 'XBT':
                        $price = '฿' . $price;
                        break;
                    case '':
                        $price = '€' . $price;
                        break;
                }

                array_push($price_arr, $price);
                array_push($id_arr, get_the_ID());
            }
        endwhile;
        wp_reset_postdata();
    } else {
       
    }

    $result = array(
        'location' => $location_arr,
        'price' => $price_arr,
        'id' => $id_arr
    );

    return $result;
}

function houzez_map_listing() {
    $result = array();

    $id_arr = $_POST['ids'];

    if (sizeof($id_arr) > 0) {
        for ($i = 0; $i < sizeof($id_arr); $i++) {
            $content = '';

            $content .= '<div id="ID-' . $id_arr[$i] .'" class="item-wrap infobox_trigger prop_addon">';
            $content .= '<div class="property-item-v2">';

            $content .= '<div class="figure-block">';
            $content .= '<figure class="item-thumb">';

            $week = get_post_meta($id_arr[$i], 'fave_week', true);
            if ($week == '1')
                $content .= '<span class="label-week label">Property of the Week</span>';

            $featured = get_post_meta($id_arr[$i], 'fave_featured', true);
            if ($featured == '1')
                $content .= '<span class="label-featured label">Featured</span>';

            $content .= get_the_post_thumbnail($id_arr[$i], 150, 120);
            $content .= '<ul class="actions">';
            $content .= '<li><span class="add_fav" data-placement="top" data-toggle="tooltip"';
            $content .= ' data-original-title="Favorite" data-propid="' . $id_arr[$i] . '">';
            $content .= '<i class="fa fa-heart"></i></span></li>';
            $content .= '<li><span data-toggle="tooltip" data-placement="top" title=""';
            $content .= ' data-original-title="(' . sizeof(get_post_meta($id_arr[$i], 'fave_property_images')) . ')">';
            $content .= '<i class="fa fa-camera"></i></span></li>';
            $content .= '</ul>';
            $content .= '</figure>';
            $content .= '</div>';

            $content .= '<div class="item-body">';
            $content .= '<div class="item-detail"><p>';
            $content .= wp_trim_words(get_post_field('post_content', $id_arr[$i]), 20);
            $content .= '</p></div>';
            $content .= '<div class="item-title"><h2 class="property-title">' . get_the_title($id_arr[$i]) .'</h2></div>';
            $content .= '<div class="item-info">';
            $content .= '<ul class="item-amenities">';

            $bed = get_post_meta($id_arr[$i], 'fave_property_bedrooms', true);
            $content .= '<li>';
            $content .= '<img src="' . get_stylesheet_directory_uri() . '/icons/rooms.png">';
            $content .= '<span>' . $bed . '</span>';
            $content .= '</li>';

            $bath = get_post_meta($id_arr[$i], 'fave_property_bathrooms', true);
            $content .= '<li>';
            $content .= '<img src="' . get_stylesheet_directory_uri() . '/icons/bathtub.png">';
            $content .= '<span>' . $bath . '</span>';
            $content .= '</li>';

            $size = get_post_meta($id_arr[$i], 'fave_property_size', true);
            $content .= '<li>';
            $content .= '<img src="' . get_stylesheet_directory_uri() . '/icons/house.png">';
            $content .= '<span>' . $size . ' m²</span>';
            $content .= '</li>';

            $content .= '<li><a target="_blank" href="' . get_the_permalink($id_arr[$i]) . '" class="btn btn btn-primary">';
            $content .= 'Details &gt;</a></li>';

            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="item-price-block"><span class="item-price">';

            $price = get_post_meta($id_arr[$i], 'fave_property_price', true);
            $price = number_format ( $price , 0, '', ',' );

            $currency = get_post_meta($id_arr[$i], 'fave_currency', true);

            switch ($currency) {
                case 'EUR':
                    $price = '€' . $price;
                    break;
                case 'USD':
                    $price = '$' . $price;
                    break;
                case 'GBP':
                    $price = '£' . $price;
                    break;
                case 'XBT':
                    $price = '฿' . $price;
                    break;
                case '':
                    $price = '€' . $price;
                    break;
            }

            $status = wp_get_post_terms($id_arr[$i], 'property_status', array('fields' => 'slugs'));
            $status = $status[0];

            if ($status == 'for-rent')
                $status = '/mo';
            else
                $status = '';

            $content .= $price . $status . '</span></div>';
            $content .= '</div>';

            $content .= '</div></div>';

            array_push($result, $content);
        }
    }

    return $result;
}

/**
 * Custom taxonomy for custom post type 'Property'
 */
add_action( 'admin_menu', 'remove_label_taxonomy', 999 );
function remove_label_taxonomy() {
    remove_submenu_page('edit.php?post_type=property', 'edit-tags.php?taxonomy=property_label&amp;post_type=property');
    remove_meta_box('property_labeldiv', 'property', 'normal');
}

add_action('init', 'overwrite_theme_post_types', 1000);
function overwrite_theme_post_types() {
    $labels_lifestyle = array(
        'name' => __( 'Lifestyles', 'read' ),
        'singular_name' => __( 'Lifestyle', 'read' ),
        'search_items' =>  __( 'Search', 'read' ),
        'all_items' => __( 'All', 'read' ),
        'parent_item' => __( 'Parent Lifestyle', 'read' ),
        'parent_item_colon' => __( 'Parent Lifestyle:', 'read' ),
        'edit_item' => __( 'Edit', 'read' ),
        'update_item' => __( 'Update', 'read' ),
        'add_new_item' => __( 'Add New Lifestyle', 'read' ),
        'new_item_name' => __( 'New Lifestyle Name', 'read' ),
        'menu_name' => __( 'Lifestyles', 'read' )
    );

    register_taxonomy(
        'property_lifestyle', 'property',
        array(
            'hierarchical' => true,
            'labels' => $labels_lifestyle,
            'show_ui' => true,
            'public' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'property_lifestyle'
            )
        )
    );

    $labels_region = array(
        'name' => __( 'Regions', 'read' ),
        'singular_name' => __( 'Region', 'read' ),
        'search_items' =>  __( 'Search', 'read' ),
        'all_items' => __( 'All', 'read' ),
        'parent_item' => __( 'Parent Region', 'read' ),
        'parent_item_colon' => __( 'Parent Region:', 'read' ),
        'edit_item' => __( 'Edit', 'read' ),
        'update_item' => __( 'Update', 'read' ),
        'add_new_item' => __( 'Add New Region', 'read' ),
        'new_item_name' => __( 'New Region Name', 'read' ),
        'menu_name' => __( 'Regions', 'read' )
    );

    register_taxonomy(
        'property_region', 'property',
        array(
            'hierarchical' => true,
            'labels' => $labels_region,
            'show_ui' => true,
            'public' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'property_region'
            )
        )
    );

    $prop_city = array(
        'id' => 'fave_prop_region_meta',
        'title' => 'Property Region',
        'pages' => array('property_region'),
        'context' => 'normal',
        'fields' => array(),
        'local_images' => false,
        'use_with_theme' => false
    );

    $taxnow = isset($_REQUEST['taxonomy'])? $_REQUEST['taxonomy'] : '';

    $prop_city_meta =  new Tax_Meta_Class( $prop_city );
    $prop_city_meta->addImage('fave_prop_type_image',array('name'=> __('Thumbnail ','houzez')));

    if ($taxnow == 'property_region') {  
        $prop_city_meta->check_field_upload();
        $prop_city_meta->check_field_color();
        $prop_city_meta->check_field_date();
        $prop_city_meta->check_field_time();

        $plugin_path = plugins_url('houzez-theme-functionality/extensions/Tax-meta-class');
      
        wp_enqueue_style( 'tax-meta-clss', $plugin_path . '/css/Tax-meta-class.css' );

        wp_enqueue_script( 'tax-meta-clss', $plugin_path . '/js/tax-meta-clss.js', array( 'jquery' ), null, true );

    }
}

if ( !function_exists( 'houzez_get_property_lifestyle_meta' ) ):
    function houzez_get_property_lifestyle_meta( $term_id = false, $field = false ) {
        $defaults = array(
            'color_type' => 'inherit',
            'color' => '#bcbcbc',
            'ppp' => ''
        );

        if ( $term_id ) {
            $meta = get_option( '_houzez_property_lifestyle_'.$term_id );
            $meta = wp_parse_args( (array) $meta, $defaults );
        } else {
            $meta = $defaults;
        }

        if ( $field ) {
            if ( isset( $meta[$field] ) ) {
                return $meta[$field];
            } else {
                return false;
            }
        }
        return $meta;
    }
endif;

if ( !function_exists( 'houzez_property_lifestyle_add_meta_fields' ) ) :
    function houzez_property_lifestyle_add_meta_fields() {
        $houzez_meta = houzez_get_property_lifestyle_meta();
        ?>

        <div class="form-field">
            <label for="Color"><?php _e( 'Global Color', 'houzez'); ?></label><br/>
            <label><input type="radio" name="fave[color_type]" value="inherit" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'inherit' );?>> <?php _e( 'Inherit from default accent color', 'houzez' ); ?></label>
            <label><input type="radio" name="fave[color_type]" value="custom" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'custom' );?>> <?php _e( 'Custom', 'houzez' ); ?></label>
            <div id="fave_color_wrap">
                <p>
                    <input name="fave[color]" type="text" class="fave_colorpicker" value="<?php echo $houzez_meta['color']; ?>" data-default-color="<?php echo $houzez_meta['color']; ?>"/>
                </p>
                <?php if ( !empty( $colors ) ) { echo $colors; } ?>
            </div>
            <div class="clear"></div>
            <p class="howto"><?php _e( 'Choose color', 'houzez' ); ?></p>
        </div>

        <?php
    }
endif;

add_action( 'property_lifestyle_add_form_fields', 'houzez_property_lifestyle_add_meta_fields', 10, 2 );

if ( !function_exists( 'houzez_property_lifestyle_edit_meta_fields' ) ) :
    function houzez_property_lifestyle_edit_meta_fields( $term ) {
        $houzez_meta = houzez_get_property_lifestyle_meta( $term->term_id );
        ?>
        <?php

        $most_used = get_option( 'houzez_recent_colors' );

        $colors = '';

        if ( !empty( $most_used ) ) {
            $colors .= '<p>'.__( 'Recently used', 'houzez' ).': <br/>';
            foreach ( $most_used as $color ) {
                $colors .= '<a href="#" style="width: 20px; height: 20px; background: '.$color.'; float: left; margin-right:3px; border: 1px solid #aaa;" class="fave_colorpick" data-color="'.$color.'"></a>';
            }
            $colors .= '</p>';
        }

        ?>

        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Color', 'houzez' ); ?></label></th>
            <td>
                <label><input type="radio" name="fave[color_type]" value="inherit" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'inherit' );?>> <?php _e( 'Inherit from default accent color', 'houzez' ); ?></label> <br/>
                <label><input type="radio" name="fave[color_type]" value="custom" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'custom' );?>> <?php _e( 'Custom', 'houzez' ); ?></label>
                <div id="fave_color_wrap">
                    <p>
                        <input name="fave[color]" type="text" class="fave_colorpicker" value="<?php echo $houzez_meta['color']; ?>" data-default-color="<?php echo $houzez_meta['color']; ?>"/>
                    </p>
                    <?php if ( !empty( $colors ) ) { echo $colors; } ?>
                </div>
                <div class="clear"></div>
                <p class="howto"><?php _e( 'Choose color', 'houzez' ); ?></p>
            </td>
        </tr>

        <?php
    }
endif;

add_action( 'property_lifestyle_edit_form_fields', 'houzez_property_lifestyle_edit_meta_fields', 10, 2 );


if ( !function_exists( 'houzez_save_property_lifestyle_meta_fields' ) ) :
    function houzez_save_property_lifestyle_meta_fields( $term_id ) {

        if ( isset( $_POST['fave'] ) ) {

            $houzez_meta = array();

            $houzez_meta['color'] = isset( $_POST['fave']['color'] ) ? $_POST['fave']['color'] : 0;
            $houzez_meta['color_type'] = isset( $_POST['fave']['color_type'] ) ? $_POST['fave']['color_type'] : 0;

            update_option( '_houzez_property_lifestyle_'.$term_id, $houzez_meta );

            if ( $houzez_meta['color_type'] == 'custom' ) {
                houzez_update_recent_colors( $houzez_meta['color'] );
            }

            houzez_update_property_lifestyle_colors( $term_id, $houzez_meta['color'], $houzez_meta['color_type'] );
        }

    }
endif;

add_action( 'edited_property_lifestyle', 'houzez_save_property_lifestyle_meta_fields', 10, 2 );
add_action( 'create_property_lifestyle', 'houzez_save_property_lifestyle_meta_fields', 10, 2 );

if ( !function_exists( 'houzez_update_property_lifestyle_colors' ) ):
    function houzez_update_property_lifestyle_colors( $cat_id, $color, $type ) {

        $colors = (array)get_option( 'fave_lifestyle_colors' );

        if ( array_key_exists( $cat_id, $colors ) ) {

            if ( $type == 'inherit' ) {
                unset( $colors[$cat_id] );
            } elseif ( $colors[$cat_id] != $color ) {
                $colors[$cat_id] = $color;
            }

        } else {

            if ( $type != 'inherit' ) {
                $colors[$cat_id] = $color;
            }
        }

        update_option( 'houzez_property_lifestyle_colors', $colors );

    }
endif;

if ( !function_exists( 'houzez_get_property_region_meta' ) ):
    function houzez_get_property_region_meta( $term_id = false, $field = false ) {
        $defaults = array(
            'color_type' => 'inherit',
            'color' => '#bcbcbc',
            'ppp' => ''
        );

        if ( $term_id ) {
            $meta = get_option( '_houzez_property_region_'.$term_id );
            $meta = wp_parse_args( (array) $meta, $defaults );
        } else {
            $meta = $defaults;
        }

        if ( $field ) {
            if ( isset( $meta[$field] ) ) {
                return $meta[$field];
            } else {
                return false;
            }
        }
        return $meta;
    }
endif;

if ( !function_exists( 'houzez_property_region_add_meta_fields' ) ) :
    function houzez_property_region_add_meta_fields() {
        $houzez_meta = houzez_get_property_region_meta();
        ?>

        <div class="form-field">
            <label for="Color"><?php _e( 'Global Color', 'houzez'); ?></label><br/>
            <label><input type="radio" name="fave[color_type]" value="inherit" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'inherit' );?>> <?php _e( 'Inherit from default accent color', 'houzez' ); ?></label>
            <label><input type="radio" name="fave[color_type]" value="custom" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'custom' );?>> <?php _e( 'Custom', 'houzez' ); ?></label>
            <div id="fave_color_wrap">
                <p>
                    <input name="fave[color]" type="text" class="fave_colorpicker" value="<?php echo $houzez_meta['color']; ?>" data-default-color="<?php echo $houzez_meta['color']; ?>"/>
                </p>
                <?php if ( !empty( $colors ) ) { echo $colors; } ?>
            </div>
            <div class="clear"></div>
            <p class="howto"><?php _e( 'Choose color', 'houzez' ); ?></p>
        </div>

        <?php
    }
endif;

add_action( 'property_region_add_form_fields', 'houzez_property_region_add_meta_fields', 10, 2 );

if ( !function_exists( 'houzez_property_region_edit_meta_fields' ) ) :
    function houzez_property_region_edit_meta_fields( $term ) {
        $houzez_meta = houzez_get_property_region_meta( $term->term_id );
        ?>
        <?php

        $most_used = get_option( 'houzez_recent_colors' );

        $colors = '';

        if ( !empty( $most_used ) ) {
            $colors .= '<p>'.__( 'Recently used', 'houzez' ).': <br/>';
            foreach ( $most_used as $color ) {
                $colors .= '<a href="#" style="width: 20px; height: 20px; background: '.$color.'; float: left; margin-right:3px; border: 1px solid #aaa;" class="fave_colorpick" data-color="'.$color.'"></a>';
            }
            $colors .= '</p>';
        }

        ?>

        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Color', 'houzez' ); ?></label></th>
            <td>
                <label><input type="radio" name="fave[color_type]" value="inherit" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'inherit' );?>> <?php _e( 'Inherit from default accent color', 'houzez' ); ?></label> <br/>
                <label><input type="radio" name="fave[color_type]" value="custom" class="fave-radio color-type" <?php checked( $houzez_meta['color_type'], 'custom' );?>> <?php _e( 'Custom', 'houzez' ); ?></label>
                <div id="fave_color_wrap">
                    <p>
                        <input name="fave[color]" type="text" class="fave_colorpicker" value="<?php echo $houzez_meta['color']; ?>" data-default-color="<?php echo $houzez_meta['color']; ?>"/>
                    </p>
                    <?php if ( !empty( $colors ) ) { echo $colors; } ?>
                </div>
                <div class="clear"></div>
                <p class="howto"><?php _e( 'Choose color', 'houzez' ); ?></p>
            </td>
        </tr>

        <?php
    }
endif;

add_action( 'property_region_edit_form_fields', 'houzez_property_region_edit_meta_fields', 10, 2 );


if ( !function_exists( 'houzez_save_property_region_meta_fields' ) ) :
    function houzez_save_property_region_meta_fields( $term_id ) {

        if ( isset( $_POST['fave'] ) ) {

            $houzez_meta = array();

            $houzez_meta['color'] = isset( $_POST['fave']['color'] ) ? $_POST['fave']['color'] : 0;
            $houzez_meta['color_type'] = isset( $_POST['fave']['color_type'] ) ? $_POST['fave']['color_type'] : 0;

            update_option( '_houzez_property_region_'.$term_id, $houzez_meta );

            if ( $houzez_meta['color_type'] == 'custom' ) {
                houzez_update_recent_colors( $houzez_meta['color'] );
            }

            houzez_update_property_region_colors( $term_id, $houzez_meta['color'], $houzez_meta['color_type'] );
        }

    }
endif;

add_action( 'edited_property_region', 'houzez_save_property_region_meta_fields', 10, 2 );
add_action( 'create_property_region', 'houzez_save_property_region_meta_fields', 10, 2 );

if ( !function_exists( 'houzez_update_property_region_colors' ) ):
    function houzez_update_property_region_colors( $cat_id, $color, $type ) {

        $colors = (array)get_option( 'fave_region_colors' );

        if ( array_key_exists( $cat_id, $colors ) ) {

            if ( $type == 'inherit' ) {
                unset( $colors[$cat_id] );
            } elseif ( $colors[$cat_id] != $color ) {
                $colors[$cat_id] = $color;
            }

        } else {

            if ( $type != 'inherit' ) {
                $colors[$cat_id] = $color;
            }
        }

        update_option( 'houzez_property_region_colors', $colors );

    }
endif;

if ( ! function_exists( 'HOUZEZ_property_taxonomies_remove' ) ) {
    function HOUZEZ_property_taxonomies_remove (){
        unregister_widget( 'HOUZEZ_property_taxonomies' );
    }
    add_action( 'widgets_init', 'HOUZEZ_property_taxonomies_remove', 11 );

    require_once( get_stylesheet_directory(). '/houzez-property-taxonomies.php' );
}

function houzez_custom_menu_order() {
    global $submenu;

    $i = 0;
    $features = 0;
    $lifestyles = 0;
    $order = array();

    foreach ($submenu['edit.php?post_type=property'] as $item) {
        array_push($order, $item);

        if ($item[0] == 'Features')
            $features = $i;

        if ($item[0] == 'Lifestyles')
            $lifestyles = $i;

        $i++;
    }

    $lifestyle = $order[$lifestyles];

    for ($i = $lifestyles; $i > $features; $i--) {
        $order[$i] = $order[$i - 1];
    }

    $order[$features + 1] = $lifestyle;
    
    $submenu['edit.php?post_type=property'] = $order;
}

add_filter( 'custom_menu_order', 'houzez_custom_menu_order' );
add_filter( 'menu_order', 'houzez_custom_menu_order' );

/**
 *  Property Addon
 */
if ( !function_exists('houzez_property_addon') ) {
    function houzez_property_addon($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'hz_limit_post_number' => '',
            'hz_select_addon' => ''
        ), $atts));

        ob_start();

        global $paged;
        if (is_front_page()) {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        }

        if ($atts['hz_select_addon'] == 'fave_week')
            $css_classes = 'list-view';
        if ($atts['hz_select_addon'] == 'fave_featured')
            $css_classes = 'grid-view';

        $args = array(
            'order' => 'DESC',
            'orderby' => 'id',
            'post_status' => 'publish',
            'post_type' => 'property',
            'posts_per_page' => $atts['hz_limit_post_number'],
            'meta_key' => $atts['hz_select_addon'],
            'meta_value' => 1,
            'meta_compare' => '='
        );

        $the_query = new WP_Query($args);
        ?>

        <div id="properties_module_section" class="houzez-module property-item-module">
            <div id="properties_module_container">
                <div id="module_properties" class="property-listing <?php echo esc_attr($css_classes);?>">

                    <?php
                        if ($the_query->have_posts()) :
                            while ($the_query->have_posts()) : $the_query->the_post();
                                get_template_part('template-parts/property-for-addon');
                            endwhile;

                            wp_reset_postdata();
                        else:
                            get_template_part('template-parts/property', 'none');
                        endif;
                    ?>

                </div>
            </div>
        </div>

        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;

    }

    add_shortcode('houzez-property_addon', 'houzez_property_addon');
}

vc_map( array(
    "name"  =>  esc_html__( "Property Addon", "houzez" ),
    "description"           => '',
    "base"                  => "houzez-property_addon",
    'category'              => "By Favethemes",
    "class"                 => "",
    'admin_enqueue_js'      => "",
    'admin_enqueue_css'     => "",
    "icon"                  => "icon-addon-settings",
    "params"                => array(
        array(
            "param_name" => "hz_limit_post_number",
            "type" => "textfield",
            "value" => '',
            "heading" => esc_html__("Limit post number:", "houzez" ),
            "description" => esc_html__( "Enter limit post number", "houzez" ),
            "save_always" => true
        ),
        array(
            "param_name" => "hz_select_addon",
            "type" => "dropdown",
            "value" => array( 'Featured Listing' => 'fave_featured', 'Property of the week' => 'fave_week' ),
            "heading" => esc_html__("Select Property Add On", "houzez" ),
            "save_always" => true
        ),
    )
) );

/**
 *  Add Regions to Houzez Grids
 */
vc_remove_element('hz-grids');

$houzez_grids_tax = array();

if (function_exists('vc_remove_param'))
    vc_remove_param('vc_row', 'font_color');
    
$houzez_grids_tax['Property Types'] = 'property_type';
$houzez_grids_tax['Property Status'] = 'property_status';
$houzez_grids_tax['Property Region'] = 'property_region';
$houzez_grids_tax['Property State'] = 'property_state';
$houzez_grids_tax['Property City'] = 'property_city';
$houzez_grids_tax['Property Neighborhood'] = 'property_area';

if( !function_exists('houzez_grid_update') ) {
    function houzez_grid_update($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'houzez_grid_type' => '',
            'houzez_grid_from' => '',
            'houzez_show_child' => '',
            'orderby'           => '',
            'order'             => '',
            'houzez_hide_empty' => '',
            'no_of_terms'       => '',
            'property_type' => '',
            'property_status' => '',
            'property_area' => '',
            'property_state' => '',
            'property_city' => '',
            'property_region' => ''
        ), $atts));

        ob_start();
        $module_type = '';
        $houzez_local = houzez_get_localization();

        $slugs = '';

        if( $houzez_grid_from == 'property_city' ) {
            $slugs = $property_city;

        } else if ( $houzez_grid_from == 'property_area' ) {
            $slugs = $property_area;

        } else if ( $houzez_grid_from == 'property_region' ) {
            $slugs = $property_region;

        } else if ( $houzez_grid_from == 'property_state' ) {
            $slugs = $property_state;

        } else if ( $houzez_grid_from == 'property_status' ) {
            $slugs = $property_status;

        } else {
            $slugs = $property_type;
        }

        if ($houzez_show_child == 1) {
            $houzez_show_child = '';
        }
        if ($houzez_grid_type == 'grid_v2') {
            $module_type = 'location-module-v2';
        }

        if( $houzez_grid_from == 'property_type' ) {
            $custom_link_for = 'fave_prop_type_custom_link';
        } else {
            $custom_link_for = 'fave_prop_taxonomy_custom_link';
        }
        ?>
        <div id="location-module"
             class="houzez-module location-module <?php echo esc_attr( $module_type ); ?> grid <?php echo esc_attr( $houzez_grid_type ); ?>">
            <div class="row">
                <?php
                $tax_name = $houzez_grid_from;
                $taxonomy = get_terms(array(
                    'hide_empty' => $houzez_hide_empty,
                    'parent' => $houzez_show_child,
                    'slug' => houzez_traverse_comma_string($slugs),
                    'number' => $no_of_terms,
                    'orderby' => $orderby,
                    'order' => $order,
                    'taxonomy' => $tax_name,
                ));
                $i = 0;
                $j = 0;
                if ( !is_wp_error( $taxonomy ) ) {
                
                    foreach ($taxonomy as $term) {

                        $i++;
                        $j++;

                        if ($houzez_grid_type == 'grid_v1') {
                            if ($i == 1 || $i == 4) {
                                $col = 'col-sm-4';
                            } else {
                                $col = 'col-sm-8';
                            }
                            if ($i == 4) {
                                $i = 0;
                            }
                        } elseif ($houzez_grid_type == 'grid_v2') {
                            $col = 'col-sm-4';
                        }

                        $term_img = get_tax_meta($term->term_id, 'fave_prop_type_image');
                        $taxonomy_custom_link = get_tax_meta($term->term_id, $custom_link_for);

                        if( !empty($taxonomy_custom_link) ) {
                            $term_link = $taxonomy_custom_link;
                        } else {
                            $term_link = get_term_link($term, $tax_name);
                        }

                        ?>
                        <div class="<?php echo esc_attr($col); ?>">
                            <div class="location-block" <?php if (!empty($term_img['src'])) {
                                echo 'style="background-image: url(' . esc_url($term_img['src']) . ');"';
                            } ?>>
                                <a href="<?php echo esc_url($term_link); ?>">
                                    <div class="location-fig-caption">
                                        <h3 class="heading"><?php echo esc_attr($term->name); ?></h3>

                                        <p class="sub-heading">
                                            <?php echo esc_attr($term->count); ?>
                                            <?php
                                            if ($term->count < 2) {
                                                echo $houzez_local['property'];
                                            } else {
                                                echo $houzez_local['properties'];
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;

    }

    add_shortcode('hz-grids-update', 'houzez_grid_update');
}

vc_map( array(
    "name"  =>  esc_html__( "Houzez Grids", "houzez" ),
    "description"           => 'Show Locations, Property Types, Cities, States in grid',
    "base"                  => "hz-grids-update",
    'category'              => "By Favethemes",
    "class"                 => "",
    'admin_enqueue_js'      => "",
    'admin_enqueue_css'     => "",
    "icon"                  => "icon-hz-grid",
    "params"                => array(

        array(
            "param_name" => "houzez_grid_type",
            "type" => "dropdown",
            "value" => array( 'Grid v1' => 'grid_v1', 'Grid v2' => 'grid_v2' ),
            "heading" => esc_html__("Choose Grid:", "houzez" ),
            "save_always" => true
        ),
        array(
            "param_name" => "houzez_grid_from",
            "type" => "dropdown",
            "value" => $houzez_grids_tax,
            "heading" => esc_html__("Choose Taxonomy", "houzez" ),
            "save_always" => true
        ),
        array(
            'type'          => 'houzez_get_taxonomy_list',
            'heading'       => esc_html__("Property Types", "houzez"),
            'taxonomy'      => 'property_type',
            'is_multiple'   => true,
            'is_hide_empty'   => false,
            'description'   => '',
            'param_name'    => 'property_type',
            "dependency" => Array("element" => "houzez_grid_from", "value" => array("property_type")),
            'save_always'   => true,
            'std'           => '',
        ),
        array(
            'type'          => 'houzez_get_taxonomy_list',
            'heading'       => esc_html__("Property Status", "houzez"),
            'taxonomy'      => 'property_status',
            'is_multiple'   => true,
            'is_hide_empty'   => false,
            'description'   => '',
            'param_name'    => 'property_status',
            "dependency" => Array("element" => "houzez_grid_from", "value" => array("property_status")),
            'save_always'   => true,
            'std'           => '',
        ),
        array(
            'type'          => 'houzez_get_taxonomy_list',
            'heading'       => esc_html__("Property Regions", "houzez"),
            'taxonomy'      => 'property_region',
            'is_multiple'   => true,
            'is_hide_empty'   => false,
            'description'   => '',
            'param_name'    => 'property_region',
            "dependency" => Array("element" => "houzez_grid_from", "value" => array("property_region")),
            'save_always'   => true,
            'std'           => '',
        ),
        array(
            'type'          => 'houzez_get_taxonomy_list',
            'heading'       => esc_html__("Property States", "houzez"),
            'taxonomy'      => 'property_state',
            'is_multiple'   => true,
            'is_hide_empty'   => false,
            'description'   => '',
            'param_name'    => 'property_state',
            "dependency" => Array("element" => "houzez_grid_from", "value" => array("property_state")),
            'save_always'   => true,
            'std'           => '',
        ),
        array(
            'type'          => 'houzez_get_taxonomy_list',
            'heading'       => esc_html__("Property Cities", "houzez"),
            'taxonomy'      => 'property_city',
            'is_multiple'   => true,
            'is_hide_empty'   => false,
            'description'   => '',
            'param_name'    => 'property_city',
            "dependency" => Array("element" => "houzez_grid_from", "value" => array("property_city")),
            'save_always'   => true,
            'std'           => '',
        ),

        array(
            'type'          => 'houzez_get_taxonomy_list',
            'heading'       => esc_html__("Property Areas", "houzez"),
            'taxonomy'      => 'property_area',
            'is_multiple'   => true,
            'is_hide_empty'   => false,
            'description'   => '',
            'param_name'    => 'property_area',
            "dependency" => Array("element" => "houzez_grid_from", "value" => array("property_area")),
            'save_always'   => true,
            'std'           => '',
        ),

        array(
            "param_name" => "houzez_show_child",
            "type" => "dropdown",
            "value" => array( 'No' => '0', 'Yes' => '1' ),
            "heading" => esc_html__("Show Child:", "houzez" ),
            "save_always" => true
        ),
        array(
            "param_name" => "orderby",
            "type" => "dropdown",
            "value" => array( 'Name' => 'name', 'Count' => 'count', 'ID' => 'id' ),
            "heading" => esc_html__("Order By:", "houzez" ),
            "save_always" => true
        ),
        array(
            "param_name" => "order",
            "type" => "dropdown",
            "value" => array( 'ASC' => 'ASC', 'DESC' => 'DESC' ),
            "heading" => esc_html__("Order:", "houzez" ),
            "save_always" => true
        ),
        array(
            "param_name" => "houzez_hide_empty",
            "type" => "dropdown",
            "value" => array( 'Yes' => '1', 'No' => '0' ),
            "heading" => esc_html__("Hide Empty:", "houzez" ),
            "save_always" => true
        ),
        array(
            "param_name" => "no_of_terms",
            "type" => "textfield",
            "value" => '',
            "heading" => esc_html__("Number of Items to Show:", "houzez" ),
            "save_always" => true
        )

    ) // end params
) );

/*
 * Widget Name: Property Add On: Property of the week
 */

function widget_content($args, $instance, $type) {
    global $before_widget, $after_widget, $before_title, $after_title, $post;
    extract( $args );

    $allowed_html_array = array(
        'div' => array(
            'id' => array(),
            'class' => array()
        ),
        'h3' => array(
            'class' => array()
        )
    );

    $title = apply_filters('widget_title', $instance['title'] );
    $items_num = $instance['items_num'];
    $widget_type = $instance['widget_type'];
    
    echo wp_kses( $before_widget, $allowed_html_array );

    if ($title) 
        echo wp_kses( $before_title, $allowed_html_array ) . $title . wp_kses( $after_title, $allowed_html_array );

    $wp_qry = new WP_Query(
        array(
            'post_type' => 'property',
            'posts_per_page' => $items_num,
            'meta_key' => $type,
            'meta_value' => '1',
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish'
        )
    );
    ?>
    
    <div class="widget-body">

        <?php if( $widget_type == "slider" ) { ?>
        <div class="property-widget-slider slide-animated owl-carousel owl-theme">
        <?php } else { ?>
        <div class="item-wrap infobox_trigger prop_addon">
        <?php } ?>

        <?php if ($wp_qry->have_posts()): while($wp_qry->have_posts()): $wp_qry->the_post(); ?>
            <?php $prop_featured = get_post_meta( get_the_ID(), 'fave_featured', true ); ?>
            <?php $prop_week = get_post_meta( get_the_ID(), 'fave_week', true ); ?>            
            <?php $prop_images = get_post_meta( get_the_ID(), 'fave_property_images', false ); ?>

            <?php if( $widget_type == "slider" ) { ?>
                <div class="item">
                    <div class="figure-block">
                        <figure class="item-thumb">
                            <?php if( $prop_featured != 0 ) { ?>
                                <span class="label-featured label label-success">
                                    <?php esc_html_e( 'Featured', 'houzez' ); ?>
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
                                    the_post_thumbnail( 'houzez-property-thumb-image' );
                                }else{
                                    houzez_image_placeholder( 'houzez-property-thumb-image' );
                                }
                                ?>
                            </a>
                            <figcaption class="thumb-caption">
                                <div class="cap-price pull-left"><?php echo houzez_listing_price(); ?></div>
                                <ul class="list-unstyled actions pull-right">
                                    <li>
                                        <span title="" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo count($prop_images); ?> <?php echo esc_html__('Photos', 'houzez'); ?>">
                                            <i class="fa fa-camera"></i>
                                        </span>
                                    </li>
                                </ul>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            <?php } else { ?>
                <div class="figure-block">
                    <figure class="item-thumb">
                        <?php if( $prop_featured != 0 ) { ?>
                                <span class="label-featured label label-success">
                                    <?php esc_html_e( 'Featured', 'houzez' ); ?>
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
                                the_post_thumbnail( 'houzez-property-thumb-image' );
                            }else {
                                houzez_image_placeholder( 'houzez-property-thumb-image' );
                            }
                            ?>
                        </a>
                        <figcaption class="thumb-caption clearfix">
                            <div class="cap-price pull-left"><?php echo houzez_listing_price(); ?></div>

                            <ul class="list-unstyled actions pull-right">
                                <li>
                                    <span title="" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo count($prop_images); ?> <?php echo esc_html__('Photos', 'houzez'); ?>">
                                        <i class="fa fa-camera"></i>
                                    </span>
                                </li>
                            </ul>
                        </figcaption>
                    </figure>
                </div>
                <div class="item-body">
                    <div class="item-detail">
                        <p><?php echo wp_trim_words( get_the_content(), 20 ); ?></p>
                    </div>

                    <div class="item-title">
                        <?php
                            echo '<h2 class="property-title">'. esc_attr( get_the_title() ). '</h2>';
                        ?>
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
                                <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-primary btn-block">
                                    <?php echo esc_html__( 'Details >', 'houzez' ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>

                     <div class="item-price-block">
                        <span class="item-price">
                            <?php echo houzez_listing_price_v1(); ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
        <?php endwhile; endif; ?>

        </div>
        <?php wp_reset_postdata(); ?>
        
    </div>


<?php 
    echo wp_kses( $after_widget, $allowed_html_array );
}
 
class HOUZEZ_property_week extends WP_Widget {
    /**
     * Register widget
    **/
    public function __construct() {
        
        parent::__construct(
            'houzez_property_week', // Base ID
            esc_html__( 'HOUZEZ: Property Add On: Property of the Week', 'houzez' ), // Name
            array( 'description' => esc_html__( 'Show property of the week', 'houzez' ), ) // Args
        );
        
    }
    /**
     * Front-end display of widget
    **/
    public function widget( $args, $instance ) {
        widget_content($args, $instance, 'fave_week');
    }
    /**
     * Sanitize widget form values as they are saved
    **/
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        /* Strip tags to remove HTML. For text inputs and textarea. */
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['items_num'] = strip_tags( $new_instance['items_num'] );
        $instance['widget_type'] = strip_tags( $new_instance['widget_type'] );
        
        return $instance;
    }
    /**
     * Back-end widget form
    **/
    public function form( $instance ) {
        /* Default widget settings. */
        $defaults = array(
            'title' => 'Property of the Week',
            'items_num' => '1',
            'widget_type' => 'entries'
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        
    ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'houzez'); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'items_num' ) ); ?>"><?php esc_html_e('Maximum posts to show:', 'houzez'); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'items_num' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'items_num' ) ); ?>" value="<?php echo esc_attr( $instance['items_num'] ); ?>" size="1" />
        </p>
        <p>
            <input type="radio" id="<?php echo esc_attr( $this->get_field_id( 'slider' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_type' ) ); ?>" <?php if ($instance["widget_type"] == 'slider')  echo 'checked="checked"'; ?> value="slider" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'slider' ) ); ?>"><?php esc_html_e( 'Display Properties as Slider', 'houzez' ); ?></label><br />

            <input type="radio" id="<?php echo esc_attr( $this->get_field_id( 'entries' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_type' ) ); ?>" <?php if ($instance["widget_type"] == 'entries') echo 'checked="checked"'; ?> value="entries" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'entries' ) ); ?>"><?php esc_html_e( 'Display Properties as List', 'houzez' ); ?></label>
        </p>
        
    <?php
    }

}

if ( ! function_exists( 'HOUZEZ_property_week_loader' ) ) {
    function HOUZEZ_property_week_loader (){
     register_widget( 'HOUZEZ_property_week' );
    }
     add_action( 'widgets_init', 'HOUZEZ_property_week_loader' );
}

/*
 * Widget Name: Property Add On: Featured Listing
 */
 
class HOUZEZ_featured_listing extends WP_Widget {
    /**
     * Register widget
    **/
    public function __construct() {
        
        parent::__construct(
            'houzez_featured_listing', // Base ID
            esc_html__( 'HOUZEZ: Property Add On: Featured Listing', 'houzez' ), // Name
            array( 'description' => esc_html__( 'Show featured listing', 'houzez' ), ) // Args
        );
        
    }
    /**
     * Front-end display of widget
    **/
    public function widget( $args, $instance ) {
        widget_content($args, $instance, 'fave_featured');
    }
    /**
     * Sanitize widget form values as they are saved
    **/
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        /* Strip tags to remove HTML. For text inputs and textarea. */
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['items_num'] = strip_tags( $new_instance['items_num'] );
        $instance['widget_type'] = strip_tags( $new_instance['widget_type'] );
        
        return $instance;
    }
    /**
     * Back-end widget form
    **/
    public function form( $instance ) {
        
        /* Default widget settings. */
        $defaults = array(
            'title' => 'Featured Listing',
            'items_num' => '5',
            'widget_type' => 'entries'
        );
        $instance = wp_parse_args( (array) $instance, $defaults );    
    ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'houzez'); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'items_num' ) ); ?>"><?php esc_html_e('Maximum posts to show:', 'houzez'); ?></label>
            <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'items_num' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'items_num' ) ); ?>" value="<?php echo esc_attr( $instance['items_num'] ); ?>" size="1" />
        </p>
        <p>
            <input type="radio" id="<?php echo esc_attr( $this->get_field_id( 'slider' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_type' ) ); ?>" <?php if ($instance["widget_type"] == 'slider')  echo 'checked="checked"'; ?> value="slider" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'slider' ) ); ?>"><?php esc_html_e( 'Display Properties as Slider', 'houzez' ); ?></label><br />

            <input type="radio" id="<?php echo esc_attr( $this->get_field_id( 'entries' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_type' ) ); ?>" <?php if ($instance["widget_type"] == 'entries') echo 'checked="checked"'; ?> value="entries" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'entries' ) ); ?>"><?php esc_html_e( 'Display Properties as List', 'houzez' ); ?></label>
        </p>
        
    <?php
    }

}

if ( ! function_exists( 'HOUZEZ_featured_listing_loader' ) ) {
    function HOUZEZ_featured_listing_loader (){
     register_widget( 'HOUZEZ_featured_listing' );
    }
     add_action( 'widgets_init', 'HOUZEZ_featured_listing_loader' );
}

/**
 * Footer Mortgage Calculator
 */
if ( ! function_exists( 'HOUZEZ_mortgage_calculator_remove' ) ) {
    function HOUZEZ_mortgage_calculator_remove (){
        unregister_widget( 'HOUZEZ_mortgage_calculator' );
    }
    add_action( 'widgets_init', 'HOUZEZ_mortgage_calculator_remove', 11 );

    require_once( get_stylesheet_directory() . '/houzez-mortgage-calculator.php' );
}

/**
 * Footer Mortgage Sitemap
 */
/* Add 2 widgets for footer */
add_action('widgets_init', 'houzez_add_widget', 20);
if( !function_exists('houzez_add_widget') ) {
    function houzez_add_widget() {
        register_sidebar(array(
            'name' => esc_html__('Footer Area 5', 'houzez'),
            'id' => 'footer-sidebar-5',
            'description' => esc_html__('Widgets in this area will be show in footer column five', 'houzez'),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-top"><h3 class="widget-title">',
            'after_title' => '</h3></div>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Footer Area 6', 'houzez'),
            'id' => 'footer-sidebar-6',
            'description' => esc_html__('Widgets in this area will be show in footer column six', 'houzez'),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-top"><h3 class="widget-title">',
            'after_title' => '</h3></div>',
        ));
    }
}

/**
 * Featured Listing/Property of the Week
 */

/* -----------------------------------------------------------------------------------------------------------
 *  Make Property of the Week
 -------------------------------------------------------------------------------------------------------------*/
 if( !function_exists('houzez_make_prop_week') ):
    function  houzez_make_prop_week(){
        global $current_user;

        wp_get_current_user();
        $userID =   $current_user->ID;

        $prop_id = intval( $_POST['propid'] );
        $post = get_post( $prop_id );

        if( $post->post_author == $userID ) {
            update_post_meta($prop_id, 'fave_week', 1);
        }

        return ($post->post_author == $userID);
    }
endif;

/* -----------------------------------------------------------------------------------------------------------
 *  Remove Property of the Week
 -------------------------------------------------------------------------------------------------------------*/
if( !function_exists('houzez_remove_prop_week') ):
    function  houzez_remove_prop_week(){
        global $current_user;

        wp_get_current_user();
        $userID =   $current_user->ID;

        $prop_id = intval( $_POST['propid'] );
        $post = get_post( $prop_id );

        if( $post->post_author == $userID ) {
            update_post_meta($prop_id, 'fave_week', 0);
        }

        return ($post->post_author == $userID);
    }
endif;

/**
 * Package Creation
 */

// Use update_custom_metabox
add_action( 'wp_ajax_nopriv_houzez_remove_payment_option', 'houzez_remove_payment_option');
add_action( 'wp_ajax_houzez_remove_payment_option', 'houzez_remove_payment_option' );

if( !function_exists('houzez_remove_payment_option') ):
    function  houzez_remove_payment_option(){
        $postID = $_POST['postID'];
        $metaKey = $_POST['metaKey'];

        delete_post_meta($postID, $metaKey);

        wp_die();
    }
endif;

/**
 * Encrypt Document Upload
 */

function houzez_doc_upload() {
    $filename = $_FILES['file']['name'];

    $name = pathinfo($filename, PATHINFO_FILENAME);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    $increment = '';

    $basename = '';

    $upload = wp_upload_dir();
    $dir = $upload['basedir'] . '/../../Documents/';

    if (file_exists($dir . $filename)) {
        $increment = 1;

        while(file_exists($dir . $name . '_' . $increment . '.' . $extension)) {
            $increment++;
        }

        $basename = $dir . $name . '_' . $increment . '.' . $extension;
    } else {
        $basename = $dir . $filename;
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $basename)) {
        return "success";
    } else {
        return "fail";
    }
}

/**
 * Membership Function
 */

function houzez_get_user_current_package( $user_id ) {

    $remaining_listings = houzez_get_remaining_listings( $user_id );
    $pack_featured_remaining_listings = houzez_get_featured_remaining_listings( $user_id );
    $package_id = houzez_get_user_package_id( $user_id );
    $packages_page_link = houzez_get_template_link('template-advanced-package.php');

    if( $remaining_listings == -1 ) {
        $remaining_listings = esc_html__('Unlimited', 'houzez');
    }

    if( !empty( $package_id ) ) {

        $seconds = 0;
        $pack_title = get_the_title( $package_id );
        $pack_listings = get_post_meta( $package_id, 'fave_package_listings', true );
        $pack_unmilited_listings = get_post_meta( $package_id, 'fave_unlimited_listings', true );
        $pack_featured_listings = get_post_meta( $package_id, 'fave_package_featured_listings', true );
        $pack_billing_period = get_post_meta( $package_id, 'fave_billing_time_unit', true );
        $pack_billing_frequency = get_post_meta( $package_id, 'fave_billing_unit', true );
        $pack_date = strtotime ( get_user_meta( $user_id, 'package_activation',true ) );

        switch ( $pack_billing_period ) {
            case 'Day':
                $seconds = 60*60*24;
                break;
            case 'Week':
                $seconds = 60*60*24*7;
                break;
            case 'Month':
                $seconds = 60*60*24*30;
                break;
            case 'Year':
                $seconds = 60*60*24*365;
                break;
        }

        $pack_time_frame = $seconds * $pack_billing_frequency;
        $expired_date    = $pack_date + $pack_time_frame;
        $expired_date = date_i18n( get_option('date_format'),  $expired_date );

        echo '<div class="pkgs-status">';
        echo '<h4 class="pkgs-status-title">'.esc_html__( 'Your Current Package', 'houzez' ).'</h4>';
        echo '<ul>';
        echo '<li><strong>'.esc_attr( $pack_title ).'</strong></li>';

        if( $pack_unmilited_listings == 1 ) {
            echo '<li><span class="pkg-status-left">'.esc_html__('Listings Included: ','houzez').'</span><span class="pkg-status-right">'.esc_html__('unlimited listings ','houzez').'</span></li>';
            echo '<li><span class="pkg-status-left">'.esc_html__('Listings Remaining: ','houzez').'</span><span class="pkg-status-right">'.esc_html__('unlimited listings ','houzez').'</li>';
        } else {
            echo '<li><span class="pkg-status-left">'.esc_html__('Listings Included: ','houzez').'</span><span class="pkg-status-right">'.esc_attr( $pack_listings ).'</li>';
            echo '<li><span class="pkg-status-left">'.esc_html__('Listings Remaining: ','houzez').'</span><span class="listings_remainings pkg-status-right">'.esc_attr( $remaining_listings ).'</span></li>';
        }

        echo '<li><span class="pkg-status-left">'.esc_html__('Featured Included: ','houzez').'</span><span class="pkg-status-right">'.esc_attr( $pack_featured_listings ).'</span></li>';
        echo '<li><span class="pkg-status-left">'.esc_html__('Featured Remaining: ','houzez').'</span><span class="featured_listings_remaining pkg-status-right">'.esc_attr( $pack_featured_remaining_listings ).'</span></li>';
        echo '<li><span class="pkg-status-left">'.esc_html__('Ends On','houzez').'</span><span class="pkg-status-right">';
        echo ' '.esc_attr( $expired_date );
        echo '</span></li>';
        echo '</ul>';
        echo '</div>';

        if( ! is_page_template( 'template/user_dashboard_membership.php' ) ) {
            echo '<a href="' . esc_url($packages_page_link) . '" class="plan-link btn btn-primary btn-block"> ' . esc_html__('Change Membership Plan', 'houzez') . ' </a>';
        }

    }
}

/**
 * Membership Package Payment (Bitcoin, GooglePay, ApplePay)
 */
function houzez_stripe_payment_membership( $pack_price, $title ) {

    require_once( get_template_directory() . '/framework/stripe-php/init.php' );
    $stripe_secret_key = houzez_option('stripe_secret_key');
    $stripe_publishable_key = houzez_option('stripe_publishable_key');

    $current_user = wp_get_current_user();

    $userID = $current_user->ID;
    $user_login = $current_user->user_login;
    $user_email = get_the_author_meta('user_email', $userID);

    $stripe = array(
        "secret_key" => $stripe_secret_key,
        "publishable_key" => $stripe_publishable_key
    );

    \Stripe\Stripe::setApiKey($stripe['secret_key']);

    $submission_currency = houzez_option('currency_paid_submission');

    $package_price_for_stripe = $pack_price * 100;

    print '
        <div class="houzez_stripe_membership " id="'.  sanitize_title($title).'">
            <script src="https://checkout.stripe.com/checkout.js" id="stripe_script"
            class="stripe-button"
            data-key="'. $stripe_publishable_key.'"
            data-amount="'.$package_price_for_stripe.'"
            data-email="'.$user_email.'"
            data-currency="'.$submission_currency.'"
            data-zip-code="true"
            data-locale="'.get_locale().'"
            data-billing-address="true"
            data-label="'.__('Pay with Credit Card','houzez').'"
            data-description="'.$title.' '.__('Package Payment','houzez').'">
            </script>
        </div>
        <input type="hidden" name="userID" value="' . $userID . '">
        <input type="hidden" id="pay_ammout" name="pay_ammout" value="' . $package_price_for_stripe . '">';
}

add_action( 'wp_ajax_nopriv_houzez_bitcoin_package_payment', 'houzez_bitcoin_package_payment' );
add_action( 'wp_ajax_houzez_bitcoin_package_payment', 'houzez_bitcoin_package_payment' );

function houzez_bitcoin_package_payment() {

}

function houzez_googlepay_payment_membership( $pack_price, $title ) {
    require_once( get_template_directory() . '/framework/stripe-php/init.php' );

    $stripe_secret_key = houzez_option('stripe_secret_key');
    $stripe_publishable_key = houzez_option('stripe_publishable_key');

    $stripe = array(
        "secret_key" => $stripe_secret_key,
        "publishable_key" => $stripe_publishable_key
    );

    \Stripe\Stripe::setApiKey($stripe['secret_key']);

    echo '<script src="https://js.stripe.com/v3/"></script>
           <div id="google-pay-button"></div>
           <script type="text/javascript">
            var stripe = Stripe("' . $stripe_publishable_key . '");

            var googlePay = stripe.paymentRequest({
                country: "US",
                currency: "eur",
                total: {
                label: "' . $title . '",
                amount: ' . $pack_price * 100 . ',
                },
                requestPayerName: true,
                requestPayerEmail: true,
            });

            var elements = stripe.elements();
            var googleButton = elements.create("paymentRequestButton", {
                paymentRequest: googlePay,
            });

            googlePay.canMakePayment().then(function(result) {
                if (result) {
                    googleButton.mount("#google-pay-button");
                } else {
                    document.getElementById("google-pay-button").style.display = "none";
                    //document.getElementById("google-pay-button").closest(".method-row").style.display = "none";
                }
            });

            googlePay.on("token", function(ev) {
              fetch("/charges", {
                method: "POST",
                body: JSON.stringify({token: ev.token.id}),
                headers: {"content-type": "application/json"},
              })
              .then(function(response) {
                if (response.ok) {
                  ev.complete("success");
                } else {
                  ev.complete("fail");
                }
              });
            });
           </script>
           ';
}

function houzez_applepay_package_payment( $pack_price, $title ) {
    require_once( get_template_directory() . '/framework/stripe-php/init.php' );

    $stripe_secret_key = houzez_option('stripe_secret_key');
    $stripe_publishable_key = houzez_option('stripe_publishable_key');

    $stripe = array(
        "secret_key" => $stripe_secret_key,
        "publishable_key" => $stripe_publishable_key
    );

    \Stripe\Stripe::setApiKey($stripe['secret_key']);

    \Stripe\ApplePayDomain::create([
      'domain_name' => get_site_url()
    ]);

    echo '<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
            <style>
              #apple-pay-button {
                display: none;
                background-color: black;
                background-image: -webkit-named-image(apple-pay-logo-white);
                background-size: 100% 100%;
                background-origin: content-box;
                background-repeat: no-repeat;
                width: 100%;
                height: 44px;
                padding: 10px 0;
                border-radius: 10px;
              }
            </style>
            <button id="apple-pay-button"></button>
            <script type="text/javascript">
                Stripe.setPublishableKey("' . $stripe['publishable_key'] . '");

                Stripe.applePay.checkAvailability(function(available) {
                  if (available) {
                    document.getElementById("apple-pay-button").style.display = "block";
                  } else {
                    //document.getElementById("apple-pay-button").closest(".method-row").style.display = "none";
                  }
                });
            </script>
        ';
}
?>