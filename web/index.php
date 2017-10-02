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
<?php
// Check consistency
$version = $myConfig->get("version");
if(strcasecmp(VERSION,$version) != 0) {
    echo "<div class='alert alert-danger' role='alert'>
	 <strong>Version mismatch !</strong> Your release ".VERSION." doesn't match database release ($version). Please run <a href='/install'>install script</a> to check and upgrade, if needed.
    </div>";
}

// Check for updates, if TRUE
if($CFG["check_updates"]) {
    $url = 'https://raw.githubusercontent.com/michelep/Nidan/master/version.xml'; 
    $xml = simpleXML_load_file($url,"SimpleXMLElement",LIBXML_NOCDATA); 
    if($xml === FALSE) { 
	// Error...
    } else {
	if(strcasecmp(VERSION,$xml["version"]) != 0) {
	    echo "<div class='alert alert-warning' role='alert'>
		 <strong>A new release available !</strong> Nidan ".$xml["version"]." was released: ".$xml["note"].". Update from <a href='https://github.com/michelep/Nidan'>repository</a>
	    </div>";
	}
	// print_r($xml);
	// SimpleXMLElement Object ( [version] => 0.0.1rc8 [reldate] => 2017-10-02 [priority] => HIGH [note] => Lot of bugs fixed, minor changes in DB schema, funcionality added )
    }
}


?>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card card-inverse card-success">
                <div class="card-block bg-success">
                    <div class="rotate">
                        <i class="fa fa-cubes fa-5x"></i>
                    </div>
                    <h6 class="text-uppercase">Networks</h6>
                    <h1 class="display-1">
		    <?php 
		    $result = doQuery("SELECT ID FROM Networks;");
		    echo mysqli_num_rows($result);
		    ?>
		    </h1>
    		</div>
	    </div>
	</div><div class="col-lg-3 col-md-6">
            <div class="card card-inverse card-danger">
                <div class="card-block bg-danger">
                    <div class="rotate">
                        <i class="fa fa-laptop fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Hosts</h6>
                    <h1 class="display-1">
		    <?php
		    $result = doQuery("SELECT ID FROM Hosts;");
		    echo mysqli_num_rows($result);
		    ?>
		    </h1>
                </div>
            </div>
        </div><div class="col-lg-3 col-md-6">
            <div class="card card-inverse card-info">
                <div class="card-block bg-info">
                    <div class="rotate">
                        <i class="fa fa-cogs fa-5x"></i>
                    </div>
                    <h6 class="text-uppercase">Services</h6>
                    <h1 class="display-1">
		    <?php
		    $result = doQuery("SELECT ID FROM Services;");
		    echo mysqli_num_rows($result);
		    ?>
		    </h1>
                </div>
            </div>
        </div><div class="col-lg-3 col-md-6">
            <div class="card card-inverse card-warning">
                <div class="card-block bg-warning">
                    <div class="rotate">
                        <i class="fa fa-plug fa-5x"></i>
                    </div>
                    <h6 class="text-uppercase">Agents Online</h6>
                    <h1 class="display-1">
		    <?php
		    $result = doQuery("SELECT ID FROM Agents WHERE isOnline=1;");
		    echo mysqli_num_rows($result);
		    ?>
		    </h1>
                </div>
    	    </div>
    	</div>
    </div>
    <hr>
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
	    <td>";
	if($myUser->getACL('manageNetworks')) {
		echo "<a class='ajaxDialog' title='Edit network' href='/ajax?action=network_edit&id=$net_id'><i class='fa fa-pencil-square' aria-hidden='true'></i></a></td>";
	}
	echo "</tr>";
    }
} else {
    echo "<tr><td colspan=10>No networks !</td></tr>";
}
?>
    </tbody></table>
    </div>
<?php
if($myUser->getACL('manageNetworks')) {
?>
    <div class="btn-group" role="group" aria-label="Network actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=network_edit" title="Add new network"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new network</a>
    </div>
<?php
}
?>
</main>

<?php 

include "common_foot.php"; 

?>
