<?php
// $conn = mysql_connect("localhost","root","");
// mysql_select_db("phppot_examples",$conn);


$filename = "transactiondetail_csv.csv";
$fp = fopen('php://output', 'w');

$header = array('created_date','name','receiver_user_id','email','cardnumber','amount','transaction_notes');

$auth_code = $_REQUEST['auth_code'];
//$auth_code = 'b337e84de8752b27eda3a12363109e80'; 
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
fputcsv($fp, $header);

	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://s3cur3.com/admin/webservices/index.php/data/transactionhistroy?");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
					"auth_code=$auth_code&offset=0&limit=100");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec ($ch);
		$result_final = json_decode($response);
		curl_close ($ch);
		
	
		if($result_final->Status == 'true' )
		{
			foreach ($result_final->Data as $val)
			{
				$ddate = explode(' ',$val->created_date);
				$aa['created_date'] = $ddate[0];
				$aa['name'] = $val->name;
				$aa['receiver_user_id'] = $val->receiver_user_id;
				$aa['email'] = $val->email;
				$aa['cardnumber'] = $val->cardnumber;
				$aa['amount'] = $val->amount;
				$aa['transaction_notes'] = $val->transaction_notes;
				
				fputcsv($fp, $aa);

			}
		}	
		
			
			
			
exit();
?>