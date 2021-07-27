<?php
	define('_DB_HOST_NAME','localhost');
	define('_DB_USER_NAME','root');
	define('_DB_PASSWORD','');
	define('_DB_DATABASE_NAME','newone');
	
	$dbConnection = mysqli_connect(_DB_HOST_NAME,_DB_USER_NAME,_DB_PASSWORD,_DB_DATABASE_NAME);


	if(isset($_POST['submit'])){
		
		
		if($_FILES['csv_data']['name']){
			
			$arrFileName = explode('.',$_FILES['csv_data']['name']);
			// echo'<pre>';
		// print_r($arrFileName);
		// echo'</pre>';die('adsf');
			if($arrFileName[1] == 'csv'){

				$handle = fopen($_FILES['csv_data']['tmp_name'], "r");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

					//$id = mysqli_real_escape_string($dbConnection,$data[0]);
					$name = mysqli_real_escape_string($dbConnection,$data[1]);
					$email = mysqli_real_escape_string($dbConnection,$data[2]);
					$address = mysqli_real_escape_string($dbConnection,$data[3]);
					$status = mysqli_real_escape_string($dbConnection,$data[4]);
					$password = mysqli_real_escape_string($dbConnection,$data[5]);
					//$no_of_employees = mysqli_real_escape_string($dbConnection,$data[6]);
					//$turnover = mysqli_real_escape_string($dbConnection,$data[7]);
					//$import="INSERT into pp_old_data(employer_name,location,industry,contact_person,contact_no,email_id,no_of_employees,turnover) values('$employer_name','$location','$industry','$contact_person','$contact_no','$email_id','$no_of_employees','$turnover')";
					 $import = "INSERT INTO `tbl_users`(`name`, `email`, `address`, `status`, `password`) VALUES ('$name',
					'$email','$address','$status','$password')";
					mysqli_query($dbConnection,$import);
				}

				fclose($handle);
				print "Import done";
			}
		}
	}
?>
<html>
	<head>
		<title>Stepblogging :: Upload CSV and Insert into Database Using PHP</title>
	<head>
	<body>
		<form method='POST' enctype='multipart/form-data'>
			Upload CSV: <input type='file' name='csv_data' /> <input type='submit' name='submit' value='import' />
		</form>
	</body>
</html>