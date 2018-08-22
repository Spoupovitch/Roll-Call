<?php
	include_once 'assets/sql/db_api.php';
	include_once 'assets/php/utils.php';
	
	session_start();
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	
	$tableName = $_COOKIE['tableName'];
	$roster = unserialize($_COOKIE['roster']);
	$checked = array_keys($_POST);
	
	if (isset($_SESSION['team_leader'])) {
		$lead = true;
	}
	else {
		$lead = false;
	}
	
	
	foreach ($checked as $currName) {
		//break name apart using regex
		$fullName = prepare_name($currName);
		//record checked names in table
		update_roster($tableName, $fullName);
	}
?>

<html>
<head>
	<link href="https://fonts.googleapis.com/css?family=Sunflower:300,700" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="assets/css/roster_style.css"/>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta charset="utf-8"/>
	
	<title>Roll Call - Compiling Rosters</title>
</head>
<body>

	<div id="display_page_container">
	
		<!-- sidebar -->
		<div id="options_container">
			<button class="misc_button" onclick="location.href='roster_index.html'">
				Home
			</button>
			<br/>
			
			<button class="misc_button" onclick="placeholder()">
				Hide Bulk
			</button>
			<br/>
			
			<button class="misc_button" onclick="confirmCreateFile( '<?php echo $tableName ?>', '<?php echo $lead ?>' )">
				Download
			</button>
		</div>
		
		<!-- display checked and unchecked names -->
		<div id="roster_container">
			<p>
				<?php
					print_team();
				?>
			</p>
			
			<?php
				foreach ($roster as $currName) {
					print_status($currName, $tableName);
				}
			?>
		</div>
		
	</div>
	
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="assets/js/roster_scripts.js"></script>
</body>
</html>