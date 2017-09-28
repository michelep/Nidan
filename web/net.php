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


?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Details for <?php echo "$net_desc ($net_address)"; ?></h2>
    <div class="row">
	<canvas id="netChart" width="800" height="400"></canvas>
    </div>
<?php
    echo "<a href='/host/?net=$net_id'>Hosts in $net_address</a>";
?>
    <div class="clearfix">&nbsp;</div>
    <div class="btn-group" role="group" aria-label="Network actions">
	<a class="btn btn-secondary ajaxCall" href="/ajax?action=network_refresh"><i class="fa fa-refresh" aria-hidden="true"></i> Force rescan </a>
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=network_remove&id=<?php echo $net_id; ?>" title="Remove network <?php echo $net_address; ?>"><i class="fa fa-trash" aria-hidden="true"></i> Remove </a>
    </div>
</main>

<?php

include "common_foot.php"; 

?>
