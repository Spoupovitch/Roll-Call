<?php
	$nameOrder = $_POST['name_order'];
	
	$partitionStart = $_POST['name_group_start'];
	$partitionEnd = $_POST['name_group_end'];
	
	if (isset($_POST['submit'])) {
		$rosterFile = $_FILES['roster'];
		
		$rosterFileName = $rosterFile['name'];
		$rosterFileTmpName = $rosterFile['tmp_name'];
		$rosterFileSize = $rosterFile['size'];
		$rosterFileError = $rosterFile['error'];
		$rosterFileType = $rosterFile['type'];
		
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
							$currName = trim($currName, "\n\0");
							//break name apart using regex
							preg_match('/(.*) (.*)/', $currName, $fullNames);
							//remove leading/trailing whitespace, add new line
							$fullNames[1] = trim($fullNames[1], "\0") . "\n";
							//swap name places, insert comma, enter into roster
							allocate_names($fullNames[2] . ', ' . $fullNames[1]);
						}
					}
				}
				//handle Last, First order
				else if ($nameOrder === 'last') {
					while ($currName = fgets($openFile)) {
						
						//filter empty lines and whitespaces
						if (!($currName === '$')
						&& !($currName === '\s*')) {
						
							allocate_names($currName);
						}
					}
				}
				
				sort($sortedBulkHi);
				sort($sortedPartition);
				sort($sortedBulkLo);
				
				fclose($openFile);
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

<?php
	function allocate_names($currName) {
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
	
	//print names w checkbox for each
	function print_name($name) {
		echo "<div class='line_name'>";
		
			echo "<div>";
				echo "<input type=\"checkbox\" name=\"$name\" value=\"$name\"/>";
			echo "</div>";
			
			echo "<div>";
				echo "$name<br/>";
			echo "</div>";
		
		echo "</div>";
	}
	
	//empty row for excluded name
	function print_break() {
		echo "<div class='line_break'>";
			
			echo "<div>";
				echo "...<br/>";
			echo "</div>";

		echo "</div>";
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
		
		<!-- display names in file -->
		<div id="roster_container">
			
			<form method="POST" action="roster_compilation.php" name="checked_partition">
				
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
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="assets/js/roster_scripts.js"></script>
	
</body>
</html>