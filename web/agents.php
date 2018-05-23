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
    <h2><i class="fa fa-bullseye" aria-hidden="true"></i> Agents</h2>
<?php
if(isset($agent_id)) {
    $agent = new Agent($agent_id);
?>
    <div class="card text-center">
	<div class="card-header">
	    <h2>Agent "<?php echo "$agent->name"; ?>"</h2>
	</div>
	<div class="card-body">
	    <p class="text-left">
          Api Key: <?php echo $agent->apiKey; ?><br/>
          Description: <?php echo $agent->description; ?><br/>
          Plugins:
          <?php
          if($agent->Plugins) {
            foreach($agent->Plugins as $plugin) {
              echo "<b>$plugin</b>&nbsp;";
            }
          } else {
            echo "no plugins";
          }
          ?><br/>
          IP: <?php echo $agent->IP." (".$agent->hostName.")"; ?>
	    </p><p class="text-left">
		<a href="/agents" class="btn btn-primary">Back</a>
	    </p>
	</div>
	<div class="card-footer text-muted">
	    Added on <?php echo $agent->addDate->format("H:i:s d-M-Y"); ?>, last seen on <?php echo ($agent->lastSeen ? $agent->lastSeen->format("H:i:s d-M-Y"):"Never"); ?>
	</div>
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
		<th>Run time</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
	$result = doQuery("SELECT ID,Name,Description,IP,Hostname,isEnable,isOnline,addDate,TIMESTAMPDIFF(MINUTE,lastSeen,NOW()) AS lastSeen,TIMESTAMPDIFF(HOUR,startDate,lastSeen) AS runTime FROM Agents ORDER BY addDate;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$agent_id = $row["ID"];
		$agent_name = stripslashes($row["Name"]);
		$agent_description = stripslashes($row["Description"]);
		$agent_ip = $row["IP"];
		$agent_hostname = $row["Hostname"];
		$agent_is_enable = $row["isEnable"];
		$agent_adddate = new DateTime($row["addDate"]);

		$agent_runtime = NULL;

		if($agent_is_enable) {
		    $agent_status = "fa-times text-danger";
		    if($row["isOnline"] == 1) {
			$agent_status = "fa-circle text-success";
			$agent_runtime = $row["runTime"];
		    }
		} else {
		    $agent_status = "fa-pause-circle text-warning";
		}

		$agent_lastseen = getHumanETA($row["lastSeen"]);

		echo "<tr>
		    <td><i class='fa $agent_status' aria-hidden='true'></i></td>
		    <td><a href='/agents/$agent_id'>$agent_name</a></td>
		    <td>$agent_description</td>
		    <td>$agent_ip</td>
		    <td>$agent_hostname</td>
		    <td>$agent_lastseen ago</td>
		    <td>".(is_null($agent_runtime)?"Offline":getHumanETA($agent_runtime))."</td>
		    <td>";
		if($myUser->getACL('manageAgents')) {
		    echo "<a class='ajaxDialog' title='Edit agent' href='/ajax?action=agent_edit&id=$agent_id'><i class='fa fa-pen-square' aria-hidden='true'></i></a>";
		}
		echo "</td></tr>";
	    }
	} else {
	    echo "<tr><td colspan=10>No agents registered ...yet !</td></tr>";
	}
?>
	</tbody></table>
    </div>
    <div class="clearfix">&nbsp;</div>
<?php
    if($myUser->getACL('manageAgents')) {
?>
    <div class="btn-group" role="group" aria-label="Agents actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=agent_edit" title="Add new agent"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new agent</a>
    </div>
<?php
    }
}
?>
</main>

<?php

include "common_foot.php";

?>
