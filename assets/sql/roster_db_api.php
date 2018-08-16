<?php
	require_once 'db_login.php';
	
	
	//connect to database
	$conn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);
	if (!$conn) {
		die('Failed to connect to server: ' . mysqli_connect_error());
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
			id INT(9) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			checked TINYINT(1) DEFAULT 0,
			full_name VARCHAR(40) CHARACTER SET utf8
			);";
			
		$result = mysqli_query($conn, $query);
		
		if (!$result) {
			die ("Table creation failed: " . mysqli_error($conn));
		}
	}
	
	//delete table
	function delete_table($tableName) {
		global $conn;
		
		$query = "DROP TABLE '" . $tableName . "'";
		$result = mysqli_query($conn, $query);
		
		if ($result === false) {
			die('drop unsuccessful' . mysqli_error($conn));
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
			echo "insert didn't work, bitch";
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
			echo "update didn't work, bitch<br/>";
			echo mysqli_error($conn);
		}
	}
	
?>