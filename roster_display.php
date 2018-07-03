<?php
	$inputNameOrder = $_POST['name_order'];
	
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
		
		//pull file extension
		$fileExtPrep = explode('.', $rosterFileName);
		$rosterFileExt = strtolower(end($fileExtPrep));
		
		if ($rosterFileError === 0) {
			
			//file size restriction
			if ($rosterFileSize < 500000) {
				
				//generate unique name for file upload
				$rosterNameDynam = uniqid('', true) . '.' . $rosterFileExt;
				
				//place file in uploads/
				$fileDestination = 'uploads/' . $rosterNameDynam;
				move_uploaded_file($rosterFileTmpName, $fileDestination);
				
				//sort names in roster
				$sortedRoster = [];
				while ($currName = fgets($openFile)) {
					$sortedRoster[] = $currName;
				}
				sort($sortedRoster);
				
				fclose($openFile);
			}
			else {
				echo "File size too large.";
			}
		}
		else {
			echo "Error occured uploading file: FILES array returned 1.";
		}
	}
	else {
		echo "File upload failed.";
	}
?>

<?php
	function print_line($name) {
		echo "<tr>";
		
			echo "<td>";
				echo "<input type=\"checkbox\" name=\"$name\" value=\"$name\"/>";
			echo "</td>";
			
			echo "<td>";
				echo "$name <br/>";
			echo "</td>";
		
		echo "</tr>";
	}
?>

<html>
<head>
	<link href="https://fonts.googleapis.com/css?family=Sunflower:300,700" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="roster_style.css"/>
	<title>Roll Call - Your Partitioned Roster</title>
</head>
<body>

	<div id="display_page_container">
		
		<div id="roster_container">
			<!--  -->
			<div id="roster_partition_container">
				
				Partition: <?php echo $partitionStart; ?> to <?php echo $partitionEnd; ?>
				
				<form method="POST" action="">
					<table id="partition_table">
						<?php
							
							//display requested partition
							foreach ($sortedRoster as $currName) {
								
								//only display names in given bounds
								if (strncmp(ucfirst($currName), $partitionStart, 1) >= 0
								&& strncmp(ucfirst($currName), $partitionEnd, 1) <= 0) {
									
									print_line($currName);
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
				
				<form method="POST" action="">
					<table id="bulk_table">
						<?php

							//display remainder of roster
							foreach ($sortedRoster as $currName) {
								
								//only display names outside of given bounds
								if (strncmp(ucfirst($currName), $partitionStart, 1) < 0
								|| strncmp(ucfirst($currName), $partitionEnd, 1) > 0) {
									
									print_line($currName);
								}
								else if (strncmp(ucfirst($currName), $partitionStart, 1) === 0) {
									if (strncmp('A', $partitionStart, 1) !== 0) {
										echo '...<br/>';
									}
								}
								else if (strncmp(ucfirst($currName), $partitionEnd, 1) === 0) {
									if (strncmp('Z', $partitionEnd, 1) !== 0) {
										echo '...<br/>';
									}
								}
							}
						?>
					</table>
				</form>
			</div>
		</div>
		
	</div>
</body>
</html>










