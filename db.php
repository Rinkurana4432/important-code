<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$link = mysqli_connect("localhost", "zsvvcifs_user", "85baTVIFKmgr", "zsvvcifs_live");
if (empty($_SERVER['HTTPS'])) {
	$home_url = 'http://'.$_SERVER['HTTP_HOST'];
}else{
	$home_url = 'https://'.$_SERVER['HTTP_HOST'];
}
define ('HOMEURL',$home_url.'/');
define ('HOMEPATH',$_SERVER['DOCUMENT_ROOT']);
define ('EMAILTITLE','Linkwell Technologies Customer Portal');
define ('EMAILSEMAIL','support@linkwell.ae');
define ('EMAILSPHONE','+97155 2243488');
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

function add_auditlog($data_arr){
	//Set logs - start
	if(is_array($data_arr) && count($data_arr) > 0){
		$to_print = "Date: ".date("F j, Y, g a")."\r\n";
		foreach($data_arr as $key=>$value){
			$to_print .= $key." : ".$value."\r\n";
		}
		$to_print .= '*****************************************'."\r\n";
		//Save string to log, use FILE_APPEND to append.
		file_put_contents('debubLog.txt', $to_print, FILE_APPEND); 
	}
	//Set logs - end
}
// $data_arrt = array('Name'=>'abc','Class'=>'Test');
// add_auditlog($data_arrt);
// $data_arrt = array('Log Type'=>'Client Deleted','Deleted by'=>$current_username.'('.$user_id.')','Deleted Client Name'=>$name22['fname']);
// add_auditlog($data_arrt);//USING THIS WE CAN ADD DATA IN  debubLog.txt fiel

function currenturl(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}



require_once('PHPMailer_v5.1/class.phpmailer.php');
require_once('PHPMailer_v5.1/class.smtp.php');
require_once('PHPMailer_v5.1/PHPMailerAutoload.php');

$mail = new PHPMailer;
$mail->SMTPDebug = 0; 

$mail->IsSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'server3.apps.ae';                 // Specify main and backup server
$mail->Port = 587;                                    // Set the SMTP port
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'notifications@linkwellportal.ae';                // SMTP username
$mail->Password = '0.sWmE;.IzFY';                  // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

$mail->From = 'notifications@linkwellportal.ae';
$mail->FromName = 'LinkWell';