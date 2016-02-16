<?php

class MZ_Vindi_Sandbox {

	private $mz_mbo_globals;
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	
	public function mZ_vindi_mbo_sandbox($atts, $account=0) {
	
	  $json_data = file_get_contents('/Users/mikekilmer/Documents/Clients/Tracer Parkour/testJSON_mine.txt');
	  $sale_details = json_decode($json_data, true);
	  echo "<hr/>";

	  	$mb = MZ_Mindbody_Init::instantiate_mbo_API();
	  	$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
	  	$mb->userCredentials = array (
									//'Username' => '_' . $options['mz_source_name'],
									//'Password' => $options['mz_mindbody_password'],
									'Username' => 'Siteowner',// '_' . $options['mz_source_name'],
								  'Password' => 'apitest1234',// $options['mz_mindbody_password'],
									'SiteIDs' => array($options['mz_mindbody_siteID']
									));
			
/*	  	
	  $checkoutShoppingCartRequest = $mb->CheckoutShoppingCart(array(
    'Test'=>'true',
    'ClientID'=>1234,
    'CartItems'=>array(
        'CartItem'=>array(
            'Quantity'=>1,
            'Item' => new SoapVar(
                array('ID'=>'1357'), 
                SOAP_ENC_ARRAY, 
                'Service', 
                'http://clients.mindbodyonline.com/api/0_5'
            ),
            'DiscountAmount' => 0
        )
    ),
    'Payments' => array(
        'PaymentInfo' => new SoapVar(
            array(
                'CreditCardNumber'=>'4111111111111111', 
                'ExpYear'=>'2015', 
                'ExpMonth'=>'06', 
                'Amount'=>'130', 
                'BillingAddress'=>'123 Happy Ln', 
                'BillingPostalCode'=>'93405'
            ), 
            SOAP_ENC_ARRAY, 
            'CreditCardInfo', 
            'http://clients.mindbodyonline.com/api/0_5'
        )
    )
));
*/
		$checkoutShoppingCartRequest = $mb->CheckoutShoppingCart(array(
    'Test'=>'true',
    'ClientID'=>'1234', //Rivka at URU 100000602,
    'CartItems'=>array(
        'CartItem'=>array(
            'Quantity'=>1,
            'Item' => new SoapVar(
                array('ID'=>'1357'), 
                SOAP_ENC_ARRAY, 
                'Service', 
                'http://clients.mindbodyonline.com/api/0_5'
            ),
            'DiscountAmount' => 0
        )
    ),
    'Payments' => array(
        'PaymentInfo' => new SoapVar(
            array(
							'Amount' => 130
              ), 
            SOAP_ENC_ARRAY, 
            'CashInfo', 
            'http://clients.mindbodyonline.com/api/0_5'
        )
    )
));

	  	
		mz_pr($checkoutShoppingCartRequest);
		/*$test = $mb->AddOrUpdateClients(array(
			'Test'=>'False',
			'UpdateAction'=>'Account Balance',
			'Clients'=>array(
				'ID'=>'1234',
				'AccountBalance'=>'90'
				)
		));
		
		mz_pr($test);
		*/
		$mb->debug();
	  $return = <<<EFD
		   <!-- Page Content -->
    <div class="container">

        <div class="row">
EFD;
		
		$return .= "<p>That's all she wrote.</p></div></div>";
	  return $return;
	}

	protected function remove_empties ($matches) {
	  ### Variableize the stuff between the tags.
	  $content = $matches;
	  ### Remove all nbsps, empty tags, brs, and whitespace.
	  $content = str_replace('&nbsp;', '', $content);
	  $content = preg_replace('%<(\w+)[^>]*></\1>%', '', $content);
	  $content = preg_replace('%<br/?>%', '', $content);
	  $content = preg_replace('/\s/s', '', $content);
	  ### If there is still content the tag innards are not empty,
	  ### send back the original match. Otherwise, send empty.
	  return $content ? $matches[0] : '' ;
	}

	// http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
	protected function sortById($a, $b) {
		return $a['ID'] - $b['ID'];
	}
}//EOF MZ_MBO_Staff
?>
