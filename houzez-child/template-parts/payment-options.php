<?php

$selected_package_id = $_GET['selected_package'];

$option = (isset($_GET['option'])) ? $_GET['option'] : 'option1';

if (isset($_GET['state'])) {
	$value = explode(',', urldecode($_GET['state']));

	$option = $value[0];
	$selected_package_id = $value[1];
}

$payment1 = get_post_meta( $selected_package_id, 'fave_payment_option1', true );
$payment2 = get_post_meta( $selected_package_id, 'fave_payment_option2', true );
$payment3 = get_post_meta( $selected_package_id, 'fave_payment_option3', true );
$payment4 = get_post_meta( $selected_package_id, 'fave_payment_option4', true );

?>

<div class="info-title">
    <h2 class="info-title-left"> <?php echo esc_html('Select Payment Option', 'houzez'); ?> </h2>
</div>

<div class="option-select-block">
	<div class="radio">
	    <input type="radio" class="payment_option" name="payment_option" id="option1" value="option1" 
	    	<?php if ($option == 'option1') echo 'checked'; ?>/>
	    <label for="option1">One-Time(60 Days, €<?php echo $payment1; ?>)</label>
	</div>

	<div class="radio">
	    <input type="radio" class="payment_option" name="payment_option" id="option2" value="option2" 
	    	<?php if ($option == 'option2') echo 'checked'; ?>/>
	    <label for="option2">Monthly (€<?php echo $payment2; ?>) on a recurring basis</label>
	</div>

	<div class="radio">
	    <input type="radio" class="payment_option" name="payment_option" id="option3" value="option3" 
	    	<?php if ($option == 'option3') echo 'checked'; ?>/>
	    <label for="option3">Quarterly (€<?php echo $payment3; ?>) on a recurring basis</label>
	</div>

	<div class="radio">
	    <input type="radio" class="payment_option" name="payment_option" id="option4" value="option4" 
	    	<?php if ($option == 'option4') echo 'checked'; ?>/>
	    <label for="option4">Semi Annually (€<?php echo $payment4; ?>) on a recurring basis</label>
	</div>
</div>