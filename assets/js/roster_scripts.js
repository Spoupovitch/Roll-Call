//Vanilla

//prompt user to confirm creation & download of file, then deletion of roster
function confirmCreateFile(tableName) {
	var retVal = confirm("If you create a file from this roster, "
		+ "you will no longer be able to edit it.\nWould you like to proceed?");
	
	if (retVal == true) {
		//create file
		makeRosterFile(tableName);
		//drop table
		//deleteRoster(tableName);
		//navigate home
	}
	else {
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
		success: function(rosterFile) {
			//console.log($.parseJSON(rosterFile));
			console.log(rosterFile);
		}
	});
}

//call php delete_table function
function deleteRoster(tableName) {
	$.ajax({
		url: '../sql/ajax.php',
		type: 'POST',
		data: {method: 'delete_table', tableName: tableName},
		success: function() {
			alert("called php!");
		}
	});
	ajax.fail(function() {
		alert("ajax failed");
	});
}


