<?php
/*******************************Code for chat API [START]*******************************/
	
	//Code to send messages to one to one person
	public function send_message(){  
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		//date_default_timezone_set('Asia/Kolkata');

		$sender_id = $data->sender_id;
		$sender_name = get_user_meta( $sender_id, 'first_name', true );
		$receiver_id = $data->receiver_id;
		// if($receiver_id == '0'){
			 // $receiver_id = 1;
			 // $receiver_id;
		// }
		
		
		//$receiver_id = '1';
		
		$msg = $data->message;
		$time = time();
		//echo date('h:i A',$time);die();

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'sng_messages';

		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			sender_id int(11) NOT NULL,
			receiver_id int(11) NOT NULL,
			messages text NOT NULL,
			time varchar(225) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		if($sender_id == ''){
			$message = $this->_requestStatus("1801");
			return array(
				"RespCode" => "1801",
				"Message" => $message,
			);
		}
		if($receiver_id == ''){
			$message = $this->_requestStatus("1802");
			return array(
				"RespCode" => "1802",
				"Message" => $message,
			);
		}
		if($msg == ''){
			$message = $this->_requestStatus("1803");
			return array(
				"RespCode" => "1803",
				"Message" => $message,
			);
		}
		$data = array(
			'sender_id' => $sender_id,
			'receiver_id' => $receiver_id,
			'messages' => $msg,
			'time' => $time
		);
		


		if($wpdb->insert($table_name, $data)){
			//Code to send chat notification [START]
			$devicetype = get_user_meta( (int)$receiver_id, 'devicetype', true);
			$devicetoken = get_user_meta( (int)$receiver_id, 'devicetoken', true);
			$badge = get_user_meta((int)$receiver_id,'badge',true);
			$datax = array(
				'message'   => $msg,
				'title'     => 'New message received',
				'vibrate'   => 1,
				'sound'     => 1,
				'type'		=> 'chat'
			);
			if($devicetoken != ''){
				if($devicetype != 'I'){
					if($badge == 0 || $badge == ''){
						$badge = 1;
					}else{ 
						$badge = $badge +1;
					}
					$datax['badge'] = $badge;
					$datax['sender_id'] = $sender_id;
					$datax['receiver_id'] = $receiver_id;
					update_user_meta((int)$receiver_id, 'badge', $badge);
					
					$message =array (
						'body'   => 'New Message Received',
						'title'     => 'New Message'
					);

					$fields = array(
						'to' =>  $devicetoken,
						'notification' => $message,
						'data' => $datax,
					);
					$result = $this->send_android_notification($fields);
				}else{
					
					$datax['sender_id'] = $sender_id;
					$datax['receiver_id'] = $receiver_id;
					$datax['sender_name'] = $sender_name;
					
					$datax['time'] = $time;
					$datax['format_date'] = date('d-m-Y',$time);
					$datax['format_time'] = date('h:i A',$time);
					$datax['message_id'] = $wpdb->insert_id;
					
					// Put your alert message here:
					$message = $datax;
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta((int)$receiver_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'You have a new message',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'Chat',
							'Data' => $message
						);
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'You have a new message',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'Chat',
							'Data' => $message
						);
						update_user_meta((int)$receiver_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($datax,$devicetoken,$body);
				}
			}
			//Code to send chat notification [END]
			
			$message = $this->_requestStatus("1804");
			return array(
				"RespCode" => "1804",
				"Message" => $message,
				"Details" => array(
					'id'=> $wpdb->insert_id,
					'sender_id' => $sender_id,
					'receiver_id' => $receiver_id,
					'messages' => $msg,
					'time' => $time,
					'format_date'=>date('d-m-Y',$time) ,
					'format_time' =>date('h:i A',$time)
				)
			);
		} else {
			$message = $this->_requestStatus("1007");
			return array(
				"RespCode" => "1007",
				"Message" => $message,
			);
		}
	}
	
	
	//Get user's all chats
	public function get_chat(){  
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id; 
		
		$chats = $wpdb->get_results("select * from `".$wpdb->prefix."sng_messages` where id in(select max(id) from (SELECT sender_id as cid,receiver_id as sid,id FROM `".$wpdb->prefix."sng_messages` where sender_id='".$user_id."' union SELECT receiver_id as cid,sender_id as sid,id FROM `".$wpdb->prefix."sng_messages` where receiver_id='".$user_id."') temp group by cid,sid)");

		$count = count($chats) / 2; 
		$j = 0; 
		if($chats){
			foreach($chats as $chat){ 
				//if($j >= $count) { $chats[$j]->shown = 0; }else{$chats[$j]->shown = 1; }
				if($user_id == $chat->sender_id){
					$_id = $chat->receiver_id;
				}elseif($user_id == $chat->receiver_id){
					$_id = $chat->sender_id;
				}
				
				$receiver_name = get_user_meta($_id,'first_name',true);
				$receiver_image = get_user_meta($_id,'cupp_upload_meta',true);
			
				
				$chats[$j]->format_time = date('h:i A',$chat->time);
				$chats[$j]->format_date = date('d-m-Y',$chat->time);
				$chats[$j]->receiver_name = $receiver_name;
				$chats[$j]->receiver_image = $receiver_image;
				$chats[$j]->final_receiver = $_id;
				$j++;
			}

			$message = $this->_requestStatus("1806");
			return array(
				"RespCode" => "1806",
				"Message" => $message,
				"chats" => $chats
			);
		}else{
			$message = $this->_requestStatus("1805");
			return array(
				"RespCode" => "1805",
				"Message" => $message,
			);
		}
	}
	
	//Get specific chat's all messages 
	public function get_chat_messages(){   
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		
		$sender_id = $data->sender_id;
		$receiver_id = $data->receiver_id;
		// if($receiver_id == '0'){
			 // $receiver_id = 1;
			 // $receiver_id;
		// }
		
		$sent_msgs = $wpdb->get_results("select * from ".$wpdb->prefix."sng_messages where sender_id = $sender_id && receiver_id='$receiver_id'");
		$received_msgs = $wpdb->get_results("select * from ".$wpdb->prefix."sng_messages where sender_id = $receiver_id && receiver_id='$sender_id'");
		$full_chat = array_merge($sent_msgs, $received_msgs);
		usort($full_chat, function($a, $b) { 
			return $a->id - $b->id;
		});
		
		$j = 0;
		foreach($full_chat as $chat){ 
			$full_chat[$j]->format_time = date('h:i A',$chat->time);
			$full_chat[$j]->format_date = date('d-m-Y',$chat->time);
			$j++;
		}
		if(!empty($full_chat)){
			$message = $this->_requestStatus("1806");
			return array(
				"RespCode" => "1806",
				"Message" => $message,
				"chats" => $full_chat
			);
		} else {
			$message = $this->_requestStatus("1805");
			return array(
				"RespCode" => "1805",
				"Message" => $message,
			);
		}
	}
	
	//Get last/latest message
	public function get_last_message(){   
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$sender_id = $data->sender_id;
		$receiver_id = $data->receiver_id;
		// if($receiver_id == '0'){
			 // $receiver_id = 1;
			 // $receiver_id;
		// }

		$current_time = time();
		$current_time_before = time() - 10;

		//$last_message = $wpdb->get_results("select * from ".$wpdb->prefix."sng_messages where (sender_id='$sender_id' || sender_id='$receiver_id') && (receiver_id='$receiver_id' || receiver_id='$sender_id') && time >= '$current_time_before'");
		$last_message = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sng_messages WHERE `sender_id` = $sender_id AND `receiver_id` = $receiver_id ORDER BY `ID` DESC LIMIT 1 ");
	
		$j = 0;
		foreach($last_message as $chat){ 			
			$last_message[$j]->format_time = date('h:i A',$chat->time);
			$last_message[$j]->format_date = date('d-m-Y',$chat->time);
			$j++;
		}
		
		if(!empty($last_message)){
			$message = $this->_requestStatus("1806");
			return array(
				"RespCode" => "1806",
				"Message" => $message,
				"chats" => $last_message
			);
		} else {
			$message = $this->_requestStatus("1805");
			return array(
				"RespCode" => "1805",
				"Message" => $message,
			);
		}
	}

/*******************************  Code for chat API [end] *******************************/