<?php
/**
 * Template Name: User Dashboard Addtional Package
 */

if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

$dashboard_listings = houzez_get_template_link_2('template-user-dashboard-properties.php');
$listings = add_query_arg( 'prop_status', 'package', $dashboard_listings );

if ( !isset($_GET['option']) || $_GET['option'] == '' || 
	 !isset($_GET['post']) || $_GET['post'] == '' ||
	 ($_GET['option'] != 'featured' && $_GET['option'] != 'week')) {

	wp_redirect( $listings );
}

$dashboard_addon_payment = houzez_get_template_link_2('template-addon-payment.php');
$payment = add_query_arg( array('option' => $_GET['option'], 'post' => $_GET['post']), $dashboard_addon_payment );

global $wpdb;

$currency_code = get_post_meta( $_GET['post'], 'fave_currency', true);

$result = $wpdb->get_results(" SELECT currency_symbol FROM " . $wpdb->prefix . "houzez_currencies where currency_code='$currency_code'");

if (sizeof($result) > 0)
    $symbol = $result[0]->currency_symbol;
else
    $symbol = '€';

$sale_price = get_post_meta( $_GET['post'], 'fave_property_price', true );
$sale_price = number_format ( $sale_price , 0, '', ',' );

$status = get_the_terms( $_GET['post'], 'property_status' );

if ($status[0]->slug == 'for-rent')
    $price = $symbol . $sale_price . '/mo';
else
    $price = $symbol . $sale_price;

$size = get_post_meta( $_GET['post'], 'fave_property_size', true);
$type = get_the_terms( $_GET['post'], 'property_type' );

get_header();

get_template_part( 'template-parts/dashboard', 'menu' );
?>
<div class="user-dashboard-right dashboard-with-panel">

  <?php get_template_part( 'template-parts/dashboard-title' ); ?>

  <div class="dashboard-content-area">
    <div class="container">
    	<?php get_template_part('template-parts/add-package-option'); ?>

    	<div class="row">
    		<div class="my-property-listing my-package">
    			<div class="row grid-row">
    				<div class="item-wrap">
				    	<div class="media my-property">
					    	<h2 class="title">Listing</h2>
				        <div class="media-left">
			            <div class="figure-block">
			                <figure class="item-thumb">
		                	<?php
	                        if( has_post_thumbnail($_GET['post']) ) {
	                            echo get_the_post_thumbnail($_GET['post'], 'houzez-property-thumb-image');
	                        }else{
	                            houzez_image_placeholder('houzez-property-thumb-image');
	                        }
	                        ?>
			                </figure>
			            </div>
				        </div>
				        <div class="media-body">
			            <div class="my-description">
		                <h2 class="my-heading"><?php echo get_the_title($_GET['post']); ?></h2>
		                <p>
			                <?php echo wp_trim_words(get_post_field('post_content', $_GET['post']), '100'); ?>
			            	</p>
		                <div class="status">
	                    <p>
                        <span>
                        	<strong>Status:</strong>&nbsp;
                        	<?php
                        		if (sizeof($status) > 0)
                        			echo $status[0]->name;
                        	?>
                        </span>
                        <span>
                        	<strong>Price:</strong> <?php echo $price; ?>
                        </span>
                        <span><strong>sqft: </strong> <?php echo $size; ?></span>
                        <?php if (sizeof($type) > 0) { ?>
	                        <span><?php echo $type[0]->name; ?></span>
	                    <?php } ?>
	                    </p>
		                </div>
			            </div>
  							</div>

  							<div class="addon">
  								<h2 class="title">Select Add On</h2>
  								
  								<div>
    								<input type="radio" class="addon-type" name="addon-type" id="week" value="week" 
                        <?php if ($_GET['option'] == 'week') echo 'checked'; ?>>
    								<label for="week">Property of the Week</label>
    								<p>
    									Property of the Week is featured on the Homepage as well as sidebar positoning throughout the site. Users searching for propertes on aﬀordablemallorca.com will see Property of the Week listngs on search results pages and other views. Selected listngs will show randomly for 168 hours from the tme of purchase. Propertes are displayed at random based on individual user session.
    								</p>
    								<span>€1,000/week per listing</span>
  								</div>
  								<div>
    								<input type="radio" class="addon-type" name="addon-type" id="featured" value="featured"
                        <?php if ($_GET['option'] == 'featured') echo 'checked'; ?>>
    								<label for="featured">Featured Listing</label>
    								<p>
    									Agencies and Agent Users should be able to Purchase Featured Property Listngs – A Featured Property shows in all featured property sectons at random as well as on Search results pages to match search. Order is random on search results pages when more than one Featured property are a match. Limit 2 per sidebar. Selected listngs will show randomly for 168 hours from the tme of purchase. Propertes are displayed at random based on individual user session.
    								</p>
    								<span>€750/week per listing</span>
  								</div>
  							</div>
							</div>
						</div>
						<div class="step-bar">
	            <div class="btn-left btn-back action">
	                <a class="btn" href="<?php echo esc_url($listings); ?>"><i class="fa fa-angle-left"></i></a>
	                <span>Back</span>
	            </div>
	            <div class="btn-right btn-next action">
	                <span>Next</span>
	                <a class="btn" href="<?php echo esc_url($payment); ?>"><i class="fa fa-angle-right"></i></a>
	            </div>
	          </div>
    			</div>
    		</div>
    	</div>
    </div>
  </div>

</div>

<?php get_footer(); ?>