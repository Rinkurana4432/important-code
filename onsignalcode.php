<?php 	
	
	function sendMessage($message, $playerid){
		$content = array(
		  "en" => $message
		  );
		$fields = array(
		  'app_id' => "0d498369-f799-42f1-9424-e52e2e78b8e6",
		  'include_player_ids' =>array($playerid),
		  'contents' => $content
		);
		
		if($data != 0 )
		{
			$fields['data']= $data;
		}	
		
		$fields = json_encode($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
							   'Authorization: Basic ZTE3Mjk3ZGYtZTA0Ny00NDE0LTkyZTgtZTVmYzgxZTQyZGQx'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	  }
	  
	  
	  //jis bhe table main auth_code(device_token, player_id) jo bhe name hoo vo nikal kar yhan call karana hai $device_token ki jghan 
	  //function jo call karna hai jahan se push send karni hai 
	  sendMessage($msg ,$device_token);