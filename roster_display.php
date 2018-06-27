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
				
				$fileDestination = 'uploads/' . $rosterNameDynam;
				
				move_uploaded_file($rosterFileTmpName, $fileDestination);
				
				//header("Location: roster_display.html?Hereisyoshit");
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
		echo "<input type=\"checkbox\" name=\"$name\"/>";
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
	<div id="roster_partition">
		<form method="POST" action="">
			<table id="partition_table">
				<?php
					//display requested partition
					while ($currName = fgets($openFile)) {
						
						//only display names in given bounds
						if (strncmp(ucfirst($currName), $partitionStart, 1) >= 0
						&& strncmp(ucfirst($currName), $partitionEnd, 1) <= 0 ) {
							
							print_line($currName);
						}
					}
				?>
			</table>
		</form>
	</div>
	
	<div id="roster_bulk">
		
	</div>
</body>
</html>










