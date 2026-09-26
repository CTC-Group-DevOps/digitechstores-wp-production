<?php


    /**
     * SyberPay Payment Gateway Class
     */
    class WC_SyberPay_Gateway extends WC_Payment_Gateway {
        
        /** @var boolean Whether or not logging is enabled */
        public static $log_enabled = true;
        /** @var WC_Logger Logger instance */
        public static $log = false;

        // Setup our Gateway's id, description and other values
        function __construct() {
     
            // The global ID for this Payment method
            $this->id = "syberpay";
 
            // The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
            $this->method_title = __( "Syberpay", 'SyberPay' );
 
            // The description for this Payment Gateway, shown on the actual Payment options page on the backend
            $this->method_description = __( "SyberPay Payment Gateway allows you to use your bank Physical/Virtual Card in Sudan for your Woocommerce Powered Site.", 'SyberPay' );
 
            // The title to be used for the vertical tabs that can be ordered top to bottom
            $this->title = __( "SyberPay", 'Syberpay' );
 
            // If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
            $this->icon = plugins_url( 'assets/syber-icon.png' , __FILE__ );
 
            // Bool. Can be set to true if you want payment fields to show on the checkout 
            // if doing a direct integration, which we are not doing in this case
            $this->has_fields = false;
 
            //The URL for SyberPay payment webservice
            
            //$this->payment_url = 'http://localhost:8880/SyberPay/syberpay/getUrl';
            $this->notify_url = WC()->api_request_url( 'WC_SyberPay_Gateway_notification' );
            $this->redirect_url = WC()->api_request_url( 'WC_SyberPay_Gateway_redirection' );

            $this->credit_url = WC()->api_request_url( 'wc_syberpay_credit' );

            // Supports the default credit card form
            //$this->supports = array( 'default_credit_card_form' );

            $this -> msg['message'] = "";
            $this -> msg['class']   = "";
            
            // Define user set variables 
            $this->payment_url =$this->get_option( 'geturlapi' ); // 'http://41.78.108.194:9999/SyberPay/syberpay/getUrl';
            // The URL for SyberPay Payment status (payment transaction status )
            $this->payment_status_url = $this->get_option( 'getstatusapi' );  //'http://41.78.108.194:9999/SyberPay/syberpay/payment_status';
			$this->title 				= $this->get_option( 'title' );
			$this->description 			= $this->get_option( 'description' );
			$this->applicationId 		= $this->get_option( 'applicationId' );
			$this->serviceId 			= $this->get_option( 'serviceId' );
            $this->key 			        = $this->get_option( 'key' );
            $this->salt 			    = $this->get_option( 'salt' );
 
            // This basically defines your settings which are then loaded with init_settings()
            $this->init_form_fields();
 
            // After init_settings() is called, you can get the settings and load them into variables, e.g:
            // $this->title = $this->get_option( 'title' );
            $this->init_settings();
     
            // Turn these settings into variables we can use
            foreach ( $this->settings as $setting_key => $value ) {
                $this->$setting_key = $value;
            }
     
            // Lets check for SSL
            //add_action( 'admin_notices', array( $this,  'do_ssl_check' ) );
     
            // Save settings
            if ( is_admin() ) {
                // Versions over 2.0
                // Save our administration options. Since we are not going to be doing anything special
                // we have not defined 'process_admin_options' in this class so the method in the parent
                // class will be used instead
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            }
            
            // Check if the gateway can be used
			if ( ! $this->is_valid_for_use() ) {
				$this->enabled = false;
			}
            $this->log('entering overrriding function ..........');
            remove_filter('get_header', 'wc_clear_cart_after_payment' );
            add_action('get_header', array( $this,'st_wc_clear_cart_after_payment') );
            add_action( 'woocommerce_cart_emptied',array($this, 'cart_emptied' ));

            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
            //add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));  
            // finial redirection from syberPay client just contains the customerRef
            // api : site_url/wc-api/woocommerce_api_wc-syberpay
            
            add_action( 'woocommerce_api_wc_syberpay_gateway', array( $this, 'syberpay_api_check' ) ); 
            //request from syberpay server with transaction id to be called back to get payment data 
    
            add_action( 'woocommerce_api_wc_syberpay_credit', array( $this, 'check_syberpay_credit' ) );     

            add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'thankyou_page_text' ), 10, 2 );
            
            //filter to mark virtual orders as completed
            add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'virtual_order_payment_complete_order_status'), 10, 2 );

        //    $this->log('urls : ' . $this->payment_url . ' , ' . $this->notify_url . ' , ' . $this->credit_url);
        } // End __construct()
//         function override_wc_clear_cart_after_payment() {
           
// }
function st_wc_clear_cart_after_payment( $methods ) {
    global $wp, $woocommerce;

    if ( ! empty( $wp->query_vars['order-received'] ) ) {
        $order_id = absint( $wp->query_vars['order-received'] );

        if ( isset( $_GET['key'] ) ){
            $order_key = $_GET['key'];
        }
        else
            $order_key = '';

        if ( $order_id > 0 ) {
            $order = wc_get_order( $order_id );

            if ( $order->order_key == $order_key ) {
        
             //  WC()->cart->empty_cart();
            }
        }

    }

    if ( WC()->session->order_awaiting_payment > 0 ) {

        $order = wc_get_order( WC()->session->order_awaiting_payment );

        if ( $order->id > 0 ) {
            // If the order has not failed, or is not pending, the order must have gone through
            if ( ! $order->has_status( array( 'failed', 'pending','pending-st-cleared-funds','on-hold','canceled' ) ) ) { ///// <- add your custom status here....
                WC()->cart->empty_cart();
            }
         }
    }
}

      
        /**
	     * Check if this gateway is enabled and available in the user's country
	     *
	     * @return bool
	     */
        function is_valid_for_use(){

			if( ! in_array( get_woocommerce_currency(), array('SDG') ) ){
				$this->msg = 'Syberpay doesn\'t support your store currency, set it to Sudanese Pound <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=general">here</a>';
                $this->log($this->msg);
				return false;
			}

			return true;
		}

        /**
	     * Initialise Gateway Administration Settings Form Fields
	    **/
		function init_form_fields(){
			$this->form_fields = array(
				'enabled' => array(
					'title' 		=> 'Enable/Disable',
					'type' 			=> 'checkbox',
					'label' 		=>'Enable SyberPay Payment Gateway',
					'description' 	=> 'Enable or disable the gateway.',
            		'desc_tip'      => true,
					'default' 		=> 'yes'
				),
				'title' => array(
					'title' 		=> 'Title',
					'type' 			=> 'text',
					'description' 	=> 'This controls the title which the user sees during checkout.',
        			'desc_tip'      => false,
					'default' 		=> 'SyberPay'
				),
				'description' => array(
					'title' 		=> 'Description',
					'type' 			=> 'textarea',
					'description' 	=> 'This controls the description which the user sees during checkout.',
					'default' 		=> 'Pay securely Via SyberPay in Sudan: Use your bank physical or virtual cards.'
				),
				'applicationId' => array(
					'title' 		=> 'SyberPay Application ID',
					'type' 			=> 'text',
					'description' 	=> 'Enter Your SyberPay Application ID, this is given to your site page by SyberPay.' ,
					'default' 		=> '',
        			'desc_tip'      => true
				),
				'serviceId' => array(
					'title' 		=> 'Service ID',
					'type' 			=> 'text',
					'description' 	=> 'Enter Your Service ID here, this is given to your site page by SyberPay.' ,
					'default' 		=> '',
        			'desc_tip'      => true
				),
                'key' => array(
					'title' 		=> 'Application Key',
					'type' 			=> 'text',
					'description' 	=> 'Enter Your Application Key here, this is given to your site page by SyberPay.' ,
					'default' 		=> '',
        			'desc_tip'      => true
				),
                'salt' => array(
					'title' 		=> 'Application Salt',
					'type' 			=> 'text',
					'description' 	=> 'Enter Your Application Salt here, this is given to your site page by SyberPay.' ,
					'default' 		=> '',
        			'desc_tip'      => true
				),
					'geturlapi' => array(
							'title' 		=> 'Syber Pay Get Payment URL API URL',
							'type' 			=> 'text',
							'description' 	=> 'Enter  Syber Pay get payment URL API URL.' ,
							'default' 		=> '',
							'desc_tip'      => true
					),
					'getstatusapi' => array(
							'title' 		=> 'Syber Pay Get Payment Status API URL',
							'type' 			=> 'text',
							'description' 	=> 'Enter Syber Pay Get Payment Status API URL.' ,
							'default' 		=> '',
							'desc_tip'      => true
					),
                'environment' => array(
                    'title'     =>  'SyberPay Test Mode',
                    'label'     =>  'Enable Test Mode',
                    'type'      => 'checkbox',
                    'description' => 'Place the payment gateway in test mode.', 
                    'default'     => 'no',
                )
			);
		} // End of init_form_fields

       	/**
	     * Logging method
	     * @param  string $message
	     */
	    public static function log( $message ) {
		    if ( self::$log_enabled ) {
			    if ( empty( self::$log ) ) {
				    self::$log = new WC_Logger();
			    }
			    self::$log->add( 'syberpay', $message );
		    }
	    }

        /**
	     * Process the payment and return the result
	    **/
		function process_payment( $order_id ) {

			$order = wc_get_order( $order_id );
            $this->log('checkout process payment for order ' . $order_id);

	        return array(
	        	'result' 	=> 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
	        );
		}

        /**
	     * Output for the pay page.
	    **/
		function receipt_page( $order_id ) {
            $this->log('receipt page for order ' . $order_id);

			echo '<p>Thank you - your order is now pending payment. You should be automatically redirected to the gateway to make payment</p>';

			echo $this->generate_syberpay_form( $order_id );

            //$redirectUrl = 'http://www.google.com';
            //header('Location:'.$redirectUrl);
		}

        /**
	     * Output for the thank you (order received) page.
	    **/
		function thankyou_page($order_id) {
            $this->log('thankyou page for order ' . $order_id);

			echo '<p>Thank you for shopping with us - your order payment is completted</p>';
            //wp_redirect( "http://www.google.com" ); exit; // or whatever url you want
   
            //echo $this->generate_syberpay_form( $order );

            //$redirectUrl = 'http://www.google.com';
            //header('Location:'.$redirectUrl);
		}

        function thankyou_page_text($message, $order) {
       
            $this->log('thankyou page text for order ');
            
            if($order) {
                $order_status  = $order->get_status();
                if($order_status== 'pending'){
                echo '<p><h4 style="color: green">Thank you for shopping with us - your order payment is now '.$order_status.'!</h4></p><p>Please enter your order number in the designated area in your Mbok app to complete your payment. </p><p>To know how to pay via Mbok app, please follow the below steps:</p><p>1. Go to "Bill payments" in Mbok app home.</br>2. Choose "Onine Shopping"</br>3. Choose "Digitech"</br>4. Enter your order number and press "submit"</br>5. Confirm your order amount and press "Confirm"</p><p>For more information please call 0123196461/0123796462 or call our hotline 5454</p>';
                }
                else{
                  echo '<p><h4 style="color: green">Thank you for shopping with us - your order payment is now '.$order_status.'!</h4></p>';
                }
            } else {
                echo '<p><h4 style="color: red">Thank you for shopping with us - However there is an error retrieving your order!</h4></p>';
            }
            
			//echo '<p>Thank you for shopping with us - your order payment is completted</p>';
            //wp_redirect( "http://www.google.com" ); exit; // or whatever url you want
		}

        /**
        * Handle SyberPay credit request 
        **/
        function check_syberpay_credit() {
            $this->log('handle syperpay credit request.');
            
            global $HTTP_RAW_POST_DATA;
		    // A bug in PHP < 5.2.2 makes $HTTP_RAW_POST_DATA not set by default,
		    // but we can do it ourself.
		    if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
			    $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		    }

            $json_body = json_decode( $HTTP_RAW_POST_DATA, true );
            $this->log('Json body : ' . $json_body);


        }
        function syberpay_api_check(){
 $this->log('entering syberpay_api_check .......');
                    if ( empty( $_POST )  ) {
                    $message =  'Thank you for shopping with us. <br />However, the transaction wasn\'t successful, no parameters were returned.';
                    $message_type = 'error';
                    $msg['message'] = $message;
                    $msg['class'] = $message_type;
                    
                    $this->log('callback failed response is empty .....1');
                    //$this->payment_status_failed();????????????????????????
                    exit;    
                 } elseif (!empty($_POST['transactionId']) ) {
                    $this->log('transactioId founded .......'.$_POST['transactionId'] );
                    $this->syberpay_notification_handler();
                 }elseif (!empty($_POST['customerRef']) ) {
                 $this->log('customerRef founded .......'.$_POST['customerRef'] );

                    $this->syberpay_redirection_handler();
                 } else{
                    $message =  'Thank you for shopping with us. <br />However, the transaction wasn\'t successful, no parameters were returned.';
                    $message_type = 'error';
                    $msg['message'] = $message;
                    $msg['class'] = $message_type;
                    
                    $this->log('callback failed response is empty .....2');
                    //$this->payment_status_failed();????????????????????????
                    exit;    
                 }

        }
        /**
         * call  Syberpay status api update order status!
        **/
        function syberpay_notification_handler(){
             
            $this->log('syberpay_notification_handler .......' );

            $msg = array();
            try {

                 //check if the customerRef (order_id) & response status are posted back
                
            
                $posted = wp_unslash( $_POST );
                //echo $posted;

              
                $transaction_id = $_POST['transactionId'];
            $this->log('syberpay_notification_handler .......'.$transaction_id );
          $this->log('getting payment status args ...'.$transaction_id );
            try{


                $payment_status_args =$this->get_syberpay_payment_status_args($transaction_id);
            }catch(Exception $ex){
                $this->log('getting payment status args' .$ex);
            }
                          $this->log('getting payment status args' .$payment_status_args);

                $payment_status = $this->getPaymentStatus($payment_status_args);
                $this->log('done getting data from syberpay .......' );
                $r_status = $payment_status['status'];
                $r_code = $payment_status['responseCode'];
                $r_message = $payment_status['responseMessage'];
                $payment = $payment_status['payment'];
                $payment_r_status =$payment['status'];
                $payment_r_code =$payment['responseCode'];
                $payment_r_message = $payment['responseMessage'];
                $payment_r_customerRef = $payment['customerRef'];
                $posted['customerRef']=$payment_r_customerRef;
                echo $r_status . ' ' . $r_code . ' ' . $r_message . ' ' . $customerRef . ' ' . $transaction_id;
                $this->log('callback parms ' . $payment_r_status . ' ' . $payment_r_code . ' ' . $payment_r_message . ' ' . $payment_r_customerRef . ' ' . $transaction_id);
                
                //return the order
                $order = wc_get_order( $payment_r_customerRef );
                
                if ( ! ( $order ) ) {
                    $this->log('callback ' . $r_status . ' order ' . $customerRef);
                    //$this->payment_status_failed();????????????????????????
                    exit;
                }
                
                 

                if($payment_r_status == 'Successful' && $payment_r_code == 0) {
                     // Payment has been successful   
                     $this->log('callback ' . $payment_r_status);

                     $this->payment_status_successfull( $order, $posted );
                     
            
                     
                  } else if($payment_r_status == 'Failed') {
                            //transaction status is failed
                            $this->log('callback ' . $payment_r_status);

                            $this->payment_status_failed( $order, $posted );

                        
                    
                   } else if($payment_r_status == 'Canceled') {
                            //transaction status is canceled by user
                            $this->log('callback ' . $payment_r_status);

                            $this->payment_status_canceled( $order, $posted );

                           
                   }
                   else {
                        //Error: response status has invalid value
                        $this->log('callback ' . $payment_r_status);
                        $this->payment_status_failed( $order, $posted );
                   }
                
            } catch(Exception $e){
                $this->log('callback exception' . $e);
                
            }


        }

        /**
		 * Check Syberpay client Refrence and display thank you message
		**/
		function syberpay_redirection_handler() {
            $this->log(' syberpay_redirection_handler....... ');

            $msg = array();
            try {

                 //check if the customerRef (order_id) & response status are posted back
                
			
                $posted = wp_unslash( $_POST );
                //echo $posted;
                $customerRef = $_POST['customerRef'];
                $order = wc_get_order( $customerRef );
                $r_status = $order->get_status();
               
               
                

                echo $r_status . ' ' . $customerRef ;
                $this->log('callback parms ' . $r_status . ' ' . $customerRef );
                
                //return the order
                
                if ( ! ( $order ) ) {
                    $this->log('callback ' . $r_status . ' order ' . $customerRef);
                    //$this->payment_status_failed();????????????????????????
			        exit;
		        }
                
                 
                //should validate order with posted parameters
                //validateReturnedOrder($order, $posted);

                //after payment hook
                //do_action( 'tbz_wc_voguepay_after_payment', $transaction );
                     $this->log('order status ===== ' . $r_status);

                if($r_status == 'processing' || $r_status == 'completed' ) {
                     // Payment has been successful   
                     $this->log('callback transaction successful ' . $r_status);

                //     $this->payment_status_successfull( $order, $posted );
                     
                     $message = 'Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.';
					 $message_type = 'success';   
                     $msg['message'] = $message;
                     $msg['class'] = $message_type;
                     
                   
                     
                  } else if($r_status == 'failed') {
                            //transaction status is failed
                            $this->log('callback transaction failed ' . $r_status);

                   //         $this->payment_status_failed( $order, $posted );

                            $message = 	'Thank you for shopping with us. <br />However, the transaction has failed.' . $r_message;
					        $message_type = 'error';
                            $msg['message'] = $message;
                            $msg['class'] = $message_type;

                            
                    
                   } else if($r_status == 'canceled'||$r_status=='pending') {
                            //transaction status is canceled by user
                            $this->log('callback transaction canceled ' . $r_status);

                     //       $this->payment_status_canceled( $order, $posted );

                            $message = 	'You have decided to cancel the payment process. <br />However, you can still continue with your order.';
					        $message_type = 'notice';
                            $msg['message'] = $message;
                            $msg['class'] = $message_type;
                   }
                   else {
                        //Error: response status has invalid value
                        $this->log('callback ' . $r_status);
                       // $this->payment_status_failed( $order, $posted );
                   }
                
            } catch(Exception $e){
                $this->log('callback exception' . $e);
                $message = 	'Thank you for shopping with us. However, the transaction has been failed.';
			    $message_type = 'error';
                $msg['class'] = $message_type;
                $msg['message'] = $message;

            }

            if ($r_status == 'canceled'||$r_status=='pending') {
                                $redirect_url = $order->get_cancel_order_url();

                $this->log('redirecting to cancelling order page... ' .$redirect_url);
                // Redirect to cancel page
                $redirect_url = $order->get_cancel_order_url();
            } else {
                $redirect_url = $order->get_checkout_order_received_url();
              $this->log('redirecting to thankyou page... '.$redirect_url );
                // Redirect to thank you page
                $redirect_url = $order->get_checkout_order_received_url();
            }
            
            wp_redirect( $redirect_url );
         	
            //$redirectUrl = 'http://www.google.com';
            //header('Location:'.$redirectUrl);
            exit;
            
            //die( 'IPN Processed OK' );
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /**
        * Mark orders with all vritual items as completed!
        **/
        function virtual_order_payment_complete_order_status( $order_status, $order_id ) {
              $this->log('virtual order can be marked completed ' . $order_id);
              $order = new WC_Order( $order_id );
                
              if ( 'processing' == $order_status &&
                   ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {
 
                $virtual_order = null;
 
                if ( count( $order->get_items() ) > 0 ) {
 
                  foreach( $order->get_items() as $item ) {
 
                    if ( 'line_item' == $item['type'] ) {
 
                      $_product = $order->get_product_from_item( $item );
 
                      if ( ! $_product->is_virtual() ) {
                        // once we've found one non-virtual product we know we're done, break out of the loop
                        $virtual_order = false;
                        break;
                      } else {
                        $virtual_order = true;
                      }
                    }
                  }
                }
 
                // virtual order, mark as completed
                if ( $virtual_order ) {
                    $this->log('virtual order has been marked completed ' . $order_id);
                  return 'completed';
                }
              }
 
              // non-virtual order, return original status
              return $order_status;
        }

        /**
	     * Complete order, add transaction ID and note
	     * @param  WC_Order $order
	     * @param  string $txn_id
	     * @param  string $note
	     */
	    function payment_complete( $order, $txn_id = '', $note = '' ) {
		    $this->log('callback payment complete.');
            $order->add_order_note( $note );
		    $order->payment_complete( $txn_id );

            //$order->add_order_note( __( 'SyberPay payment completed.', 'syberpay' ) );         
            // Mark order as Paid
            //$order->payment_complete();
            // Empty the cart (Very important step)
            //$woocommerce->cart->empty_cart();
	    }

	    /**
	     * Hold order and add note
	     * @param  WC_Order $order
	     * @param  string $reason
	     */
	    function payment_on_hold( $order, $reason = '' ) {
		    $order->update_status( 'on-hold', $reason );
		    $order->reduce_order_stock();
		    WC()->cart->empty_cart();
	    }

        /**
	     * Cancel order and add note
	     * @param  WC_Order $order
	     * @param  string $reason
	     */
	    function payment_cancel( $order, $reason = '' ) {
		    $this->log('callback payment cancel.');
            $order->update_status( 'cancelled', $reason );
		    $order->cancel_order($reason);
            //$order->reduce_order_stock();
		    //WC()->cart->empty_cart();
	    }

        /**
	     * Cancel order and add note
	     * @param  WC_Order $order
	     * @param  string $reason
	     */
	    function payment_fail( $order, $reason = '' ) {
		    $this->log('callback payment fail.');
            $order->update_status( 'failed', $reason );
            //Add Customer Order Note
            //$order->add_order_note($reason.'<br />SyberPay Transaction ID: '.$transaction_id, 1);
		    //$order->reduce_order_stock();
		    //WC()->cart->empty_cart();

            // Add notice to the cart
             //wc_add_notice( $r_message, 'error' );
             // Add note to the order for your reference
             //$order->add_order_note( 'Error: '. $r_message );
	    }

        /**
	     * Handle a completed payment
	     * @param  WC_Order $order
	     */
	    function payment_status_successfull( $order, $posted ) {
            $this->log('callback payment status successful.');
		    if ( $order->has_status( 'completed' ) || $order->has_status( 'processing' )  ) {
			    exit;
		    }

            $this->payment_complete( $order, ( ! empty( $posted['customerRef'] ) ? wc_clean( $posted['customerRef'] ) : '' ), __( 'SyberPay payment completed successfully. <br />SyberPay Transaction ID: '.$posted['transactionId'], 'woocommerce' ) );
            /*
            if( $order->status == 'processing' ) {
			      $order->add_order_note('SyberPay Payment Received<br />SyberPay Transaction ID: '.$transaction_id);

			      //Add customer order note
			 	  $order->add_order_note('SyberPay Payment Received.<br />Your order is currently being processed.<br />We will be shipping your order to you soon.<br /> SyberPay Transaction ID: '.$transaction_id, 1);

				// Reduce stock levels
		    	$order->reduce_order_stock();

					// Empty cart
			    	wc_empty_cart();

							$message = 'Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.';
							$message_type = 'success';
			         } else {

			                if( $order->has_downloadable_item() ) {

			                		    //Update order status
									    $order->update_status( 'completed', 'Payment received, your order is now complete.' );

				                        //Add admin order note
				                        $order->add_order_note('Payment Via SyberPay Payment Gateway<br />Syberpay Transaction ID: '.$transaction_id);

				                        //Add customer order note
				 					    $order->add_order_note('Payment Received.<br />Your order is now complete.<br />Syberpay Transaction ID: '.$transaction_id, 1);

									    $message = 'Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is now complete.';
									    $message_type = 'success';

			                } 
                            else {

			                		    //Update order status
									    $order->update_status( 'processing', 'Payment received, your order is currently being processed.' );

									    //Add admin order noote
				                        $order->add_order_note('Payment Via Syberpay Payment Gateway<br />Syberpay Transaction ID: '.$transaction_id);

				                        //Add customer order note
				 					    $order->add_order_note('Payment Received.<br />Your order is currently being processed.<br />We will be shipping your order to you soon.<br />Syberpay Transaction ID: '.$transaction_id, 1);

									    $message = 'Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.';
									    $message_type = 'success';
			               }

							// Reduce stock levels
							$order->reduce_order_stock();

							// Empty cart
							wc_empty_cart();
			           }
            
           
            }
            */
	    }

        /**
	     * Handle a failed payment
	     * @param  WC_Order $order
	     */
	    function payment_status_failed( $order, $posted ) {
		    $this->log('callback payment status failed.');
            //$order->update_status( 'failed', sprintf( __( 'Payment %s via IPN.', 'woocommerce' ), wc_clean( $posted['payment_status'] ) ) );
            $this->payment_fail($order, __( 'SyberPay payment failed. <br />SyberPay Transaction ID: '.$posted['transactionId'], 'woocommerce' ) );
	    }
	    
        /**
	     * Handle a canceled payment
	     * @param  WC_Order $order
	     */
	    function payment_status_canceled( $order, $posted ) {
		    $this->log('callback payment status canceled.');
            //$order->update_status( 'failed', sprintf( __( 'Payment %s via IPN.', 'woocommerce' ), wc_clean( $posted['payment_status'] ) ) );
            $this->payment_cancel($order, __( 'SyberPay payment canceled by user. <br />SyberPay Transaction ID: '.$posted['transactionId'], 'woocommerce' ) );
	    }

        /**
	     * Handle a pending payment
	     * @param  WC_Order $order
	     */
	    function payment_status_pending( $order, $posted ) {
		    $this->payment_status_completed( $order, $posted );
	    }
	    
        
	    /**
	     * Handle a denied payment
	     * @param  WC_Order $order
	     */
	    function payment_status_denied( $order, $posted ) {
		    $this->payment_status_failed( $order, $posted );
	    }
	    /**
	     * Handle an expired payment
	     * @param  WC_Order $order
	     */
	    function payment_status_expired( $order, $posted ) {
		    $this->payment_status_failed( $order, $posted );
	    }
	    /**
	     * Handle a voided payment
	     * @param  WC_Order $order
	     */
	    function payment_status_voided( $order, $posted ) {
		    $this->payment_status_failed( $order, $posted );
	    }


        /**
	     * Save important data from the IPN to the order
	     * @param WC_Order $order
	     */
	    function save_paypal_meta_data( $order, $posted ) {
		    if ( ! empty( $posted['payer_email'] ) ) {
			    update_post_meta( $order->id, 'Payer PayPal address', wc_clean( $posted['payer_email'] ) );
		    }
		    if ( ! empty( $posted['first_name'] ) ) {
			    update_post_meta( $order->id, 'Payer first name', wc_clean( $posted['first_name'] ) );
		    }
		    if ( ! empty( $posted['last_name'] ) ) {
			    update_post_meta( $order->id, 'Payer last name', wc_clean( $posted['last_name'] ) );
		    }
		    if ( ! empty( $posted['payment_type'] ) ) {
			    update_post_meta( $order->id, 'Payment type', wc_clean( $posted['payment_type'] ) );
		    }
	    }

	    /**
	     * Send a notification to the user handling orders.
	     * @param  string $subject
	     * @param  string $message
	     */
	    function send_ipn_email_notification( $subject, $message ) {
		    $new_order_settings = get_option( 'woocommerce_new_order_settings', array() );
		    $mailer             = WC()->mailer();
		    $message            = $mailer->wrap_message( $subject, $message );
		    $mailer->send( ! empty( $new_order_settings['recipient'] ) ? $new_order_settings['recipient'] : get_option( 'admin_email' ), $subject, $message );
	    }

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /**
		 * Generate the SyberPay Payment button link
	    **/
        function generate_syberpay_form( $order_id ) {
            
			$order = wc_get_order( $order_id );
            $this->log('cancel url: ' . $order->get_cancel_order_url_raw() . ' ,' . $order->get_cancel_order_url() . 
            ' , checkout received: ' . $order->get_checkout_order_received_url()  . ' , checkout payment: ' . $order->get_checkout_payment_url(true));
			$syberpay_args = $this->get_syberpay_args( $order );

			$syberpay_args_array = array();

			foreach ($syberpay_args as $key => $value) {
				$syberpay_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
			}

            //$redirectUrl = $this->getPaymentUrl($syberpay_args);

            $api_result = $this->callRemoteUrl($syberpay_args);
            $redirectUrl = $api_result['url'] && $api_result['url'] != '' ? $api_result['url'] : $order->get_checkout_payment_url();
            
            //echo $redirectUrl;
            //echo WC()->api_request_url( 'WC_Tbz_Voguepay_Gateway' );         
            //echo str_replace( 'https:', 'http:', home_url( '/wc-api/WC_Mrova_Ccave' )  );

			wc_enqueue_js( '
				$.blockUI({
						message: "' . esc_js( __( $api_result['message'], 'woocommerce' ) ) . '",
						baseZ: 99999,
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css: {
							padding:        "20px",
							zindex:         "9999999",
							textAlign:      "center",
							color:          "#555",
							border:         "3px solid #aaa",
							backgroundColor:"#fff",
							cursor:         "wait",
							lineHeight:		"24px",
						}
					});
                    setTimeout(
                      function() 
                      {
                        //do something special
                        jQuery("#submit_syberpay_payment_form").click();
                      }, 5000);
				
			' );

			return '<form action="' . $api_result['url'] . '" method="get" id="syberpay_payment_form" target="_top">
					
					<!-- Button Fallback -->
					<div class="payment_buttons">
						<input type="submit" class="button alt" id="submit_syberpay_payment_form" value="Make Payment" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">Cancel order &amp; restore cart</a>
					</div>
					<script type="text/javascript">
						jQuery(".payment_buttons").hide();
					</script>
				</form>';
		}

        /**
		 * Get SyberPay Args for passing to Syberpay
		**/
		function get_syberpay_args( $order ) {

			$order_id 		= $order->id;

			$order_total	= $order->get_total();
            $currency       = get_woocommerce_currency();
			$applicationId 	= $this->applicationId;
            $serviceId 	    = $this->serviceId;
			$memo        	= "Payment for Order ID: $order_id on ". get_bloginfo('name');
            $notify_url  	= $this->notify_url;
			$success_url  	= $this->get_return_url( $order );
			$fail_url	  	= $this->get_return_url( $order );
			$store_id 		= $this->storeId  ? $this->storeId : '';
            $email          = $order->billing_email ? $order->billing_email : '';
            $customerName   = $order->billing_first_name ? $order->billing_first_name : '';
            //$payment_info   = array("customerName" => $customerName , "email" => $email ,"orderNo" => $order_id);
            $payment_info   = array("customerName" => $customerName);

            
			// syberpay Args (need hash & paymentInfo ???????????????????????)
			$syberpay_args = array(
				'applicationId' 		=> $applicationId,
                'serviceId' 		    => $serviceId,
				'amount' 				=> $order_total,
                'currency' 				=> $currency,
				'customerRef'			=> $order_id,
                'paymentInfo'			=> $payment_info
			);

            $hash = $this->generateHash($syberpay_args);
            $syberpay_args['hash'] = $hash;

			$syberpay_args = apply_filters( 'woocommerce_syberpay_args', $syberpay_args );
			return $syberpay_args;
		}

        /**
         * Get SyberPay Args for passing to Syberpay
        **/
        function get_syberpay_payment_status_args( $transaction_id ) {

           $this->log('entering get_syberpay_payment_status_args ....');
            $applicationId  = $this->applicationId;
            

            
            // syberpay Args (need hash & paymentInfo ???????????????????????)
            $syberpay_args = array(
                'applicationId'         => $applicationId,
                'transactionId'             => $transaction_id
            );

            $hash = $this->generatePaymentStatusHash($transaction_id);
            $syberpay_args['hash'] = $hash;

            $syberpay_args = apply_filters( 'woocommerce_syberpay_args', $syberpay_args );
                       $this->log(' get_syberpay_payment_status_args ===....'.$syberpay_args);

            return $syberpay_args;
        }
              /**
        * Generate Message SHA256 hash
        **/
        function generatePaymentStatusHash($transaction_id) {

            //TODO: Should add a check for empty mandatory field.
            $message = $this->key . '|' . $this->applicationId . '|' . $transaction_id . '|' . $this->salt;

            //echo $message;

            $hash = hash('sha256', $message);
            return $hash;

        }

        /**
        * Generate Message SHA256 hash
        **/
        function generateHash($syberpay_args) {

            //TODO: Should add a check for empty mandatory field.
            $message = $this->key . '|' . $this->applicationId . '|' . $this->serviceId . '|' . $syberpay_args['amount'] . 
            '|' . $syberpay_args['currency'] .  '|' . $syberpay_args['customerRef'] . '|' . $this->salt;

            //echo $message;

            $hash = hash('sha256', $message);
            return $hash;

        }

        /**   
        * Call SyberPay WS and returns the payment page for this transaction
        **/
        function getPaymentUrl($syberpay_args) {
        require_once('httpful.phar');
        //use Httpful\Request;

            try {
                //set the uri for SyberPay WS
                $url = $this->payment_url;
                
                //set the HTTP content-type to json
                $content_type = 'application/json';    
    
                //encode the data as json. The data should have : applicationId, serviceId, amount, customerRef , and paymentInfo.
                $data = json_encode($syberpay_args );
                //if PHP < 5.4
                //$data = $paymentReq->to_json();
                //echo "$data";
    
                //call the WS and get the json response.
                $response = Httpful\Request::post($url, $data)->addHeader('Content-Type',$content_type)->send();
                //echo "$response"; 

                //get the response code value 
                $rcode = $response->body->responseCode;
                //echo 'Response Code' . $rcode;
    
                //code == 1 means requset is successful
                if ($rcode == 1) {
                    //get the payment url
                    $redirectUrl = $response->body->paymentUrl;
                    //echo 'Response URL' . $redirectUrl;
                } else {
                    //return the default error page
                    $redirectUrl = 'http://www.google.com';
                }    
            } catch(Exception $e) {
                $redirectUrl = 'http://www.google.com';
            }
    
            return $redirectUrl;
        }

        /**
        *
        **/
        function callRemoteUrl($syberpay_args) {
            
            $api_result = array();
            //global $woocommerce;
            $checkout_url = '';

            try {
                //set the uri for SyberPay WS
                //$url = "http://localhost:8880/SyberPay/syberpay/getUrl";
                $url = $this->payment_url;
                
                //set the HTTP content-type to json
                $content_type = 'application/json';    
    
                //encode the data as json. The data should have : applicationId, serviceId, amount, customerRef , and paymentInfo.
                $data = json_encode($syberpay_args );

                //echo $data;
                $response = wp_remote_post(
                    $url,
                    array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(
                            'Content-Type' => $content_type,
                            //'Authorization' => 'Basic ' . base64_encode( 'ias' . ':' . '1q2w3e$r' ),
                            //'X-Redmine-API-Key' => '59930c6460e8e71ef58b4cc95d852153bf21b510'
                            ),
                        'cookies' => array(),
                        'body' => $data,
                    )
                );

                if ( is_wp_error( $response ) ) 
                    throw new Exception( __( 'We are currently experiencing problems trying to connect to this payment gateway. Sorry for the inconvenience.', 'syberpay' ) );
 
                if ( empty( $response['body'] ) )
                    throw new Exception( __( 'SyberPay\'s Response was empty.', 'syberpay' ) );
             
                // Retrieve the body's resopnse if no errors found
                $response_body = wp_remote_retrieve_body( $response );
                $json_response_body = json_decode($response_body,true);
                $response_code = $json_response_body['responseCode'];
                //echo $response_code;

                if ( $response_code == 1 ) {
                    $api_result['message'] = 'Thank you for your order. We are now redirecting you to the gateway to make payment.';
                    $api_result['url'] = $json_response_body['paymentUrl'];
                } else {
                    $api_result['message'] = 'Sorry for the inconvenience. There is a problem integrating with the payment gateway, ' . $json_response_body['responseMessage'] ;
                    $api_result['url'] =  $checkout_url;
                }

                } catch(Exception $e) {
                    $api_result['message'] = 'Sorry for the inconvenience. We are currently experiencing problems trying to connect to this payment gateway. Please try again later.';
                    $api_result['url'] =  $checkout_url;
                }

                //echo $api_result['url'];
                return $api_result;
        }

 /**
        *
        **/
        function getPaymentStatus($syberpay_args) {
                       $this->log('entering getPaymentStatus ....');

            $api_result = array();
            //global $woocommerce;
            $checkout_url = '';

            try {
                //set the uri for SyberPay WS
                //$url = "http://localhost:8880/SyberPay/syberpay/getUrl";
                $url = $this->payment_status_url;
                
                //set the HTTP content-type to json
                $content_type = 'application/json';    
    
                //encode the data as json. The data should have : applicationId, serviceId, amount, customerRef , and paymentInfo.
                $data = json_encode($syberpay_args );

                //echo $data;
                $response = wp_remote_post(
                    $url,
                    array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(
                            'Content-Type' => $content_type,
                            //'Authorization' => 'Basic ' . base64_encode( 'ias' . ':' . '1q2w3e$r' ),
                            //'X-Redmine-API-Key' => '59930c6460e8e71ef58b4cc95d852153bf21b510'
                            ),
                        'cookies' => array(),
                        'body' => $data,
                    )
                );

                if ( is_wp_error( $response ) ) {
                                           $this->log('e1 getPaymentStatus ....');

                    throw new Exception( __( 'We are currently experiencing problems trying to connect to this payment gateway. Sorry for the inconvenience.', 'syberpay' ) );
                }
 
                if ( empty( $response['body'] ) ){
                                                               $this->log('e2 getPaymentStatus ....');

                    throw new Exception( __( 'SyberPay\'s Response was empty.', 'syberpay' ) );
                }
             
                // Retrieve the body's resopnse if no errors found
                $response_body = wp_remote_retrieve_body( $response );
                $json_response_body = json_decode($response_body,true);
                //$response_code = $json_response_body['responseCode'];
                //echo $response_code;

                // if ( $response_code == 1 ) {
                //     $api_result['message'] = 'Thank you for your order. We are now redirecting you to the gateway to make payment.';
                //     $api_result['url'] = $json_response_body['paymentUrl'];
                // } else {
                //     $api_result['message'] = 'Sorry for the inconvenience. There is a problem integrating with the payment gateway, ' . $json_response_body['responseMessage'] ;
                //     $api_result['url'] =  $checkout_url;
                // }

                } catch(Exception $e) {
                    $api_result['message'] = 'Sorry for the inconvenience. We are currently experiencing problems trying to connect to this payment gateway. Please try again later.';
                    $api_result['url'] =  $checkout_url;
                $this->log('e3 getPaymentStatus ....'.$e);


                }
                 $this->log('done getPaymentStatus ...json_response_body==.'.$json_response_body);

                //echo $api_result['url'];
                return $json_response_body;
        }

        
        /*
        
	    // Output for the message ?????????????.
        function wc_syberpay_message() {

		    $order_id 		= absint( get_query_var( 'order-received' ) );
		    $order 			= wc_get_order( $order_id );
		    $payment_method = $order->payment_method;

		    if( is_order_received_page() &&  ( 'syberpay_gateway' == $payment_method ) ){

			    $syberpay_message 	= get_post_meta( $order_id, '_syberpay_message', true );

			    if( ! empty( $syberpay_message ) ){

				    $message 			= $syberpay_message['message'];
				    $message_type 		= $syberpay_message['message_type'];

				    delete_post_meta( $order_id, '_syberpay_message' );

				    wc_add_notice( $message, $message_type );
			    }
		    }
	    }
	
        add_action( 'wp', 'wc_syberpay_message' );

        // Validate fields
        public function validate_fields() {
            return true;
        }
     
        // Check if we are forcing SSL on checkout pages
        // Custom function not required by the Gateway
        public function do_ssl_check() {
            if( $this->enabled == "yes" ) {
                if( get_option( 'woocommerce_force_ssl_checkout' ) == "no" ) {
                    echo "<div class=\"error\"><p>". sprintf( __( "<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>" ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) ."</p></div>";   
                }
            }       
        }
        */
    
    } // End of Syber_Payment_Gateway
?>