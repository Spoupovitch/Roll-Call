<?php
	include_once 'assets/sql/db_api.php';
	include_once 'assets/php/utils.php';
	
	session_start();
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	
	$nameOrder = $_POST['name_order'];
	$partitionStart = $_POST['name_group_start'];
	$partitionEnd = $_POST['name_group_end'];
	
	$_SESSION['team_name'] = $_POST['team_name'];
	
	$tableName = $_POST['team_name'] . '_' . $_POST['team_pass'];
	setcookie('tableName', $tableName, time() + 60 * 60 * 8);
	
	
	//ensure table does not already exist
	if (check_table($tableName) === false) {
		
		create_table($tableName);
		//establish role as team creator
		$_SESSION['team_leader'] = true;
	}
	
	//build roster from input file
	if (isset($_POST['submit'])) {
		$rosterFile = $_FILES['roster'];
		
		$rosterFileName = $rosterFile['name'];
		$rosterFileTmpName = $rosterFile['tmp_name'];
		$rosterFileSize = $rosterFile['size'];
		$rosterFileError = $rosterFile['error'];
		//$rosterFileType = $rosterFile['type'];
		
		$openFile = fopen($rosterFileName, 'r');
		//ensure file is successfully opened
		if (!is_resource($openFile)) {
			die('Failed to open selected file.');
		}
		
		//pull file extension
		$fileExtPrep = explode('.', $rosterFileName);
		$rosterFileExt = strtolower(end($fileExtPrep));
		
		if ($rosterFileError === 0) {
			
			//file size restriction
			if ($rosterFileSize < 500000) {
				
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
				
				//arrays for listed names
				$sortedBulkHi = [];
				$sortedPartition = [];
				$sortedBulkLo = [];
				
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
				
				sort($sortedBulkHi);
				sort($sortedPartition);
				sort($sortedBulkLo);
				
				$roster = array_merge($sortedBulkHi, $sortedPartition, $sortedBulkLo);
				setcookie('roster', serialize($roster), time()+60*60*8);
				
				if (isset($_SESSION['team_leader'])) {
					sort($tableInitArr);
					build_table();
				}
			}
			else {
				echo 'File size too large.';
			}
		}
		else {
			echo 'Error occured uploading file: FILES array returned 1.';
		}
	}
	else {
		echo 'File upload failed.';
	}
?>

<html>
<head>
	<link href="https://fonts.googleapis.com/css?family=Sunflower:300,700" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="assets/css/roster_style.css"/>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta charset="utf-8"/>
	
	<title>Roll Call - Your Partitioned Roster</title>
</head>
<body>

	<div class="display_page_container">
	
		<!-- sidebar -->
		<div class="options_container">
			<button class="misc_button" onclick="location.href='roster_index.html'">
				Home
			</button>
			<br/>
			
			<button class="misc_button" onclick="toggleBulk()">
				Toggle Bulk
			</button>
			<br/>
			
			<button class="misc_button" form="checked_roster">
				Compile
			</button>
		</div>
		
		
		<!-- display names in file -->
		<div class="roster_container">
			
			<p>
				<?php
					print_team();
				?>
			</p>
			
			<br/>
			
			<form id="checked_roster" method="POST" action="roster_compilation.php" name="checked_roster">
				
				<!-- display names within partition -->
				<div id="roster_partition_container">
					
					Roster Partition: <?php echo $partitionStart; ?> to <?php echo $partitionEnd; ?>
					<br/>
					
					<?php
						foreach ($sortedPartition as $currName) {
							print_name($currName);
						}
					?>
				</div>
			
				<br/>
				<hr/>
				<br/>
				
				<!-- display names outside of partition bounds -->
				<div id="roster_bulk_container">
					
					Roster Bulk
					<br/>
					
					<?php
						foreach ($sortedBulkHi as $currName) {
							print_name($currName);
						}
						if ($upperNames === 1) {
							print_break();
						}
						if ($lowerNames === 1) {
							print_break();
						}
						foreach ($sortedBulkLo as $currName) {
							print_name($currName);
						}
					?>
				</div>
				
			</form>
			
		</div><!-- end roster_container -->
	</div><!-- end display_page_container -->
	
	
	<script src="assets/js/roster_scripts.js"></script>
</body>
</html>