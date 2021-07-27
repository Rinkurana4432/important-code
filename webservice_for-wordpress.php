<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "wp-config.php";
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (mysqli_connect_errno()){

    exit("Couldn't connect to the database: ".mysqli_connect_error());
}



$service = $_REQUEST['service_type'];

 
if(!empty($service))
{
/************** Sign up****************/	
 if($service == 'signup')
 {
	  if(isset($_REQUEST['state']) && isset($_REQUEST['firstname']) && isset($_REQUEST['lastname']) && isset($_REQUEST['email']) && isset($_REQUEST['password']) && isset($_REQUEST['type']) && isset($_REQUEST['obj_id']) && isset($_REQUEST['sign_type']) && isset($_REQUEST['fb_id']) && isset($_REQUEST['accessToken'])) 
	 {
		
		$firstname=sanitize_text_field( $_REQUEST['firstname'] );
		$lastname=sanitize_text_field( $_REQUEST['lastname']);
		$username = sanitize_text_field(  $_REQUEST['email'] );
		$email = sanitize_text_field(  $_REQUEST['email']  );
		$state = sanitize_text_field(  $_REQUEST['state']  );
		$fb_token = sanitize_text_field(  $_REQUEST['accessToken']  );
		$password = $wpdb->escape( sanitize_text_field( $_REQUEST['password']));
		
		 
		 if($_REQUEST['sign_type'] == 'fb'){
			$select_users = $wpdb->get_results('SELECT * FROM `wp_users` where user_email = "'.$_REQUEST['email'].'"');
				if($select_users[0]->user_email == $_REQUEST['email']){
					$check_objID = $wpdb->get_var('SELECT COUNT(*) from `wp_sweetnector_authcode` where obj_id = "'.$_REQUEST['obj_id'].'"');
	 				 if( $check_objID > 0 ){
					  $delete_objID = $wpdb->query('DELETE from `wp_sweetnector_authcode`  WHERE obj_id = "'.$_REQUEST['obj_id'].'"');
			 		}	
					$auth_code = md5(microtime().rand(0,9999));
					$inser_authtokn = $wpdb->query('INSERT INTO `wp_sweetnector_authcode`(`user_id`, `auth_code`, `type`, `obj_id`) VALUES 		("'.$select_users[0]->ID.'","'.$auth_code.'","'.$_REQUEST['type'].'","'.$_REQUEST['obj_id'].'")');
				echo '{"Status":"true", "Data":[{"auth_code":"'.$auth_code.'"}]}';
				die();
			}else{
			    $select_identifier = $wpdb->get_var('SELECT ID FROM `wp_social_users` WHERE type = "fb" AND identifier = "'.$_REQUEST['fb_id'].'" ');
	  	    if($select_identifier == '' ){
				 $wpdb->query($wpdb->prepare('DELETE FROM wp_social_users WHERE ID = %d AND type = \'fb\'', $_REQUEST['fb_id']));
			  	 $status = wp_create_user($username,$password,$email);
				 $insert_fb_data = $wpdb->query('INSERT INTO `wp_social_users`(`ID`, `type`, `identifier`) VALUES ("'.$status.'","'.$_REQUEST['sign_type'].'","'.$_REQUEST['fb_id'].'")');
				update_user_meta($status, 'fb_user_access_token', $_REQUEST['accessToken']);
				//$status = wp_create_user($username,$password,$email);
			 } 
		}
	}
			 
		else { 
			  $status = wp_create_user($username,$password,$email);
		  }
		 
		 
		 
		if($status->errors['existing_user_login'][0]){
			echo '{"Status":"false","error":"Sorry, that email already exists!"}';
		  }elseif($status->errors['existing_user_email'][0]){
			echo '{"Status":"false","error":"Sorry, that email address is already used!"}';
		}else{
			$user_id=$status;
			update_user_meta( $user_id, 'first_name', $firstname);
			update_user_meta( $user_id, 'last_name', $lastname);
			update_user_meta( $user_id, 'billing_first_name', $firstname);
       		update_user_meta( $user_id, 'billing_last_name', $lastname);
       		update_user_meta( $user_id, 'billing_state', $state);
       		update_user_meta( $user_id, 'billing_email', $email);
       		
 			$reg_phone = 0;
			update_user_meta( $user_id, 'billing_phone', $reg_phone );
			
			$auth_code = md5(microtime().rand(0,9999));
			$inser_authtokn = $wpdb->query('INSERT INTO `wp_sweetnector_authcode`(`user_id`, `auth_code`, `type`, `obj_id`) VALUES ("'.$user_id.'","'.$auth_code.'","'.$_REQUEST['type'].'","'.$_REQUEST['obj_id'].'")');
			
			
		echo '{"Status":"true", "Data":[{"auth_code":"'.$auth_code.'"}]}';
		
		}
		 
		
 }else{
	 echo '{"Status":"false","error":"Some Error Occured."}';
	 }
 }
	
	
/*******************LOGIN*********************/
	if($service == 'login')
	{
	if(isset($_REQUEST['user_login']) || isset($_REQUEST['user_email']) && isset($_REQUEST['user_pass']) && isset($_REQUEST['type']) && isset($_REQUEST['obj_id']) ) {
		
		 $user = wp_authenticate($_REQUEST['user_email'],$_REQUEST['user_pass']);
		
		//print_r($user->errors['invalid_email'][0]);die('sdfsd');
		
		  $auth_code = md5(microtime().rand(0,9999));
		
		 if(isset($user->data)){
			
			 $check_objID = $wpdb->get_var('SELECT COUNT(*) from `wp_sweetnector_authcode` where obj_id = "'.$_REQUEST['obj_id'].'"');
			 
			 //print_r($check_objID);die();
			 
			 if( $check_objID > 0 ){
			  $delete_objID = $wpdb->query('DELETE from `wp_sweetnector_authcode`  WHERE obj_id = "'.$_REQUEST['obj_id'].'"');
			 }
			 $inser_authtokn = $wpdb->query('INSERT INTO `wp_sweetnector_authcode`(`user_id`, `auth_code`, `type`, `obj_id`) VALUES ("'.$user->data->ID.'","'.$auth_code.'","'.$_REQUEST['type'].'","'.$_REQUEST['obj_id'].'")');
			 
	 }
		
		if(isset($user->errors['invalid_email'][0])){
			echo '{"Status":"false","error":"Invalid Email"}';
			}
			elseif(isset($user->errors['incorrect_password'][0])){
				echo '{"Status":"false","error":"Invalid Password"}';
				}
			elseif(isset($user->errors['empty_password'][0])){
			echo '{"Status":"false","error":"The password field is empty"}';
				}else{
				//echo '{"status":"true","Data":"Login Successfully"}';
				echo '{"Status":"true", "Data":[{"auth_code":"'.$auth_code.'"}]}';	
			}
		}else
		{
		echo '{"Status":"false","error":"Some Error Occured."}';
		}
	}		
	
	
	/******************GET ALL PRODUCT*******************/
	if($service == 'get_product')
	{
		 $args = array(
			    'post_type' => 'product',
			    'stock' => 1,
			    'posts_per_page' => 9,
			    'orderby' =>'date',
			    'order' => 'DESC' );
			    $loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
		    $attr = get_post_meta($product->id, '_product_attributes' );
		    $my_meta = get_post_meta( $product->id, 'dryfruit', true );
	
	//print_r($my_meta);

		
		//to get calories etc
		foreach($attr[0] as  $brdnewattr){ 
			
			$prod_options[$brdnewattr['name']] = $brdnewattr['value'];
		}
		$ingredients = explode(',',$prod_options['Ingredients']);
		$post_thumbnail_id = get_post_thumbnail_id($post->ID);
		$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );

	$array[] = array(
            'id' => get_the_ID(),
            'title' => get_the_title(),
			'img' => $post_thumbnail_url,
			'Calories' => $prod_options['Calories'],
			'Protein (g)' => $prod_options['Protein (g)'],
			'Sugars (g)' => $prod_options['Sugars (g)'],
			'Carbs (g)' => $prod_options['Carbs (g)'],
			'Ingredients' => $ingredients,
			'Notes' => $prod_options['Notes'],
		    'Type' => $my_meta,
           );
		
		
	   	   endwhile;
		
		echo json_encode($array);
		
		
		wp_reset_query();
      
	}
	
/*****************Get Account Detail*************************/
	
	if($service == 'get_account_detail'){
		
		if(isset($_REQUEST['auth_code'])){
			 $check_authcode = $wpdb->get_row('SELECT * from `wp_sweetnector_authcode` where auth_code = "'.$_REQUEST['auth_code'].'"');
					
		    if($check_authcode > 0){
				 $current_user_login =  $check_authcode->user_id;
				//echo $user_id = get_current_user_id($current_user_login);
				//die('sdf');
				//get_current_user_id();
				
			$load_address = sanitize_key( 'billing' );
			$address = WC()->countries->get_address_fields( get_user_meta( $current_user_login, $load_address . '_country', true ), $load_address . '_' ); 
			
				
				
			foreach ( $address as $key => $field ) {

			$value = get_user_meta( $current_user_login, $key, true );
			
				
			if ( ! $value ) {
				switch ( $key ) {
					case 'billing_email' :
					case 'shipping_email' :
						$value = $current_user->user_email;
						break;
					case 'billing_country' :
					case 'shipping_country' :
						$value = WC()->countries->get_base_country();
						break;
					case 'billing_state' :
					case 'shipping_state' :
						$value = WC()->countries->get_base_state();
					break;
				}
			}

			$address[ $key ]['value'] = apply_filters( 'woocommerce_my_account_edit_address_field_value', $value, $key, $load_address );
				
				//print_r($address);die('sdf');
		}			 
			
			$countries = WC()->countries->get_allowed_countries();
			$current_cc  = WC()->checkout->get_value( 'billing_country' );
			$states      = WC()->countries->get_states( $current_cc );
			
				$account_data['detail'][] = array(
				               'first_name' =>$address['billing_first_name']['value'],
				               'last_name' =>$address['billing_last_name']['value'],
				               'company' =>$address['billing_company']['value'],
				               'email' =>$address['billing_email']['value'],
				               'phone' =>$address['billing_phone']['value'],
				               'country' =>$address['billing_country']['value'],
				               'address_1' =>$address['billing_address_1']['value'],
				               'address_2' =>$address['billing_address_2']['value'],
				               'city' =>$address['billing_city']['value'],
				               'state' =>$address['billing_state']['value'],
				               'postcode' =>$address['billing_postcode']['value'],
						      );
					
				$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
					'numberposts' => $order_count,
					'meta_key'    => '_customer_user',
					'meta_value'  => $current_user_login,//get_current_user_id()
					'post_type'   => wc_get_order_types( 'view-orders' ),
					'post_status' => array_keys( wc_get_order_statuses() ),
				) ) );
				
				//print_r($customer_orders);
				//for date and time an image
				$order = new WC_Order( $cust_order->ID );
				$items = $order->get_items();
				
				
				  foreach($customer_orders as $cust_order){ 
				  	//print_r($cust_order);
					  $yrdata= strtotime($cust_order->post_date);
					  $order_date = date('d-M-y', $yrdata);
				      $time =  $cust_order->post_date;
				      $time_new = date("d.m.y h a",strtotime($time));
					  
				$order = new WC_Order( $cust_order->ID );
				$items = $order->get_items();
				$img_array = array();
				//for prduct image
				foreach($items as $item){
					//print_r($item);						
				 $product_name = $item['name'];
    			 $product_id = $item['product_id'];
    			 $product_qty = $item['quantity'];
    			 $order_id = $item['order_id'];
					
				 $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
				
				 for($i=0; $i < $product_qty; $i++) {
					$img_array[] = $image[0]; 
				 
					}
					
				}
					  
					  $get_product_img['orders'][] = array(
				          'order_id' =>  $order_id,
				          'order_date' =>  $order_date,
				          'order_time' =>  $time_new,
						  'post_status'=>wc_get_order_status_name( $cust_order->post_status ),
						  'images'=>$img_array,
				    	);
			  }	
				$all_data = array_merge( (array)$account_data, (array)$get_product_img );
				//$all_data = array_merge($account_data, $get_product_img);
				 
				$ddd =  json_encode($all_data);
				echo '{"Status":"true","Data":'.$ddd.'}';
				//echo '{"Status":"true", "Data":[{"account_detail":"'.$all_data.'"}]}';
			
		}else{
			echo '{"Status":"false" , "Data":[{"result":"authcode expired"}]}';			
			die();		
		}
		
		}	
	
	}
	

	
	/*******************Add to cart********************/
	
	if($service == 'create_order'){
		if(isset($_REQUEST['auth_code']) && isset($_REQUEST['products']) && isset($_REQUEST['address_1']) && isset($_REQUEST['address_2']) && ($_REQUEST['postcode']) && isset($_REQUEST['amount'])  && isset($_REQUEST['stripeToken']) && isset($_REQUEST['description'])  && isset($_REQUEST['payment_type'])  ){
		 $check_authcode = $wpdb->get_row('SELECT * from `wp_sweetnector_authcode` where auth_code = "'.$_REQUEST['auth_code'].'"');
		    if($check_authcode > 0){
				if($_REQUEST['payment_type'] == 'paypal'){
				$prods = $_REQUEST['products'];
				$data_prod = json_decode(stripslashes($prods), true);
				$product_value_count =  array_sum($data_prod);
				
				if($product_value_count != 6){
				echo '{"Status":"false" , "Data":[{"result":"Please Check your product quantity"}]}';	
				}
				 //WC()->shipping->load_shipping_methods();
   				// $shipping_methods = WC()->shipping->get_shipping_methods();
				//$selected_shipping_method = $shipping_methods['flat_rate'];
				// print_r();die('sdfs');
			
			$current_user_login =  $check_authcode->user_id;	
			
			$userInfo = get_user_meta($current_user_login);	
				
			$address = array(
						'first_name' => $userInfo['first_name'][0],
						'last_name'  => $userInfo['last_name'][0],
						'email'      => $userInfo['nickname'][0],
						'address_1'  => $_REQUEST['address_1'],
						'address_2'  => $_REQUEST['address_2'],
						'state'      => $_REQUEST['state'],
						'postcode'   => $_REQUEST['postcode'],
						'country'    => 'AU'
						);
				//print_r($address);die('asdfas');	
				$order = wc_create_order();
				foreach($data_prod as $prod_id=>$prd_val){
				$order->add_product( get_product($prod_id), $prd_val);
				}
			  $order->set_address( $address, 'billing' );
			  $order->set_address( $address, 'shipping' );
					  WC()->shipping->calculate_shipping( WC()->cart->get_shipping_packages() );

				// Get the rate object selected by user.
				foreach ( WC()->shipping->get_packages() as $package_key => $package ) {
					foreach ( $package['rates'] as $key => $rate ) {
						$item = new WC_Order_Item_Shipping();
							$item->set_props( array(
										'method_title' => $rate->label,
										'method_id'    => $rate->id,
										'total'        => wc_format_decimal( $rate->cost ),
										'taxes'        => $rate->taxes,
										'order_id'     => $order->id,
									) );
						foreach ( $rate->get_meta_data() as $key => $value ) {
										$item->add_meta_data( $key, $value, true );
									}

						$order->add_item( $item );
					}
				}	  			
			  $order->calculate_totals(); 				
			   update_post_meta( $order->id, '_customer_user', $current_user_login );
				$order = new WC_Order($order->id);
				if (!empty($order)) {
					$order->set_customer_note( $_REQUEST['order_notes'] );
					$order->update_status( 'wc-processing' );
					
					// Add shipping costs
					$shipping_taxes = WC_Tax::calc_shipping_tax( '10', WC_Tax::get_shipping_tax_rates() );
					$rate   = new WC_Shipping_Rate( 'flat_rate_shipping', 'Flat rate shipping', '10', $shipping_taxes, 'flat_rate' );
					$item   = new WC_Order_Item_Shipping();
					$item->set_props( array(
					  'method_title' => $rate->label,
					  'method_id'    => $rate->id,
					  'total'        => wc_format_decimal( $rate->cost ),
					  'taxes'        => $rate->taxes,
					) );
					foreach ( $rate->get_meta_data() as $key => $value ) {
					  $item->add_meta_data( $key, $value, true );
					}
					$order->add_item( $item );					

				}	
				echo '{"Status":"true" , "Data":[{"result":"Order Created"}]}';
				}else{
				$amount = $_REQUEST['amount']*100;	
					$apiKey = 'sk_test_KHtRNB0BQUDIUdEH3ouzAhUm';
					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => "https://api.stripe.com/v1/charges",
						CURLOPT_POST => 1,
						CURLOPT_HTTPHEADER => array(
							"Authorization: Bearer " . $apiKey
						),
						CURLOPT_POSTFIELDS => http_build_query(array(
							"amount" => $amount,
							"currency" => 'AUD',
							"source" => $_REQUEST['stripeToken'],
							"description" => $_REQUEST['description']
						))
					));
					
					$resp = curl_exec($curl);
					curl_close($curl);
					$data_val = json_decode($resp, true);
					
					if($data_val['paid'] == 1){
					$prods = $_REQUEST['products'];
					$data_prod = json_decode(stripslashes($prods), true);
					$product_value_count =  array_sum($data_prod);
					if($product_value_count != 6){
					echo '{"Status":"false" , "Data":[{"result":"Please Check your product quantity"}]}';	
					}

					$current_user_login =  $check_authcode->user_id;	
					$userInfo = get_user_meta($current_user_login);	
					$address = array(
						'first_name' => $userInfo['first_name'][0],
						'last_name'  => $userInfo['last_name'][0],
						'email'      => $userInfo['nickname'][0],
						'address_1'  => $_REQUEST['address_1'],
						'address_2'  => $_REQUEST['address_2'],
						'state'      => $_REQUEST['state'],
						'postcode'   => $_REQUEST['postcode'],
						'country'    => 'AU'
						);
					  $order = wc_create_order();
						foreach($data_prod as $prod_id=>$prd_val){
						 $order->add_product( get_product($prod_id), $prd_val);
							}
			  			$order->set_address( $address, 'billing' );
			 			 $order->set_address( $address, 'shipping' );
						  WC()->shipping->calculate_shipping( WC()->cart->get_shipping_packages() );

					// Get the rate object selected by user.
					foreach ( WC()->shipping->get_packages() as $package_key => $package ) {
						foreach ( $package['rates'] as $key => $rate ) {
							$item = new WC_Order_Item_Shipping();
								$item->set_props( array(
											'method_title' => $rate->label,
											'method_id'    => $rate->id,
											'total'        => wc_format_decimal( $rate->cost ),
											'taxes'        => $rate->taxes,
											'order_id'     => $order->id,
										) );
							foreach ( $rate->get_meta_data() as $key => $value ) {
											$item->add_meta_data( $key, $value, true );
										}

							$order->add_item( $item );
						}
					}	  	
						  $order->calculate_totals(); 				
					  update_post_meta( $order->id, '_customer_user', $current_user_login );
				    	$order = new WC_Order($order->id);
				if (!empty($order)) {
					$order->set_customer_note( $_REQUEST['order_notes'] );
					$order->update_status( 'wc-processing' );
				}	
				echo '{"Status":"true" , "Data":[{"result":"Order Created"}]}';
			}else{
				$error1 =  $data_val['error']['message'];
				echo '{"Status":"false", "Data":[{"result":"'.$error1.'"}]}';
			}
		}
			}else{
			echo '{"Status":"false" , "Data":[{"result":"authcode expired"}]}';			
			die();		
		}
	}else{
			echo '{"Status":"false" , "Data":[{"result":"Some thing missing"}]}';			
			die();		
		}
}		

if($service == 'update_user'){
	if(isset($_REQUEST['auth_code']) && isset($_REQUEST['state']) && isset($_REQUEST['city']) && isset($_REQUEST['firstname']) && isset($_REQUEST['lastname']) && isset($_REQUEST['postcode']) && isset($_REQUEST['phone']) && isset($_REQUEST['address_1'])  ){
		 $check_authcode = $wpdb->get_row('SELECT * from `wp_sweetnector_authcode` where auth_code = "'.$_REQUEST['auth_code'].'"');
		    if($check_authcode > 0){
			 $user_id =  $check_authcode->user_id;
				//echo $user_id ;die('sdf');
				update_user_meta( $user_id, 'billing_first_name', $_REQUEST['firstname']);
       			update_user_meta( $user_id, 'billing_last_name', $_REQUEST['lastname']);
       			update_user_meta( $user_id, 'billing_phone', $_REQUEST['phone']);
       			//update_user_meta( $user_id, 'billing_email', $_REQUEST['email']);
       			update_user_meta( $user_id, 'billing_state', $_REQUEST['state']);
       			update_user_meta( $user_id, 'billing_city', $_REQUEST['city']);
       			update_user_meta( $user_id, 'billing_postcode', $_REQUEST['postcode']);
       			update_user_meta( $user_id, 'billing_address_1', $_REQUEST['address_1']);
				echo '{"Status":"true","Data":"Update"}';
				
			}else{
			echo '{"Status":"false" , "Data":[{"result":"authcode expired"}]}';			
			die();		
		}
	
	
	}else{
		echo '{"Status":"false" , "Data":[{"result":"Some thing missing"}]}';
		
		}
}	
	
	
if($service == 'forgot_password'){
   if(isset($_REQUEST['user_email'])){
	  
   $check_Data = $wpdb->get_row('SELECT * from `wp_users` where user_email = "'.$_REQUEST['user_email'].'"');
	  
	if($check_Data > 0)
			{	
				$randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1). 			         substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);			                                   
			    $to = $_REQUEST['user_email'];
				$subject="Sweet Nector - New Password";
				$message = '<html><body topmargin="25"><p>Here is your new Password : '.$randomString.'' . '</p><br/><p>Use this to login.</p><br/><p>Regards,<br/>Team Sweet Nector</p></body></html>';
				$headers = "From: sweetnector@reply.com\r\n";
				$headers .= "Reply-To: sweetnector@reply.com\r\n";
				$headers .= "Return-Path: sweetnector@reply.com\r\n";
			    $headers = "MIME-Version: 1.0" . "\r\n";
           		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			    mail($to,$subject,$message,$headers);
				$update_pass = $wpdb->query('UPDATE `wp_users` SET `user_pass`="'.md5($randomString).'" WHERE user_email = "'.$_REQUEST['user_email'].'"');
				echo '{"Status":"true", "Data":[{"result":"A new password has been sent to your email. Please check email for further instructions."}]}';
			   exit();
			}
			else
			{	echo '{"Status":"false", "Data":[{"result":"The entered email address is not a valid registered user id. Please enter your registered email address."}]}';
				
				exit();
			}
			
   }else{
      echo '{"Status":"false", "Data":[{"result":"Data is missing"}]}';	
   }

}	
	
	

	
	
	
if($service == 'create_coupan'){
	if(isset($_REQUEST['fb_response']) && isset($_REQUEST['auth_code'])){
	$check_authcode = $wpdb->get_row('SELECT * from `wp_sweetnector_authcode` where auth_code = "'.$_REQUEST['auth_code'].'"');
	if($check_authcode > 0){	
	  $uniqid = uniqid();
      $rand_start = rand(1,5);
	  $rand_8_char = substr($uniqid,$rand_start,6);
	  $coupon_code = $rand_8_char; // Code
	  $amount = '5'; // Amount
	  $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
	  $coupon = array(
			'post_title' => $coupon_code,
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type'		=> 'shop_coupon'
		);
    	$new_coupon_id = wp_insert_post( $coupon );

		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
		update_post_meta( $new_coupon_id, 'individual_use', 'no' );
		update_post_meta( $new_coupon_id, 'product_ids', '' );
		update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
		update_post_meta( $new_coupon_id, 'usage_limit', 1 );
		update_post_meta( $new_coupon_id, 'expiry_date', '' );
		update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
		update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
	   echo '{"Status":"true" , "Data":[{"result":"'.$rand_8_char.'"}]}';	
	}else{
		echo '{"Status":"false" , "Data":[{"result":"authcode expired"}]}';			
		die();
	}
		


}
}
	
	
if($service == 'check_coupan_code'){

if(isset($_REQUEST['code']) && isset($_REQUEST['auth_code'])){
$check_authcode = $wpdb->get_row('SELECT * from `wp_sweetnector_authcode` where auth_code = "'.$_REQUEST['auth_code'].'"');
	if($check_authcode > 0){


	$code = $_REQUEST['code'];
	$coupon = new WC_Coupon($code);
	$coupon_post = get_post($coupon->id);
	
	$coupon_data = array(
		'id' => $coupon->id,
		'code' => $coupon->code,
		'type' => $coupon->type,
		'created_at' => $coupon_post->post_date_gmt,
		'updated_at' => $coupon_post->post_modified_gmt,
		'amount' => wc_format_decimal($coupon->coupon_amount, 2),
		'individual_use' => ( 'yes' === $coupon->individual_use ),
		'product_ids' => array_map('absint', (array) $coupon->product_ids),
		'exclude_product_ids' => array_map('absint', (array) $coupon->exclude_product_ids),
		'usage_limit' => (!empty($coupon->usage_limit) ) ? $coupon->usage_limit : null,
		'usage_count' => (int) $coupon->usage_count,
		'expiry_date' => (!empty($coupon->expiry_date) ) ? date('Y-m-d', $coupon->expiry_date) : null,
		'enable_free_shipping' => $coupon->enable_free_shipping(),
		'product_category_ids' => array_map('absint', (array) $coupon->product_categories),
		'exclude_product_category_ids' => array_map('absint', (array) $coupon->exclude_product_categories),
		'exclude_sale_items' => $coupon->exclude_sale_items(),
		'minimum_amount' => wc_format_decimal($coupon->minimum_amount, 2),
		'maximum_amount' => wc_format_decimal($coupon->maximum_amount, 2),
		'customer_emails' => $coupon->customer_email,
		'description' => $coupon_post->post_excerpt,
	);	
		if($coupon->id == 0){
		echo '{"Status":"false" , "Data":[{"result":"Coupon Not Found."}]}';
			die();
		}

		$usage_left = $coupon_data['usage_limit'] - $coupon_data['usage_count'];
	if ($usage_left > 0) {
		echo '{"Status":"true" , "Data":[{"result":"Coupon is Valid"}]}';
	} 
	else {
		echo '{"Status":"false" , "Data":[{"result":"Coupon Usage Limit Reached"}]}';	
	}
}else{
	echo '{"Status":"false" , "Data":[{"result":"authcode expired"}]}';			
	die();		
}	
}else{
	echo '{"Status":"false" , "Data":[{"result":"Something missing"}]}';			
	die();		
}	

}		
	

	

	
	
if($service == 'reorder'){

if(isset($_REQUEST['auth_code'])){
$check_authcode = $wpdb->get_row('SELECT * from `wp_sweetnector_authcode` where auth_code = "'.$_REQUEST['auth_code'].'"');
	if($check_authcode > 0){
	
		$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
					'numberposts' => 1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $check_authcode->user_id,
					'post_type'   => wc_get_order_types( 'view-orders' ),
					'post_status' => array_keys( wc_get_order_statuses() ),
				) ) );	
		
		if(empty($customer_orders)){
		echo '{"Status":"false" , "Data":[{"result":"Please Place the order first"}]}';			
	die();	
		}

			
			
			$order = new WC_Order($customer_orders[0]->ID);
			$items = $order->get_items();
			$img_array = array();
	
		foreach ( $items as $item ) {
			$product_name = $item['name'];
    		$product_id = $item['product_id'];
    		$product_qty = $item['quantity'];
			$post_thumbnail_id = get_post_thumbnail_id($product_id);
		    $post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
		
		$reorder_dtail[] = array(
		    'title' =>  $product_name,
			'img'=>$post_thumbnail_url,
			'Quantity'=> $product_qty, 	
			'id' =>  $product_id,
			
		);
	}
	$val =  json_encode($reorder_dtail);
		echo '{"Status":"true","result":'.$val.'}';
		
 }else{
	echo '{"Status":"false" , "Data":[{"result":"authcode expired"}]}';			
	die();		
} 
}else{
	echo '{"Status":"false" , "Data":[{"result":"Something missing"}]}';			
	die();		
}	
	
}
	
	
	
	
	
	
	
	
	
	
	
	
	

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	 

}	  
?>