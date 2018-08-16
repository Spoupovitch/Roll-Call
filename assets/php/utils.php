<?php
	include_once 'assets/sql/roster_db_api.php';

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
		preg_match("/([a-zA-Z]*)(\W*\s*,\s*\W*_*)([a-zA-Z]*(\W*))/", $unprepName, $regexArr);
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
			echo "<div class='line_name'>";
				echo "<div>";
					echo "<input type=\"checkbox\" checked=\"checked\" disabled=\"disabled\" />";
				echo "</div>";
				
				echo "<div>";
					echo "$unprepName<br/>";
				echo "</div>";
			echo "</div>";
		}
		else if ($checked === 0) {
			echo "<div class='line_name'>";
				echo "<div>";
					echo "<input type=\"checkbox\" name=\"$unprepName\" />";
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
	
	function print_team() {
		global $team_leader;
		global $team_member;
		
		if (isset($team_member)) {
			echo "Team: " . $_POST['team_name'] .
				"<br/>" .
				"Role: Member";
		}
		else if (isset($team_leader)) {
			echo "Team: " . $_POST['team_name'] .
				"<br/>" .
				"Role: Lead";
		}
		else {
			echo "Error: No team status found" .
				"<br/>";
		}
	}
	
?>