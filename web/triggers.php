<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

include "common_head.php"; 

include_once "common_sidebar.php";


?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Triggers</h2>
    <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
		<th>Event</th>
		<th>Action</th>
    		<th>Args</th>
		<th>Raised</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
$result = doQuery("SELECT ID,Event,Action,Args,raisedCount,isEnable FROM Triggers WHERE userId='$mySession->userId' ORDER BY addDate DESC LIMIT 20 ;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$trigger_id = $row["ID"];
	$trigger_event = stripslashes($row["Event"]);
	$trigger_action = stripslashes($row["Action"]);
	$trigger_args = stripslashes($row["Args"]);
	$trigger_raised = intval($row["raisedCount"]);
	$trigger_isenable = $row["isEnable"];

	echo "<tr class='".($trigger_isenable ? "":"table-danger")."'>
	    <td>$trigger_event</td>
	    <td>$trigger_action</td>
	    <td>$trigger_args</td>
	    <td>$trigger_raised</td>
	    <td>
		<a class='btn btn-secondary ajaxDialog' href='/ajax?action=trigger_edit&id=$trigger_id' title='Edit trigger'><i class='fa fa-pencil' aria-hidden='true'></i></a>
		<a class='btn btn-secondary ajaxDialog' href='/ajax?action=trigger_remove&id=$trigger_id' title='Remove trigger'><i class='fa fa-times text-danger' aria-hidden='true'></i></a>
	    </td>
	</tr>";
    }
} else {
    echo "<tr><td colspan=10>No triggers !</td></tr>";
}

?>
	</tbody></table>
    </div>
    <div class="btn-group" role="group" aria-label="Trigger actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=trigger_edit" title="Add new trigger"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new trigger</a>
    </div>
</main>

<?php 

include "common_foot.php"; 

?>
