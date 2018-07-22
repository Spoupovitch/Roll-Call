function placeholder() {
	alert('Coming Soon');
}

function compileRosters(rosterHi, $rosterMid, $rosterLo) {
//function compileRosters() {
	/*
	var rosterHi = "<?php echo $sortedBulkHi; ?>";
	var rosterMid = "<?php echo json_encode($sortedPartition); ?>";
	var rosterLo = "<?php echo json_encode($sortedBulkLo); ?>";
	*/
	
	alert(rosterHi);
	alert(rosterMid);
	alert(rosterLo);
	
	roster = rosterHi.concat(rosterMid.concat(rosterLo));
	
	document.cookie = "roster=" + roster + ";path=C/wamp64/www/projects/Roll-Call/roster_compilation.php";
	
	//document.getElementById("checked_roster").submit();
	
	//window.location.href = "../../roster_compilation.php";
}