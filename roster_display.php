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
	
	if (!isset($_POST['submit'])) {
		echo 'File upload failed.';
	}

	//build roster from input file
	$rosterFile = $_FILES['roster'];
	
	$rosterFileName = $rosterFile['name'];
	$rosterFileTmpName = $rosterFile['tmp_name'];
	$rosterFileSize = $rosterFile['size'];
	$rosterFileError = $rosterFile['error'];
	//$rosterFileType = $rosterFile['type'];
	
	//file error encountered
	if ($rosterFileError !== 0) {
		die('Error occured uploading file. Error code: ' . $rosterFileError);
	}

	//file size restriction
	if ($rosterFileSize > 500000) {
		die('File size too large.');
	}

	$openFile = fopen($rosterFileTmpName, 'r');
	//ensure file is successfully opened
	if (!is_resource($openFile)) {
		die('Failed to open selected file.');
	}
	
	//arrays for listed names
	$sortedBulkHi = [];
	$sortedPartition = [];
	$sortedBulkLo = [];
	$tableInitArr = [];
	
	parse_file($openFile, $rosterFileName, $rosterFileTmpName, $nameOrder, $partitionStart, $partitionEnd);
	
	sort($sortedBulkHi);
	sort($sortedPartition);
	sort($sortedBulkLo);
	
	$roster = array_merge($sortedBulkHi, $sortedPartition, $sortedBulkLo);
	setcookie('roster', serialize($roster), time()+60*60*8);
	
	if (isset($_SESSION['team_leader'])) {
		sort($tableInitArr);
		build_table();
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
		<div class="sidebar">
			<button class="misc_button" onclick="location.href='index.html'">
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