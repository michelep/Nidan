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
  <h2>Networks</h2>
  <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
    		<th>Network</th>
		<th>Description</th>
		<th>Hosts</th>
		<th>Agent</th>
		<th>Added on</th>
		<th>Last check</th>
		<th>Scan time</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
$result = doQuery("SELECT ID,Network,(SELECT COUNT(ID) FROM Hosts WHERE netId=Networks.ID) AS Hosts,Description,agentId,isEnable,addDate,lastCheck,scanTime FROM Networks;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$net_id = $row["ID"];
	$net_address = $row["Network"];
	$net_desc = $row["Description"];
	$net_hcount = $row["Hosts"];
	$net_agentid = $row["agentId"];
    	$net_adddate = new DateTime($row["addDate"]);
	if($row["lastCheck"]) {
	    $net_lastcheck = new DateTime($row["lastCheck"]);
	} else {
	    $net_lastcheck = NULL;
	}
	$net_scantime = $row["scanTime"];

	if($net_agentid > 0) {
	    $agent = new Agent($net_agentid);
	}

	echo "<tr class='".($row["isEnable"] ? "danger" : "")."'>
	    <td><a href='/net/$net_id'>$net_address</a></td>
	    <td>$net_desc</td>
	    <td>$net_hcount</td>
	    <td><a href='/agents/$net_agentid'>".($net_agentid ? $agent->name:"Any")."</a></td>
	    <td>".$net_adddate->format("H:i:s d-M-Y")."</td>
	    <td>".($net_lastcheck ? $net_lastcheck->format("H:i:s d-M-Y"): "None")."</td>
	    <td>".($net_scantime ? $net_scantime." secs" : "Not done yet !")."</td>
	    <td><a class='ajaxDialog' title='Edit network' href='/ajax?action=network_edit&id=$net_id'><i class='fa fa-pencil-square' aria-hidden='true'></i></a></td>
	</tr>";
    }
} else {
    echo "<tr><td colspan=10>No networks !</td></tr>";
}
?>
    </tbody></table>
    </div>

    <div class="btn-group" role="group" aria-label="Network actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=network_edit" title="Add new network"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new network</a>
    </div>
</main>

<?php 

include "common_foot.php"; 

?>
