<?php
	require_once 'db_api.php';
	session_unset();
	session_destroy();
	session_abort();


	if (isset($_POST['method'])) {
		
		if (isset($_POST['method']) 
		&& isset($_POST['tableName'])) {
		
			$method = $_POST['method'];
			$tableName = $_POST['tableName'];
			
			unset($_POST['method']);
			unset($_POST['tableName']);
		}
		
		switch ($method) {
			
			//create roster file
			case 'write_roster':
				write_roster($tableName);
				/*
				break;
			
			//drop table
			case 'delete_table':
				*/
				delete_table($tableName);
				break;
			
			
			default:
		}
	}
?>