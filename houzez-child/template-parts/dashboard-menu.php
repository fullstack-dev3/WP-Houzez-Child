<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 08/01/16
 * Time: 7:30 PM
 */
global $current_user, $post, $houzez_local;
$current_user = wp_get_current_user();
$dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
$dashboard_listings = houzez_get_template_link_2('template-user-dashboard-properties.php');
$dashboard_add_listing = houzez_get_template_link_2('template/submit_property.php');
$dashboard_favorites = houzez_get_template_link_2('template/user_dashboard_favorites.php');
$dashboard_search = houzez_get_template_link_2('template/user_dashboard_saved_search.php');
$dashboard_invoices = houzez_get_template_link_2('template/user_dashboard_invoices.php');
$dashboard_msgs = houzez_get_template_link_2('template/user_dashboard_messages.php');
$dashboard_membership = houzez_get_template_link_2('template-user-dashboard-membership.php');
$dashboard_gdpr = houzez_get_template_link_2('template/user_dashboard_gdpr.php');
$dashboard_seen_msgs = add_query_arg( 'view', 'inbox', $dashboard_msgs );
$dashboard_unseen_msgs = add_query_arg( 'view', 'sent', $dashboard_msgs );
$home_link = home_url('/');
$enable_paid_submission = houzez_option('enable_paid_submission');

$ac_profile = $ac_props = $ac_add_prop = $ac_fav = $ac_search = $ac_invoices = $ac_msgs = $ac_mem = $ac_gdpr = '';
if( is_page_template( 'template/user_dashboard_profile.php' ) ) {
    $ac_profile = 'class=active';
} elseif ( is_page_template( 'template-user-dashboard-properties.php' ) ) {
    $ac_props = 'class=active';
} elseif ( is_page_template( 'template/submit_property.php' ) ) {
    $ac_add_prop = 'class=active';
} elseif ( is_page_template( 'template/user_dashboard_saved_search.php' ) ) {
    $ac_search = 'class=active';
} elseif ( is_page_template( 'template/user_dashboard_favorites.php' ) ) {
    $ac_fav = 'class=active';
} elseif ( is_page_template( 'template/user_dashboard_invoices.php' ) ) {
    $ac_invoices = 'class=active';
} elseif ( is_page_template( 'template/user_dashboard_messages.php' ) ) {
    $ac_msgs = 'class=active';
} elseif ( is_page_template( 'template-user-dashboard-membership.php' ) ) {
    $ac_mem = 'class=active';
} elseif ( is_page_template( 'template/user_dashboard_gdpr.php' ) ) {
    $ac_gdpr = 'class=active';
}

$agency_agents = add_query_arg( 'agents', 'list', $dash_profile_link );
$agency_agent_add = add_query_arg( 'agent', 'add_new', $dash_profile_link );

$all = add_query_arg( 'prop_status', 'all', $dashboard_listings );
$package = add_query_arg( 'prop_status', 'package', $dashboard_listings );
$approved = add_query_arg( 'prop_status', 'approved', $dashboard_listings );
$pending = add_query_arg( 'prop_status', 'pending', $dashboard_listings );
$expired = add_query_arg( 'prop_status', 'expired', $dashboard_listings );
$draft = add_query_arg( 'prop_status', 'draft', $dashboard_listings );
$on_hold = add_query_arg( 'prop_status', 'on_hold', $dashboard_listings );
$ac_approved = $ac_package = $ac_pending = $ac_expired = $ac_all = $ac_draft = $ac_on_hold = $ac_agents = $ac_agent_new = '';

if( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'approved' ) {
    $ac_approved = 'class=active';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'package' ) {
    $ac_package = 'class=active';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'pending' ) {
    $ac_pending = 'class=active';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'expired' ) {
    $ac_expired = 'class=active';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'draft' ) {
    $ac_draft = 'class=active';
} elseif( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'on_hold' ) {
    $ac_on_hold = 'class=active';
} else {
    $ac_all = 'class=active';
}

if (is_page_template('template-user-dashboard-package.php') ||
    is_page_template('template-addon-payment.php')
   ) {
    $ac_props = 'class=active';
    $ac_all = '';
    $ac_package = 'class=active';
}

if( isset( $_GET['agents'] ) && $_GET['agents'] == 'list' ) {
    $ac_agents = 'class=active';
} elseif( isset( $_GET['agent'] ) && $_GET['agent'] == 'add_new' ) {
    $ac_agent_new = 'class=active';
}
?>

<div class="user-dashboard-left">
    <div class="dashboard-bar">
        <ul class="board-panel-menu">
            <?php
            if( !empty( $dash_profile_link ) ) {
                echo '<li ' .esc_attr( $ac_profile ). '> <a href="' . esc_url($dash_profile_link) . '"><i class="fa fa-user"></i>' . esc_html__('My profile', 'houzez') . '</a>';
                if ( in_array('houzez_agency', (array)$current_user->roles) ) {
                    echo '<ul class="sub-menu">
                        <li ' . esc_attr($ac_agents) . '><a href="' . esc_url($agency_agents) . '">' . esc_html__('Agents', 'houzez') . '</a></li>
                        <li ' . esc_attr($ac_agent_new) . '><a href="' . esc_url($agency_agent_add) . '" ' . esc_attr($ac_agent_new) . '>' . esc_html__('Add New Agent', 'houzez') . '</a></li>
                    </ul>';
                }
                echo '</li>';
            }
            if( !empty( $dashboard_listings ) && houzez_check_role() ) {
                echo '<li ' .esc_attr( $ac_props ). '> <a href="' . esc_url($dashboard_listings) . '"><i class="fa fa-building"></i>' . esc_html__('My Properties', 'houzez') . '</a>
                <ul class="sub-menu">
                    <li '.esc_attr( $ac_all ).'><a href="' . esc_url($all) . '">'.$houzez_local['all'].'</a></li>
                    <li '.esc_attr( $ac_package).'><a href="'.esc_url($package).'" '.esc_attr($ac_package).'>Additional Package Options</a></li>
                    <li '.esc_attr( $ac_approved ).'><a href="'.esc_url($approved).'" '.esc_attr($ac_approved).'>'.$houzez_local['published'].'</a></li>
                    <li '.esc_attr( $ac_pending ).'><a href="'.esc_url($pending).'" '.esc_attr($ac_pending).'>'.$houzez_local['pending'].'</a></li>
                    <li '.esc_attr( $ac_expired ).'><a href="'.esc_url($expired).'" '.esc_attr($ac_expired).'>'.$houzez_local['expired'].'</a></li>
                    <li '.esc_attr( $ac_draft ).'><a href="'.esc_url($draft).'" '.esc_attr($ac_draft).'>'.$houzez_local['draft'].'</a></li>
                    <li '.esc_attr( $ac_on_hold ).'><a href="'.esc_url($on_hold).'" '.esc_attr($ac_on_hold).'>'.$houzez_local['on_hold'].'</a></li>
                </ul>
                </li>';
            }
            if( !empty( $dashboard_add_listing ) && houzez_check_role() ) {
                echo '<li ' .esc_attr( $ac_add_prop ). '> <a href="' . esc_url($dashboard_add_listing) . '"><i class="fa fa-plus-circle"></i>' . esc_html__('Add new property', 'houzez') . '</a></li>';
            }
            if( !empty( $dashboard_favorites ) ) {
                echo '<li ' .esc_attr( $ac_fav ). '> <a href="' . esc_url($dashboard_favorites) . '"><i class="fa fa-heart"></i>' . esc_html__('Favourite properties', 'houzez') . '</a></li>';
            }
            if( !empty( $dashboard_search ) ) {
                echo '<li ' .esc_attr( $ac_search ). '> <a href="' . esc_url($dashboard_search) . '"><i class="fa fa-search-plus"></i>' . esc_html__('Saved Searches', 'houzez') . '</a></li>';
            }
            if( !empty( $dashboard_invoices ) && houzez_check_role() ) {
                echo '<li ' .esc_attr(  $ac_invoices ). '> <a href="' . esc_url($dashboard_invoices) . '"><i class="fa fa-file"></i>' . esc_html__('Invoices', 'houzez') . '</a></li>';
            }
            if( !empty($dashboard_msgs) ) {
                echo '<li ' . esc_attr($ac_msgs) . '> <a href="' . esc_url($dashboard_msgs) . '"> <i class="fa fa-comments-o"></i>' . esc_html__('Messages', 'houzez') . houzez_messages_notification() . '</a></li>';
            }
            if( !empty($dashboard_gdpr) ) {
                echo '<li ' . esc_attr($ac_gdpr) . '> <a href="' . esc_url($dashboard_gdpr) . '"> <i class="fa fa-envelope"></i>' . esc_html__('GDPR Data Request', 'houzez').'</a></li>';
            }
            if( !empty($dashboard_membership) && $enable_paid_submission == 'membership' && houzez_check_role() ) {
                echo '<li ' . esc_attr($ac_mem) . '> <a href="' . esc_url($dashboard_membership) . '"> <i class="fa fa-address-card-o"></i>' . esc_html__('Membership', 'houzez').'</a></li>';
            }

            echo '<li><a href="' . wp_logout_url(home_url('/')) . '"> <i class="fa fa-unlock"></i>' . esc_html__('Log out', 'houzez') . '</a></li>';
            ?>
        </ul>
    </div>
</div>
