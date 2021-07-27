<?php 

function send_android_notification($fields) {
		define("GOOGLE_API_KEY","AIzaSyD2ZzOazmDzvMNaIHuzBFxNsvjEmr7CyOE");		
		$url = 'https://fcm.googleapis.com/fcm/send';

		$headers = array(
		'Authorization: key='.GOOGLE_API_KEY,
		'Content-Type: application/json'
		);
		//Open connection
		$ch = curl_init();

		//Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		
		// Execute post
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		}
		//Close connection
		curl_close($ch);
		return $result;
	}
//calling andriod fucntion for notification
	$devicetoken = get_user_meta($send_notification_to_this_userid,'devicetoken', true);
				$devicetype = get_user_meta($send_notification_to_this_userid,'devicetype', true);
				$mes = 'Your Service reschedule request has been rejected by contractor';
			}
			if($devicetype == 'A'){ 
				$data = array(
					'message'   => $mes,
					'title'     => 'Service reschedule rejected',
					'vibrate'   => 1,
					'sound'     => 1,
					'type'		=> 'Job reschedule rejected'   
					);
				$message =array (
					'body'   => 'Service reschedule rejected',
					'title'  => 'Service reschedule rejected'
				);
				$fields = array(
					'to' =>  $devicetoken,
					'notification' => $message,
					'data' => $data,
				);
				$result = $this->send_android_notification($fields);
			}	
//calling andriod fucntion for notification	
	
	//IOS NOTIFICATION
	function send_ios_notification($data,$devicetoken,$body){
		// Put your private key's passphrase here:
		//~ print_r($body); die();
		$passphrase = '';
		// Put your alert message here:
		$message = $data;
		
		//$url = site_url();
		$cert_url = plugin_dir_path( __FILE__ ).'GetCoolSandbox.pem';
		//$cert_url = '/var/www/html/getcool/wp-content/plugins/json-api/controllers/GetCoolAPNS.pem';
		//$cert_url = plugin_dir_path( __FILE__ ).'SecretCardProduction.pem';
		if (!$message)
			exit('Message: Not able to Send Notification' . "\n");
			////////////////////////////////////////////////////////////////////////////////
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_url);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase); 
			
			// Open a connection to the APNS server gateway.sandbox.push.apple.com
			$fp = stream_socket_client( 'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		//$fp = stream_socket_client( 'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $devicetoken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		if (!$result)
		//~ exit('Message not delivered' . PHP_EOL);
		return 0;

		// Close the connection to the server
		fclose($fp);
		//~ return $result;
		return 1;
	}
	
//call function for send notification to ios users



					$data_user = array();
					$data_user['login_user_image'] = get_user_meta( $user_id, 'cupp_upload_meta',true);
			        $data_user['login_user_name'] = get_user_meta($user_id, 'first_name', true);
					$message = $data_user;
			
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta($video_author_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'Your are followed by'.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'follow user',
							'Data' => $message,
						);
						
										
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'Your are followed by'.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'follow user',
							'Data' => $message,
						);
						update_user_meta($follow_unfollow_user_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($data_user,$follow_unfollow_devicetoken,$body);	
					
					
					
	
/******************** API for Badge Reduction [START] ****************/
	public function reduce_badge_count() { 
		global $json_api;
		$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$response       = $data->response;
		$devicetoken       = $data->devicetoken;
		$users = get_users(array(
			'meta_key'     => 'devicetoken',
			'meta_value'   => $devicetoken,
			'meta_compare' => '=',
			));	
			
		
		$results = update_user_meta($users[0]->ID, 'badge', 0);
		
		if($results == 1){
			$message = $this->_requestStatus("2033");
			return array(
				"RespCode" => "2033",
				"Message" => $message
			);
		} else {
			$message = $this->_requestStatus("2033");
			return array(
				"RespCode" => "2033",
				"Message" => $message
			);
		}
			
	}	
/******************** API for Badge Reduction [END] ****************/	