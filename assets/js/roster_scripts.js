/*
Vanilla JS
*/

//prompt user to confirm creation & download of file, then deletion of roster
function confirmCreateFile(tableName, lead) {
	
	if (lead) {
		let retVal = confirm("If you create a file from this roster, "
			+ "you will no longer be able to edit it.\nWould you like to proceed?");
		
		if (retVal == true) {
			//create file, drop table, navigate home
			makeRosterFile(tableName);
		}
	}
	else {
		alert("Only the team leader may generate a file from the roster.")
	}
}

//toggle names outside of partition
function toggleBulk() {
	let bulkContainer = document.getElementById("roster_bulk_container");
	bulkContainer.classList.toggle("hidden");
}

//shows user where to find the final output roster
function showRosterFileLocation(filePath) {
	confirm("Your roster was generated and sent to" + filePath);
}


/*
jQuery
*/

//toggle checked names
function toggleChecked() {
	$(".checked_name").toggleClass("hidden");
}

//toggle unchecked names
function toggleUnchecked() {
	$(".unchecked_name").toggleClass("hidden");
}

//get file contents from php function
function makeRosterFile(tableName) {
	$.ajax({
		url: 'assets/sql/ajax.php',
		type:'POST',
		data: {method: 'write_roster', tableName: tableName},
		success: function() {
			window.location.href = "index.html";
		},
		error: function() {
			console.log("Something went wrong");
		}
	});
}

