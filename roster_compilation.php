<?php
	include 'assets/api/roster_db_api.php';
	
	$names = json_decode($_COOKIE['roster']);
	echo $names;
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
	
		<!-- navbar -->
		<div id="options_container">
			<button class="misc_button" onclick="location.href='roster_index.html'">
				Home
			</button>
			<br/>
			
			<button class="misc_button" onclick="placeholder()">
				Hide Bulk
			</button>
			<br/>
			
			<button class="misc_button" form="checked_partition" type="submit" onclick="compileRosters()">
				Compile
			</button>
		</div>
		
		<!--  -->
		<div>
			<?php
				print_r($_POST);
			/*
				foreach ($names as $currName) {
					add_to_roster($_COOKIE['tableName'], $conn, true, $currName);
				}
			*/
			?>
		</div>
		
	</div>
	
	<script src="assets/js/roster_scripts.js"></script>
</body>
</html>