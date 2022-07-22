<?php
/**
 * Widget Name: Mortgage Calculator
 */

if(!class_exists('HOUZEZ_mortgage_calculator2')) {
    class HOUZEZ_mortgage_calculator2 extends WP_Widget {

        /**
         * Register widget
         **/
        public function __construct() {

            parent::__construct(
                'houzez_mortgage_calculator2', // Base ID
                esc_html__( 'HOUZEZ: Mortgage Calculator', 'houzez' ), // Name
                array( 'description' => esc_html__( 'Add a responsive mortgage calculator widget', 'houzez' ), 'classname' => 'widget-calculate' ) // Args
            );

        }


        /**
         * Front-end display of widget
         **/
        public function widget( $args, $instance ) {

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

            echo wp_kses( $before_widget, $allowed_html_array );

            if ( $title ) echo wp_kses( $before_title, $allowed_html_array ) . $title . wp_kses( $after_title, $allowed_html_array );

            houzez_mortgage_calculator_widget2();

            echo wp_kses( $after_widget, $allowed_html_array );

        }


        /**
         * Sanitize widget form values as they are saved
         **/
        public function update( $new_instance, $old_instance ) {

            $instance = array();

            /* Strip tags to remove HTML. For text inputs and textarea. */
            $instance['title'] = strip_tags( $new_instance['title'] );

            return $instance;

        }


        /**
         * Back-end widget form
         **/
        public function form( $instance ) {

            /* Default widget settings. */
            $defaults = array(
                'title' => 'Mortgage Calculator'
            );
            $instance = wp_parse_args( (array) $instance, $defaults );

            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'houzez'); ?></label>
                <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
            </p>

            <?php
        }

    }
}

if ( ! function_exists( 'HOUZEZ_mortgage_calculator_loader2' ) ) {
    function HOUZEZ_mortgage_calculator_loader2 (){
        register_widget( 'HOUZEZ_mortgage_calculator2' );
    }
    add_action( 'widgets_init', 'HOUZEZ_mortgage_calculator_loader2', 1 );
}

if( ! function_exists('houzez_mortgage_calculator_widget2') ) {
    function houzez_mortgage_calculator_widget2() {

        $currency_symbol = houzez_option('currency_symbol');
    ?>

        <div class="widget-body">
        <?php if (is_front_page()) { ?>
            <div class="row">
                <div class="col-md-6">
                    <label class="white" for="mc_total_amount"><?php esc_html_e('Total Amount', 'houzez'); ?></label>
                    <input class="form-control" id="mc_total_amount" type="text">
                </div>
                <div class="col-md-6">
                    <label class="white" for="mc_down_payment"><?php esc_html_e('Down Payment', 'houzez'); ?></label>
                    <input class="form-control" id="mc_down_payment" type="text">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="white" for="mc_interest_rate"><?php esc_html_e('Interest Rate', 'houzez'); ?></label>
                    <input class="form-control" id="mc_interest_rate" type="text">
                </div>
                <div class="col-md-6">
                    <label class="white" for="mc_term_years">
                        <?php esc_html_e('Loan Length (years)', 'houzez'); ?>
                    </label>
                    <input class="form-control" id="mc_term_years" type="text">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="white" for="mc_currency"><?php esc_html_e('Select Currency', 'houzez'); ?></label>
                    <select class="form-control" id="mc_currency">
                        <option value="eur">EUR</option>
                        <option value="usd">USD</option>
                        <option value="gbp">GBP</option>
                        <option value="btc">BTC</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="white" for="mc_payment_period">
                        <?php esc_html_e('Payment Frequency', 'houzez'); ?>
                    </label>
                    <select class="form-control" id="mc_payment_period">
                        <option value="12"><?php esc_html_e('Monthly', 'houzez'); ?></option>
                        <option value="26"><?php esc_html_e('Bi-Weekly', 'houzez'); ?></option>
                        <option value="52"><?php esc_html_e('Weekly', 'houzez'); ?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button id="cCalculate" class="btn btn-light btn-block">
                        <?php esc_html_e('Calculator', 'houzez');?>
                    </button>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-12">
                    <label class="white" for="mc_total_amount"><?php esc_html_e('Total Amount', 'houzez'); ?></label>
                    <input class="form-control" id="mc_total_amount" type="text">
                </div>
                <div class="col-md-12">
                    <label class="white" for="mc_down_payment"><?php esc_html_e('Down Payment', 'houzez'); ?></label>
                    <input class="form-control" id="mc_down_payment" type="text">
                </div>
                <div class="col-md-12">
                    <label class="white" for="mc_interest_rate"><?php esc_html_e('Interest Rate', 'houzez'); ?></label>
                    <input class="form-control" id="mc_interest_rate" type="text">
                </div>
                <div class="col-md-12">
                    <label class="white" for="mc_term_years">
                        <?php esc_html_e('Loan Length (years)', 'houzez'); ?>
                    </label>
                    <input class="form-control" id="mc_term_years" type="text">
                </div>
                <div class="col-md-12">
                    <label class="white" for="mc_currency"><?php esc_html_e('Select Currency', 'houzez'); ?></label>
                    <select class="form-control" id="mc_currency">
                        <option value="eur">EUR</option>
                        <option value="usd">USD</option>
                        <option value="gbp">GBP</option>
                        <option value="btc">BTC</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="white" for="mc_payment_period">
                        <?php esc_html_e('Payment Frequency', 'houzez'); ?>
                    </label>
                    <select class="form-control" id="mc_payment_period">
                        <option value="12"><?php esc_html_e('Monthly', 'houzez'); ?></option>
                        <option value="26"><?php esc_html_e('Bi-Weekly', 'houzez'); ?></option>
                        <option value="52"><?php esc_html_e('Weekly', 'houzez'); ?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button id="cCalculate" class="btn btn-primary btn-block">
                        <?php esc_html_e('Calculator', 'houzez');?>
                    </button>
                </div>
            </div>
        <?php } ?>

            <div class="morg-detail">
                <div class="morg-result">
                    <h2 id="mortgage_mwbi"></h2>
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icon_inspector.png" alt="icon inspector" class="show-morg">
                </div>
                <div class="morg-summery">
                    <div class="result-title">
                        <span><?php esc_html_e('Amount Financed:', 'houzez'); ?></span>
                        <span id="amount_financed"></span>
                    </div>

                    <div class="result-title">
                        <span><?php esc_html_e('Mortgage Payments:', 'houzez'); ?></span>
                        <span id="mortgage_pay"></span>
                    </div>

                    <div class="result-title">
                        <span><?php esc_html_e('Annual cost of Loan:', 'houzez'); ?></span>
                        <span id="annual_cost"></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}