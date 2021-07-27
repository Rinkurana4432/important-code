<?php
public function sendOTP() {
        global $json_api;
        $json           = file_get_contents("php://input");
        $data           = json_decode($json);
        $contact_num = $data->contact_num;
	
		if($contact_num == ''){
			$message = $this->_requestStatus("13007");
			return array(
				"RespCode" => "13007",
				"Message" => $message,
			);
		}
		$contact_num = '+'.$contact_num;
		$otp = rand(1111,9999);
		
		/******************** Code to Send OTP SMS [START] *******************/
			// Step 1: Get the Twilio-PHP library from twilio.com/docs/libraries/php, 
			require $_SERVER['DOCUMENT_ROOT']. '/getcool/twilio-php-master/Twilio/autoload.php';
			
			// Step 2: set our AccountSid and AuthToken from https://twilio.com/console
			$AccountSid = "AC5491a6081a6bf23d977f69de13e1d3da";
			$AuthToken = "98bdec7e76db3ad005a37ce4e6965433";
			
			// Step 3: instantiate a new Twilio Rest Client
			$class = 'Twilio\Rest\Client';
			$client = new $class($AccountSid, $AuthToken);
			
			//echo 'Hello';
			// Step 4: make an array of people we know, to send them a message. 
			// Feel free to change/add your own phone number and name here.
			$people = array(
				$contact_num => "Sender Name"
			);

			// Step 5: Loop over all our friends. $number is a phone number above, and 
			// $name is the name next to it
			try {
				foreach ($people as $number => $name) {
	
					 $sms = $client->account->messages->create(
	
						// the number we are sending to - Any phone number
						$number,
	
						array(
							// Step 6: Change the 'From' number below to be a valid Twilio number 
							// that you've purchased
							'from' => "+14172135477",
							
							// the sms body
							'body' => $otp
						)
					); 
	
				}
			} 
			catch(Exception $e) {
			 echo 'Message: ' .$e->getMessage(); die();
			  // $message = $this->_requestStatus("13008");
				// $data =  array(
					// "RespCode" => "13008",
					// "Message" => $message,
					// "OTP" => $otp
				// );
				
				
			}
		/******************** Code to Send OTP SMS [END] *******************/
		echo 'Message: ' .$e->getMessage(); die();
		// $message = $this->_requestStatus("13006");
		// return array(
			// "RespCode" => "13006",
			// "Message" => $message,
			// "OTP" => $otp
		// );
    }