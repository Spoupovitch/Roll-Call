<?php
	include_once 'assets/sql/db_api.php';

	//allocate names into appropriate array depending on given partition
	function allocate_name($currName) {
		global $sortedBulkHi, $sortedPartition, $sortedBulkLo;
		global $partitionStart, $upperNames, $partitionEnd, $lowerNames;
		
		//name prior to partition
		if (strncmp(ucfirst($currName), $partitionStart, 1) < 0) {
			$upperNames = 1;
			$sortedBulkHi[] = $currName;
		}
		//name in partition bounds
		else if (strncmp(ucfirst($currName), $partitionStart, 1) >= 0
		&& strncmp(ucfirst($currName), $partitionEnd, 1) <= 0) {
			
			$sortedPartition[] = $currName;
		}
		//name subsequent to partition
		else if (strncmp(ucfirst($currName), $partitionEnd, 1) > 0) {
			$lowerNames = 1;
			$sortedBulkLo[] = $currName;
		}
	}
	
	//set roster as cookie
	function build_table() {
		global $tableInitArr;
		global $tableName;
		
		foreach ($tableInitArr as $currName) {
			init_roster($tableName, $currName);
		}
	}
	
	//prepare name for database comparison
	//occurs subsequent to entry into database
	function prepare_name($unprepName) {
		preg_match("/([a-zA-Z]*)(\W*\s*,_*\s*\W*)([a-zA-Z]*(\W*))/", $unprepName, $regexArr);
		trim($regexArr[1]);
		trim($regexArr[3]);
		return $regexArr[1] . $regexArr[3];
	}
	
	//empty row for excluded name
	function print_break() {
		echo "<div class='line_break'>";
			
			echo "<div>";
				echo "...<br/>";
			echo "</div>";
		
		echo "</div>";
	}
	
	//print names w checkbox for each
	function print_name($name) {
		echo "<div class='line_name'>";
		
			echo "<div>";
				echo "<input type=\"checkbox\" name=\"$name\" value=\"true\" />";
			echo "</div>";
			
			echo "<div>";
				echo "$name<br/>";
			echo "</div>";
		
		echo "</div>";
	}
	
	//show checked and unchecked names according to current table
	function print_status($unprepName, $tableName) {
		
		//prepare name for database query
		$prepName = prepare_name($unprepName);
		//catch value of checked column
		$checked = check_name($prepName, $tableName);
		
		if ($checked === 1) {
			echo "<div class='line_name checked_name'>";
			
				echo "<div>";
					echo "<input type=\"checkbox\" name=\"$unprepName\" disabled=\"disabled\" checked=\"checked\" />";
				echo "</div>";
				
				echo "<div>";
					echo "$unprepName<br/>";
				echo "</div>";
				
			echo "</div>";
		}
		else if ($checked === 0) {
			echo "<div class='line_name unchecked_name'>";
			
				echo "<div>";
					echo "<input type=\"checkbox\" name=\"$unprepName\" disabled=\"disabled\" />";
				echo "</div>";
				
				echo "<div>";
					echo "$unprepName<br/>";
				echo "</div>";
				
			echo "</div>";
		}
		else {
			echo "Error: checked value not found <br/>";
		}
	}
	
	//show team name and role
	function print_team() {
		if (isset($_SESSION['team_leader'])) {
			echo "Team: " . $_SESSION['team_name'] .
				"<br/>" .
				"Role: Lead";
		}
		else {
			echo "Team: " . $_SESSION['team_name'] .
				"<br/>" .
				"Role: Member";
		}
	}
	
?>