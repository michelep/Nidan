<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

if(isset($_GET["id"])) {
    $net_id = intval($_GET["id"]);
    
    $result = doQuery("SELECT Network,Description,isEnable,addDate,lastCheck FROM Networks WHERE ID='$net_id';");
    if(mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	$net_address = $row["Network"];
	$net_desc = $row["Description"];
    	$net_adddate = new DateTime($row["addDate"]);
    	$net_lastcheck = new DateTime($row["lastCheck"]);

	$pageTitle = "$net_desc ($net_address)";
    }
} else {
    header("Location: /");
    exit();
}

include "common_head.php"; 

include_once "common_sidebar.php";

$local_js_code = "$(document).ready(function() {
    draw_net_chart($net_id);
});";

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Details for <?php echo "$net_desc ($net_address)"; ?></h2>
    <div class="row">
	<canvas id="net_chart" width="800" height="400"></canvas>
    </div>
<?php
?>
    <div class="clearfix">&nbsp;</div>
    <div class="btn-group" role="group" aria-label="Network actions">
<?php
if($myUser->getACL('manageNetworks')) {
?>
	<a class="btn btn-secondary ajaxDialog" title="Edit network" href="/ajax?action=network_edit&id=<?php echo $net_id; ?>"><i class="fa fa-pen-square" aria-hidden="true"></i> Edit network</a>
<?php
}
?>
	<a class="btn btn-secondary" href="/host/?net=<?php echo $net_id; ?>"><i class="fa fa-bars" aria-hidden="true"></i> Hosts in <?php echo $net_address; ?></a>
	<a class="btn btn-secondary ajaxCall" href="/ajax?action=network_refresh&id=<?php echo $net_id; ?>"><i class="fa fa-reload" aria-hidden="true"></i> Force rescan </a>
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=network_remove&id=<?php echo $net_id; ?>" title="Remove network <?php echo $net_address; ?>"><i class="fa fa-trash" aria-hidden="true"></i> Remove </a>
    </div>
</main>

<?php

include "common_foot.php";

?>
