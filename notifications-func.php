<?php

function parse_notification($devide_id, $device_type, $message)
{

	$applicationID = "Y0ayktjZhNw27Jo1rIUo8jzheOllcZiDyfEZXdJx";
	$apiKey = "lHWy1VyHHDbKfsv4j9z948nhzPPWdMxVGpzPbFXR";
	
	$deviceToken = $devide_id;
	
	// the actual code:
	$array = array(
				"where" => array(
					"deviceToken" => $deviceToken
					),
				"data" => array(
					"alert" => $message,
					"sound" => "default",
				)
			);

	$array = json_encode($array);
	
	$headers = array();
	$headers[] = 'X-Parse-Application-Id: ' . $applicationID;
	$headers[] = 'X-Parse-REST-API-Key: ' . $apiKey;
	$headers[] = 'Content-Type: application/json';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.parse.com/1/push");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, POST);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$server_output = curl_exec ($ch);

	curl_close ($ch);


}
/*************************************One signal Notification**********************************/
function onesignal_notification($devide_id, $message)
{

    $content = array(
      "en" => $message
      );
    
    $fields = array(
      'app_id' => "83033486-deec-4c39-8999-82af99b6a8ba",
      'include_player_ids' =>array($partner_id),
      'contents' => $content
    );
    
    $fields = json_encode($fields);
    //print("\nJSON sent:\n");
    //print($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                           'Authorization: Basic Y2UxMWYyZjItODM0Ni00MjhmLWFhMGQtYzk2Mjk3NTQ5NjA1'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;

}






/*********************************Usning Send in blue send a email with using curl**********************************/
$subject = "For Email Subject";
	$from = array("timothy@planbcapitalgroup.com","Keller Finance");
	/*******For multi  mail address use this type array********/
		if (strpos($email, ',') !== false) {
				$email_2 = explode(',',$email);
			  }
			  else{
				  $email_2 =array($email);
			  }
		foreach($email_2 as $sing_arr){
				$resi[$sing_arr] = $sing_arr;
			}
	$to = $resi;
	/*******For Single mail OR one or more emails address use this type array********/
	/*******For Single mail use this type array********/
	$to = array("enclouddev@gmail.com"=>"enclouddev@gmail.com");
    /*******For Single mail use this type array********/  	
	$bcc = array("enclouddev@gmail.com"=>"Bcc"); 	
	$html = '<html>
		<body>
			<p style="color:black;">Thank you for considering financing our equipment. Leasing is an affordable means to get the equipment your business needs right now. The process is simple so review the numbers below and if you have any questions please contact at Timothy Daum at our finance partner 1-800-511-6088.</p>
			<table width="60%">
			
			<tr><td>Equipment Value:</td><td>'.$value1.'</td></tr>
				<tr><td>Fright/Delivery Cost:</td><td>'.$value2.'</td></tr><br/>';
			
			$html .='<tr><td style="text-align:left;vertical-align:middle;border:1px solid #eee; color:#737373;padding:5px;">
									<span style="margin-left:50px">24</span><br/><br/>
									<span style="margin-left:50px">36</span><br/><br/>
									<span style="margin-left:50px">48</span><br/><br/>
									<span style="margin-left:50px">60</span>
							</td>' ;
	
	$html .= '</table>
		</body>
		</html>';
		/*************For file attachment***************/
	$attachment = array('http://planbgroup.ca/pcm/Keller_quote.pdf');
		/*************For file attachment***************/
	$data = array(
			'html' => $html,
			'to' => $to,
			'from' => $from,
			'subject' => $subject,
			'bcc' => $bcc,
			'attachment'=>$attachment,
			"headers" => array(
				"Content-Type"=> "text/html; charset=iso-8859-1",
				"X-param1"=> "value1",
				"X-param2"=> "value2",
				"X-Mailin-custom"=>"my custom value",
				"X-Mailin-IP"=> "102.102.1.2",
				"X-Mailin-Tag" => "My tag"
				),
			);

    $fields = json_encode($data);
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.sendinblue.com/v2.0/email");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                           'api-key: GNaHPqCEw82nIZDs'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($ch);
	curl_close($ch);
	$yummy = json_decode($response);
	 if($yummy->code == 'failure')
		{
		    echo '{"Status":"false","Data":[{"result":"Mail Not Sent"}]}';	
		}
		else
		{
			echo '{"Status":"true","Data":[{"result":"Mail Sent"}]}';
		}

































