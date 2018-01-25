<?php
/**
 * WC wcCpg1 Gateway Class.
 * Built the wcCpg1 method.
 */
class WC_Custom_Payment_Gateway_1 extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     *
     * @return void
     */
    public function __construct() {
        global $woocommerce;

        $this->id             = 'wccpg1';
        $this->icon           = apply_filters( 'woocommerce_wcCpg1_icon', '' );
        $this->has_fields     = false;
        $this->method_title   = __( 'Custom Payment Gateways 1', 'wcwcCpg1' );

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables.
        $this->title          = $this->settings['title'];
        $this->description    = $this->settings['description'];
		$this->instructions       = $this->get_option( 'instructions' );
		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );

        // Actions.
        if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) )
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
        else
            add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );


    }


    /* Admin Panel Options.*/
	function admin_options() {
		?>
		<h3><?php _e('Custom Payment Gateways 1','wcwcCpg1'); ?></h3>
    	<table class="form-table">
    		<?php $this->generate_settings_html(); ?>
		</table> <?php
    }

    /* Initialise Gateway Settings Form Fields. */
    public function init_form_fields() {
    	global $woocommerce;

    	$shipping_methods = array();

    	if ( is_admin() )
	    	foreach ( $woocommerce->shipping->load_shipping_methods() as $method ) {
		    	$shipping_methods[ $method->id ] = $method->get_title();
	    	}
			
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'wcwcCpg1' ),
                'type' => 'checkbox',
                'label' => __( 'Enable Custom Payment Gateways 1', 'wcwcCpg1' ),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __( 'Title', 'wcwcCpg1' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'wcwcCpg1' ),
                'desc_tip' => true,
                'default' => __( 'Custom Payment Gateways 1', 'wcwcCpg1' )
            ),
            'description' => array(
                'title' => __( 'Description', 'wcwcCpg1' ),
                'type' => 'textarea',
                'description' => __( 'This controls the description which the user sees during checkout.', 'wcwcCpg1' ),
                'default' => __( 'Desctiptions for Custom Payment Gateways 1.', 'wcwcCpg1' )
            ),
			'instructions' => array(
				'title' => __( 'Instructions', 'wcwcCpg1' ),
				'type' => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page.', 'wcwcCpg1' ),
				'default' => __( 'Instructions for Custom Payment Gateways 1.', 'wcwcCpg1' )
			),
			'enable_for_methods' => array(
				'title' 		=> __( 'Enable for shipping methods', 'wcwcCpg1' ),
				'type' 			=> 'multiselect',
				'class'			=> 'chosen_select',
				'css'			=> 'width: 450px;',
				'default' 		=> '',
				'description' 	=> __( 'If wcCpg1 is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'wcwcCpg1' ),
				'options'		=> $shipping_methods,
				'desc_tip'      => true,
			)
        );

    }




    /* Process the payment and return the result. */
	function process_payment ($order_id) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		// Mark as on-hold
		$order->update_status('on-hold', __( 'Your order wont be shipped until the funds have cleared in our account.', 'woocommerce' ));

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(woocommerce_get_page_id('thanks'))))
		);
	}


    /* Output for the order received page.   */
	function thankyou() {
		echo $this->instructions != '' ? wpautop( $this->instructions ) : '';
	}



}
