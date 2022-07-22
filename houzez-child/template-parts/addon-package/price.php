<?php

$dashboard_package = houzez_get_template_link_2('template-user-dashboard-package.php');
$package = add_query_arg( array('option' => $_GET['option'], 'post' => $_GET['post']), $dashboard_package );

if (isset($_GET['state'])) {
    $value = explode(',', urldecode($_GET['state']));
    
    if ($value[0] == '750') {
        $addon = 'Featured:';
        $price = '€750';
    }

    if ($value[0] == '1000') {
        $addon = 'Property of the Week:';
        $price = '€1,000';
    }
}

if ($_GET['option'] == 'week') {
    $addon = 'Property of the Week:';
    $price = '€1,000';
}

if ($_GET['option'] == 'featured') {
    $addon = 'Featured:';
    $price = '€750';
}

?>
<h3 class="side-block-title"> <?php esc_html_e( 'Property Add On', 'houzez' ); ?> </h3>

<ul class="pkg-total-list">
    <li>
        <span id="houzez_package_name" class="pull-left"><?php echo $addon; ?></span>
        <span class="pull-right">
            <a href="<?php echo esc_url( $package ); ?>">
                <?php esc_html_e( 'Change Add On', 'houzez' ); ?>
            </a>
        </span>
    </li>
    <li>
        <span class="pull-left"><?php esc_html_e( 'Total Price:', 'houzez' ); ?></span>
        <span class="pull-right"><?php echo $price; ?></span>
    </li>
</ul>