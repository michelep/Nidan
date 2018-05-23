<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

if(!empty($_GET["id"])) {
    $host_id = intval($_GET["id"]);
    
    $result = doQuery("SELECT ID,IP,MAC,Vendor,Hostname,isOnline,addDate,stateChange,lastCheck,checkCycle FROM Hosts WHERE ID='$host_id';");
    if(mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	
	$host = new Host($row["ID"]);

	$host_id = $row["ID"];
	$host_ip = $row["IP"];
	$host_mac = $row["MAC"];
	$host_vendor = $row["Vendor"];
	$host_name = stripslashes($row["Hostname"]);
    	$host_adddate = new DateTime($row["addDate"]);
    	$host_lastCheck = new DateTime($row["lastCheck"]);
    	$host_stateChange = new DateTime($row["stateChange"]);
	$host_checkcycle = $row["checkCycle"];

	$pageTitle = "Host $host->hostname details";
    }
}

include "common_head.php"; 

include_once "common_sidebar.php";
?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
<?php

///////////////////////////////////////////////////////////////
// HOST DETAILS
//
//

if(isset($_GET["id"])) {
?>
    <div class="panel panel-default">
	<div class="panel-heading"><h2><i class="fa <?php echo $host->getTypeIcon(); ?>" aria-hidden="true"></i> Host <?php echo "$host->hostname"; ?> details</h2></div>
	<div class="panel-body">
	    <ul class="list-group list-group-flush">
		<li class="list-group-item">
		    <?php echo _("Hostname/IP: "); echo $host->hostname."/".$host->ip; ?>
		</li><li class="list-group-item">
		    <?php echo _("Added: "); echo $host->addDate->format("H:i:s d-M-Y"); ?>
		</li><li class="list-group-item">
		    <?php echo _("MAC Address: "); echo $host->mac; echo " (".($host->getVendor()).")"; ?>
		</li><li class="list-group-item">
		    <?php echo _("Last state change: "); echo ($host->stateChange ? $host->stateChange->format("H:i:s d-M-Y"):"never"); ?>
		</li><li class="list-group-item">
		    <?php echo _("Last Seen: "); echo ($host->lastSeen ? $host->lastSeen->format("H:i:s d-M-Y"):"never"); ?>
		</li><li class="list-group-item">
		    <?php echo _("Last check: "); echo ($host->lastCheck ? $host->lastCheck->format("H:i:s d-M-Y"):"planned"); ?>
		</li><li class="list-group-item">
		    <?php echo _("Note: "); echo "<i>$host->note</i>"; ?>
		    <p class="float-right">
			<a class="btn ajaxDialog" title="Edit host" href="/ajax?action=host_edit&id=<?php echo $host->id; ?>"><i class="fa fa-pen-square" aria-hidden="true"></i></a>
		    </p>
		</li>
	    </ul>
	</div>
	<h3><?php echo _("Services"); ?></h3>
	<table class="table table-striped">
	    <thead>
		<th><?php echo _("State"); ?></th>
		<th><?php echo _("Port"); ?></th>
		<th><?php echo _("Description"); ?></th>
		<th><?php echo _("Banner"); ?></th>
		<th><?php echo _("Last seen"); ?></th>
	    </thead>
	    <tbody>
<?php
    // Get all services found on this host
    $result = doQuery("SELECT ID,Port,Proto,State,Banner,lastSeen FROM Services WHERE hostId='$host_id';");
    if(mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	    $service_id = $row["ID"];
	    $service_port = $row["Port"];
	    $service_proto = $row["Proto"];
	    $service_banner = $row["Banner"];
	    $service_lastseen = new DateTime($row["lastSeen"]);

	    switch($row["State"]) {
		case 'open':
		    $service_status = "fa-circle-o text-success";
		    break;
		case 'closed':
		    $service_status = "fa-times text-danger";
		    break;
		case 'filtered':
		    $service_status = "fa-filter text-info";
		    break;
		default:
		    $service_status = "fa-question-circle";
		    break;
	    }   

	    echo "<tr>
		<td><i class='fa $service_status' aria-hidden='true'></i></td>
		<td>$service_port/$service_proto <a href='https://www.speedguide.net/port.php?port=$service_port' target=new'>&nbsp;<i class='fa fa-question-circle' aria-hidden='true'></i></a></td>
		<td>".(array_key_exists("$service_port/$service_proto",$tcp_services) ? $tcp_services["$service_port/$service_proto"]["desc"]:"No desc")."</td>
		<td>$service_banner</td>
		<td>".$service_lastseen->format("H:i:s d-M-Y")."</td>
	    </tr>";
	}
    } else {
        echo "<tr><td colspan=10>Host not scanned yet...</td></tr>";
    }
?>
	    </tbody>
	</table>
	<h3><?php echo _("Last 10 events"); ?></h3>
	<table class="table table-striped">
	    <thead>
		<th><?php echo _("Priority"); ?></th>
		<th><?php echo _("Description"); ?></th>
		<th><?php echo _("Added by"); ?></th>
		<th><?php echo _("Added on"); ?></th>
	    </thead>
	    <tbody>
<?php
	$result = doQuery("SELECT ID,Priority,Description,userId,addDate FROM HostsLog WHERE hostId='$host->id';");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$host_event_id = $row["ID"];
		$host_event_priority = $row["Priority"];
		$host_event_desc = stripslashes($row["Description"]);
		if(is_null($row["userId"])) {
		    $host_event_user = "System";
		} else {
		    $tmp_user = new User($row["userId"]);
		    $host_event_user = $tmp_user->name;
		}
    		$host_event_adddate = new DateTime($row["addDate"]);
		echo "<tr>
		<td><i class='fas fa-dot-circle ";
		switch($host_event_priority) {
		    case 1: echo ".text-info";
			    break;
		    case 2: echo ".text-primary";
			    break;
		    case 3: echo ".text-success";
			    break;
		    case 4: echo ".text-warning";
			    break;
		    case 5: echo ".text-danger";
			    break;
		    default: echo ".text-secondary";
			    break;
		}
		echo "'></i></td>
		<td>$host_event_desc</td>
		<td>$host_event_user</td>
		<td>".$host_event_adddate->format("H:i:s d-M-Y")."</td>
		</tr>";
	    }
	} else {
    	    echo "<tr><td colspan=10>No events yet!</td></tr>";
	}
?>
	    </tbody>
	</table>
	<hr/>
	<div class="btn-group" role="group" aria-label="Host actions">
	    <a class="btn btn-info ajaxDialog" href="/ajax?action=host_add_event&id=<?php echo $host->id; ?>" title="Add event"><i class="fa fa-plus-square" aria-hidden="true"></i> Add event</a>
	    <a class="btn btn-danger ajaxDialog" href="/ajax?action=host_remove&id=<?php echo $host->id; ?>" title="Remove this host"><i class="fa fa-trash" aria-hidden="true"></i> Remove host</a>
	</div>
    </div>
<?php
} else {
///////////////////////////////////////////////////////////////
// NET OR FULL HOSTS LIST
//
//
    if(isset($_GET["p"])) {
	$page_num = abs(intval(sanitize($_GET["p"])));
    } else {
	$page_num = 1;
    }

    $order_by = "INET_ATON(IP)";

    if(isset($_GET["o"])) {
	switch($_GET["o"]) {
	    case 'mac':
		$order_by = "MAC";
		break;
	    case 'ip':
		$order_by = "INET_ATON(IP)";
		break;
	    case 'host':
		$order_by = "Hostname";
		break;
	    case 'adddate':
		$order_by = "addDate";
		break;
	    case 'chgdate':
		$order_by = "stateChange";
		break;
	    case 'lastcheck':
		$order_by = "lastCheck";
		break;
	    case 'ip':
	    default:
		$order_by = "INET_ATON(IP)";
		break;
	}
    }
    
    if(isset($_GET["d"])) {
	if($_GET["d"]==1) {
	    $order_by .= " DESC";
	}
    }

    $row_offs = ($page_num-1) * 10;

    if(!empty($_GET["net"])) {
	$net_id = intval(sanitize($_GET["net"]));

	$result = doQuery("SELECT ID FROM Hosts WHERE netId='$net_id';");
	$total_rows = mysqli_num_rows($result);

	$tmp_net = new Network($net_id);

	// Show ALL HOSTS order by lastSeen first in network
	$result = doQuery("SELECT ID FROM Hosts WHERE netId='$net_id' ORDER BY $order_by LIMIT 10 OFFSET $row_offs;");
	echo "<h2>All hosts in $tmp_net->description ($tmp_net->network)</h2>";
    } else {
	$result = doQuery("SELECT ID FROM Hosts;");
	$total_rows = mysqli_num_rows($result);

	// Show ALL HOSTS order by lastSeen first
	$result = doQuery("SELECT ID FROM Hosts ORDER BY $order_by LIMIT 10 OFFSET $row_offs;");
	echo "<h2>All hosts in all net(s)</h2>";
    }

?>
    <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
		<th></th>
		<th><?php echo _("MAC"); ?></i></th>
		<th><?php echo _("IP"); ?> <a href="<?php echo keepGet('o','ip'); ?>" class="fa fa-fw fa-sort"></a></th>
    		<th><?php echo _("Hostname"); ?> <a href="<?php echo keepGet('o','host'); ?>" class="fa fa-fw fa-sort"></a></th>
		<th><?php echo _("Services"); ?></th>
		<th><?php echo _("Last seen"); ?> <a href="<?php echo keepGet('o','lastseen'); ?>" class="fa fa-fw fa-sort"></a></th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
    if(mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	    $host = new Host($row["ID"]);

	    switch($host->state) {
		case "offline":
		    $host_status = "table-warning";
		    break;
		case "online":
		default:
		    $host_status = "table-default";
		    break;
	    }

	    echo "<tr class='$host_status'>
		<td><i class='fa ".$host->getTypeIcon()."' aria-hidden='true'></a></td>
		<td>$host->mac</td>
		<td><a href='/host?id=$host->id'>$host->ip</a></td>
		<td>$host->hostname</td>
		<td>";

	    $res = doQuery("SELECT Port,Proto FROM Services WHERE hostId='$host->id' AND State='open';");
	    if(mysqli_num_rows($res) > 0) {
		while($row = mysqli_fetch_array($res,MYSQLI_ASSOC)) {
		    $service_port = $row["Port"];
		    $service_proto = $row["Proto"];
		    
		    if(isset($tcp_services["$service_port/$service_proto"])) {
			switch($tcp_services["$service_port/$service_proto"]["relevancy"]) {
			    default:
			    case 1:
				$service_relevancy = "default";
				break;
			    case 2:
				$service_relevancy = "primary";
				break;
			    case 3:
				$service_relevancy = "info";
				break;
			    case 4:
				$service_relevancy = "warning";
				break;
			    case 5:
				$service_relevancy = "danger";
				break;
			}
		    } else {
			$service_relevancy = "default";
		    }

		    echo "<span class='badge badge-pill badge-".$service_relevancy."'>$service_port/$service_proto</span> ";
		}
	    }
	    echo "</td><td>";
	    if($host->lastSeenETA > (60*24)) {
		echo $host->lastSeen->format("H:i:s d/m/Y");
	    } else {
		if($host->lastSeenETA > 0) {
		    echo $host->lastSeenETA." minutes ago";
		} else {
		    echo "just now";
		}
	    }
	    echo "</td><td>
		    <a class='nav-link ajaxCall' title='Refresh' href='/ajax?action=host_refresh&id=$host->id'><i class='fa fa-refresh' aria-hidden='true'></i></a>
		    <a class='btn ajaxDialog' title='Edit host' href='/ajax?action=host_edit&id=$host->id'><i class='fa fa-pen-square' aria-hidden='true'></i></a>
		</td>
	    </tr>";
	}
    } else {
	echo "<tr><td colspan=10>No hosts ...yet !</td></tr>";
    }
?>
	<tr>
	    <td colspan=10>
		<?php getPagination($page_num,$total_rows,'/host',10); ?>
	    </td>
	</tr></tbody></table>
    </div>
<?php
}
?>
</main>

<?php 

include "common_foot.php"; 

?>
