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
				"Role: Team Lead";
		}
		else {
			echo "Team: " . $_SESSION['team_name'] .
				"<br/>" .
				"Role: Team Member";
		}
	}

	//pull names from input file and separate
	function parse_file($openFile, $rosterFileName, $rosterFileTmpName, $nameOrder, 
			$partitionStart, $partitionEnd) {
		
		global $sortedBulkHi;
		global $sortedPartition;
		global $sortedBulkLo;
		global $tableInitArr;

		//pull file extension
		$fileExtPrep = explode('.', $rosterFileName);
		$rosterFileExt = strtolower(end($fileExtPrep));
		
		//generate unique name for file upload
		$rosterNameDynam = uniqid('Prttn-' . $partitionStart . $partitionEnd . '-', true) . '.' . $rosterFileExt;
		
		//create uploads folder if necessary
		if (!file_exists('uploads/')) {
			mkdir('uploads/', 0777, false);
			
			if (!file_exists('uploads/')) {
				die('Failed to create "uploads" directory');
			}
		}
		
		//clear uploads folder
		$uploadFiles = glob('uploads/Prttn*');
		if (count($uploadFiles) > 100) {
			foreach ($uploadFiles as $file) {
				unlink($file);
			}
		}
		
		//place file in uploads/
		$fileDestination = 'uploads/' . $rosterNameDynam;
		move_uploaded_file($rosterFileTmpName, $fileDestination);
		
		//flag names above/below partition
		$upperNames = 0;
		$lowerNames = 0;
		
		//handle First, Last order
		if ($nameOrder === 'first') {
			while ($currName = fgets($openFile)) {
				
				//filter empty lines and whitespaces
				if (!($currName === '$')
				&& !($currName === '\s*')) {
					
					//remove leading/trailing whitespace
					$currName = trim($currName);
					//break name apart using regex, prepare for table
					preg_match("/([a-zA-Z]*)(\W\s*,*\s*\W*_*)([a-zA-Z]*)(\W*)/", $currName, $regexArr);
					trim($regexArr[3]);
					trim($regexArr[1]);
					//swap name places, insert comma, enter into roster
					allocate_name($regexArr[3] . ', ' . $regexArr[1]);
					
					if (isset($_SESSION['team_leader'])) {
						$tableInitArr[] = $regexArr[3] . $regexArr[1];
					}
				}
			}
		}
		//handle Last, First order
		else if ($nameOrder === 'last') {
			while ($currName = fgets($openFile)) {
				
				//filter empty lines and whitespaces
				if (!($currName === '$')
				&& !($currName === '\s*')) {
					
					allocate_name($currName);
					
					if (isset($_SESSION['team_leader'])) {
						//break name apart using regex, prepare for table
						$tableInitArr[] = prepare_name($currName);
					}
				}
			}
		}
		fclose($openFile);
	}
	
?>