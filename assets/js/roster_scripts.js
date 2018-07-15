$(document).ready(function(){

    $("input[name='team_pass']").focus();

    $("input[name='show_hide_pass']").click(function(){
        if ($("input[name='show_hide_pass']").is(":checked"))
        {
            $("team_pass").clone()
            .attr("type", "text").insertAfter("input[name='team_pass']")
            .prev().remove();
        }
        else
        {
            $("input[name='team_pass']").clone()
            .attr("type","password").insertAfter("input[name='team_pass']")
            .prev().remove();
        }
    });
});

function placeholder() {
	alert('Coming Soon');
}

$('form.checked_partition').on('submit', compileRosters());
$('form.checked_bulk').on('submit', compileRosters);

function compileRosters() {
	var xhr = new XMLHttpRequest();
	
	
	return false;
}