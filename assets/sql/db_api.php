<?php
	require_once 'db_login.php';
	
	
	//connect to server
	$connServer = mysqli_connect($dbServer, $dbUsername, $dbPassword);
	if (!$connServer) {
		die('Failed to connect to server: ' . mysqli_connect_error());
	}

	//create schema
	$query = "CREATE DATABASE IF NOT EXISTS  $dbName ";
	mysqli_query($connServer, $query);

	mysqli_close($connServer);
	
	//connect to database
	$conn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);
	if (!$conn) {
		die('Failed to connect to database: ' . mysqli_connect_error());
	}

	//return name's "checked" status
	function check_name($prepName, $tableName) {
		global $conn;
		
		$query = "SELECT checked
			FROM $tableName
			WHERE full_name = '" . $prepName . "'";
		$result = mysqli_query($conn, $query);
		
		$row = $result->fetch_assoc();
		$checked = intval($row['checked']);
		
		return $checked;
	}
	
	//return whether table with input name exists
	function check_table($tableName) {
		global $conn;
		
		$query = "SHOW TABLES LIKE '" . $tableName . "'";
		$result = mysqli_query($conn, $query);
		
		if ($result === false
		|| $result->num_rows === 0) {
			return false;
		}
		else if ($result->num_rows >= 1) {
			return true;
		}
	}
	
	//create table with dynamic name for each group
	function create_table($tableName) {
		global $conn;
		
		$query = "CREATE TABLE $tableName (
			checked TINYINT(1) DEFAULT 0,
			full_name VARCHAR(40) CHARACTER SET utf8,
			PRIMARY KEY (full_name)
			);";
			
		$result = mysqli_query($conn, $query);
		
		if (!$result) {
			die ("Table creation failed: " . mysqli_error($conn));
		}
	}
	
	//delete table
	function delete_table($tableName) {
		global $conn;
		
		$query = "DROP TABLE $tableName ";
		$result = mysqli_query($conn, $query);
		
		if ($result === false) {
			die('drop table FAILED: ' . mysqli_error($conn));
			return false;
		}
		else if($result->num_rows === 1) {
			return true;
		}
	}
	
	//populate table with rows for each name
	function init_roster($tableName, $full_name) {
		global $conn;
		
		$query = "INSERT INTO $tableName (checked, full_name)
			VALUES (0, '" . $full_name . "');";
		
		$result = mysqli_query($conn, $query);
		if ($result === false) {
			
		}
	}
	
	//update table with checked names
	function update_roster($tableName, $full_name) {
		global $conn;
		
		$query = "UPDATE $tableName 
			SET checked = 1
			WHERE full_name = '" . $full_name . "';";
		
		$result = mysqli_query($conn, $query);
		if ($result === false) {
			echo mysqli_error($conn);
		}
	}
	
	//write roster data to file
	function write_roster($tableName) {
		global $conn;
		
		$query = "SELECT *
			FROM $tableName";
			
		$result = mysqli_query($conn, $query);
		if ($result === false) {
			die("Roster file write FAILED");
		}
		else {
			$data = json_encode(mysqli_fetch_all($result), JSON_PRETTY_PRINT);
			
			//create rosters folder if necessary
			if (!file_exists('../../rosters/')) {
				mkdir('../../rosters/', 0777, false);
			}
			if (!file_exists('../../rosters/')) {
				//place file in root dir if folder creation fails
				$filePathName = '../../Roster_' . date('M-j-Y_H-i') . '.txt';
				$rosterFile = file_put_contents($filePathName, $data);
			}
			else {
				//place file in folder upon successful creation or prior existence
				$filePathName = '../../rosters/Roster_' . date('M-j-Y_H-i') . '.txt';
				$rosterFile = file_put_contents($filePathName, $data);
			}
		}
	}
?>