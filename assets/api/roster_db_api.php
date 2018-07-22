<?php
	include 'db_login.php';
	
	
	//return count of tables with input name
	function check_table($tableName, $conn) {
		/*
		global $dbUsername;

		$query = "SELECT count(*)
			FROM information_schema.tables
			WHERE table_schema = $dbUsername
			AND table_name = $tableName";
		
		$query = "SHOW TABLES LIKE $tableName";
		$result = mysqli_query($conn, $query);
		
		if ($result === false) {
			echo "false<br/>";
		}
		else if (mysqli_num_rows($result) === 1) {
			echo 
				"<span style='color:red; font-size:1.5em;'>" .
				"Table already exists: " . mysqli_error($conn) .
				"</span>" .
				"<br/>";
		}
		*/
		
		if ($result = $conn->query("SHOW TABLES LIKE '" . $tableName . "'")) {
			if($result->num_rows == 1) {
				return true;
			}
		}
		else {
			return false;
		}
	}

	//create table with dynamic name for each group
	function create_table($tableName, $conn){
		
		$query = "CREATE TABLE IF NOT EXISTS $tableName (
			checked TINYINT(1) DEFAULT 0,
			full_name VARCHAR(40) CHARACTER SET utf8
			);";
			
		$result = mysqli_query($conn, $query);
		
		
		
		if (!$result) {
			die ("Table creation failed: " . mysqli_error($conn));
		}
	}
	
	//add names to table for compilation
	function add_to_roster($tableName, $conn, $checked, $full_name) {
		$query = "INSERT INTO $tableName
			VALUES ('$checked', '$full_name');";
		
		$result = mysqli_query($tableName, $conn);
	}
	
?>