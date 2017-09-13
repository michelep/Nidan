<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$pageTitle = "All agents";

include "common_head.php"; 

include_once "common_sidebar.php";

if(!empty($_GET["id"])) {
    $agent_id = intval(sanitize($_GET["id"]));
    $agent = new Agent($agent_id);
    $pageTitle = "Agent $agent->name details";
}

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
<?php
if(isset($agent_id)) {
?>
    <div class="panel panel-default">
	<div class="panel-heading"><h2>Agent <?php echo "$agent->name"; ?> details</h2></div>
	<div class="panel-body">
	    <p>
		Added on <?php echo $agent->addDate->format("H:i:s d-M-Y"); ?>
	    </p><p>
		Last seen on <?php echo ($agent->lastSeen ? $agent->lastSeen->format("H:i:s d-M-Y"):"Never"); ?>
	    </p>
	</div>
<?php
} else {
?>
    <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
		<th>State</th>
		<th>Name</th>
    		<th>Description</th>
		<th>IP</th>
		<th>Hostname</th>
		<th>Last Seen</th>
		<th>Added on</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
	$result = doQuery("SELECT ID,Name,Description,IP,Hostname,isEnable,isOnline,addDate,TIMESTAMPDIFF(MINUTE,lastSeen,NOW()) AS lastSeen FROM Agents ORDER BY addDate;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$agent_id = $row["ID"];
		$agent_name = stripslashes($row["Name"]);
		$agent_description = stripslashes($row["Description"]);
		$agent_ip = $row["IP"];
		$agent_hostname = $row["Hostname"];
		$agent_is_enable = $row["isEnable"];
		$agent_adddate = new DateTime($row["addDate"]);

		$agent_status = "fa-times text-danger";
		if($row["isOnline"] == 1) {
		    $agent_status = "fa-circle-o text-success";
		}

		if($row["lastSeen"] > 60) {
		    $agent_lastseen = $row["lastSeen"]." mins ago";
		} else if($row["lastSeen"] > 0) {
		    $agent_lastseen = $row["lastSeen"]." mins ago";
		} else {
		    $agent_lastseen = "now";
		}

		echo "<tr>
		    <td><i class='fa $agent_status' aria-hidden='true'></i></td>
		    <td><a href='/agents/$agent_id'>$agent_name</a></td>
		    <td>$agent_description</td>
		    <td>$agent_ip</td>
		    <td>$agent_hostname</td>
		    <td>$agent_lastseen</td>
		    <td>".$agent_adddate->format("H:i:d d:M:Y")."</td>
		    <td><a class='ajaxDialog' title='Edit agent' href='/ajax?action=agent_edit&id=$agent_id'><i class='fa fa-pencil-square' aria-hidden='true'></i></a></td>
		</tr>";
	    }
	} else {
	    echo "<tr><td colspan=10>No agents registered ...yet !</td></tr>";
	}
?>
	</tbody></table>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="btn-group" role="group" aria-label="Agents actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=agent_edit" title="Add new agent"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new agent</a>
    </div>
<?php
}
?>
</main>

<?php 

include "common_foot.php"; 

?>
