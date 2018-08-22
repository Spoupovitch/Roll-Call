//Vanilla

//prompt user to confirm creation & download of file, then deletion of roster
function confirmCreateFile(tableName, lead) {
	
	if (lead) {
		var retVal = confirm("If you create a file from this roster, "
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

function placeholder() {
	alert("Coming soon");
}


//jQuery

//get file contents from php function
function makeRosterFile(tableName) {
	$.ajax({
		url: 'assets/sql/ajax.php',
		type:'POST',
		data: {method: 'write_roster', tableName: tableName},
		success: function() {
			window.location.href = "roster_index.html";
		},
		error: function() {
			console.log("Something went wrong");
		}
	});
}
/*
//call php delete_table function
function deleteRoster(tableName) {
	$.ajax({
		url: 'assets/sql/ajax.php',
		type: 'POST',
		data: {method: 'delete_table', tableName: tableName},
		success: function(res) {
			console.log(res);
		}
	});
}
*/


