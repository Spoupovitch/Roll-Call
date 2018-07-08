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
				$rosterNameDynam = 'Prttn-' . $partitionStart . $partitionEnd . '-' . uniqid('', true) . '.' . $rosterFileExt;
				
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
				
				//array for listed names
				$sortedRoster = [];
				
				//handle first, last order
				if ($nameOrder === 'first') {
					while ($currName = fgets($openFile)) {
						//filter empty lines and whitespaces
						if (!($currName === '$')
						&& !($currName === '\s*')) {
							
							//break name apart using regex
							preg_match('/(.*) (.*)/', $currName, $fullNames);
							//swap name places, insert comma, enter into roster
							$sortedRoster[] = $fullNames[2] . ', ' . $fullNames[1];
						}
					}
				}
				//handle last, first order
				else if ($nameOrder === 'last') {
					while ($currName = fgets($openFile)) {
						//filter empty lines and whitespaces
						if (!($currName === '$')
						&& !($currName === '\s*')) {
						
							$sortedRoster[] = $currName;
						}
					}
				}
				//sort names in roster
				sort($sortedRoster);
				
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
	//print names w checkbox for each
	function print_name($name) {
		echo "<tr>";
		
			echo "<td>";
				echo "<input type=\"checkbox\" name=\"$name\" value=\"$name\"/>";
			echo "</td>";
			
			echo "<td>";
				echo "$name<br/>";
			echo "</td>";
		
		echo "</tr>";
	}
	
	//empty row for excluded name
	function print_break() {
		echo "<tr>";
			
			echo "<td>";
			echo "</td>";
			
			echo "<td>";
				echo "...<br/>";
			echo "</td>";

		echo "</tr>";
	}
?>

<html>
<head>
	<link href="https://fonts.googleapis.com/css?family=Sunflower:300,700" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="roster_style.css"/>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />
	<title>Roll Call - Your Partitioned Roster</title>
</head>
<body>

	<div id="display_page_container">
	
		<div id="options_container">
			<button class="misc_button">
				Home
			</button>
			<br/>
			<button class="misc_button">
				Hide Bulk
			</button>
			<br/>
			<button class="misc_button">
				Compile
			</button>
			
		</div>
		
		<div id="roster_container">
			<!--  -->
			<div id="roster_partition_container">
				
				Roster Partition: <?php echo $partitionStart; ?> to <?php echo $partitionEnd; ?>
				
				<form method="POST" action="" name="checked_partition">
					<table id="partition_table">
						<?php
							
							$upperNames = 0;
							$lowerNames = 0;
							
							//display requested partition
							foreach ($sortedRoster as $currName) {
								
								//only display names in given bounds
								if (strncmp(ucfirst($currName), $partitionStart, 1) >= 0
								&& strncmp(ucfirst($currName), $partitionEnd, 1) <= 0) {
									
									print_name($currName);
								}
								else if (strncmp(ucfirst($currName), $partitionEnd, 1) > 0) {
									$lowerNames = 1;
								}
							}
						?>
					</table>
				</form>
			</div>
			
			<br/>
			<hr/>
			<br/>
			
			<!--  -->
			<div id="roster_bulk_container">
				
				Roster Bulk
				
				<form method="POST" action="" name="checked_bulk">
					<table id="bulk_table">
						<?php

							//display remainder of roster
							foreach ($sortedRoster as $currName) {
								
								//only display names outside of given bounds
								if (strncmp(ucfirst($currName), $partitionStart, 1) < 0) {
									$upperNames = 1;
									print_name($currName);
								}
								else if (strncmp(ucfirst($currName), $partitionEnd, 1) > 0) {
									//print ellipse for preceding partitioned names
									if ($lowerNames === 1) {
										$lowerNames = 0;
										print_break();
									}
									print_name($currName);
								}
								//print ellipse for subsequent partitioned names
								else if ($upperNames === 1) {
									$upperNames = 0;
									print_break();
								}
							}
						?>
					</table>
				</form>
			</div>
			
		</div><!-- end roster_container -->
	</div><!-- end display_page_container -->
	
	<script src="roster_scritps.js"></script>
	
</body>
</html>










