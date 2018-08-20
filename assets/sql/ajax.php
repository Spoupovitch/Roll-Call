<?php
	require_once 'db_api.php';
	
	ini_set('display_errors');
	error_reporting(E_ALL);


	if (isset($_POST['method'])) {
		global $conn;
		
		if (isset($_POST['method']) 
		&& isset($_POST['tableName'])) {
		
			$method = $_POST['method'];
			$tableName = $_POST['tableName'];
			
			unset($_POST['method']);
			unset($_POST['tableName']);
		}
		
		switch ($method) {
			
			//received create file command
			//create roster file, send as JSON
			case 'write_roster':
			
				$query = "SELECT *
					FROM $tableName";
					
				$result = mysqli_query($conn, $query);
				if ($result === false) {
					die("fuck");
				}
				else {
					$data = json_encode(mysqli_fetch_all($result));
					$fileName = 'Roster:' . date('M-j-Y') . '.txt';
					$fsPointer = fopen($fileName, 'w');
					$rosterFile = fwrite($fsPointer, $data);
					
					//echo $data;
					echo $fileName;
				}
				break;
			
			//received delete roster command
			//drop table
			case 'delete_table':
				//$query = "";
				
				//$result = mysqli_query($conn, $tableName);
				delete_table($tableName);
				break;
			
			
			default:
		}
	}
?>