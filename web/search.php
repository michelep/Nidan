<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$query = trim(sanitize($_GET["q"]));

$pageTitle = "Search for '$query'";

$num_res = 0;

include "common_head.php"; 

include_once "common_sidebar.php";

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
<?php
    if(strlen($query) > 0) {
	// Check if search string could be an IP...
	if(filter_var($query, FILTER_VALIDATE_IP) !== false) {
	    $result = doQuery("SELECT ID,IP,MAC,Vendor,Hostname,Note,isOnline FROM Hosts WHERE IP LIKE '$query';");
	} else {
	    $result = doQuery("SELECT ID,IP,MAC,Vendor,Hostname,Note,isOnline,SUM(MATCH(Vendor,Hostname,Note) AGAINST('$query' IN BOOLEAN MODE)) AS Score FROM Hosts WHERE MATCH(Vendor,Hostname,Note) AGAINST('$query' IN BOOLEAN MODE) ORDER BY Score DESC;");
	}
	if(mysqli_num_rows($result) > 0) {
?>
	    <h2>Search results for '<?php echo $query;?>' in Hosts</h2>
	    <div class="table-responsive">
		<table class="table table-striped"><thead>
		    <tr>
			<th>State</th>
			<th>IP</th>
			<th>Hostname</th>
			<th>Note</th>
		    </tr>
		</thead><tbody>
<?php
    	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    		$host_id = $row["ID"];
		$host_ip = $row["IP"];
		$host_mac = $row["MAC"];
		$host_vendor = stripslashes($row["Vendor"]);
		$host_name = stripslashes($row["Hostname"]);
		$host_note = stripslashes($row["Note"]);

		$host_status = "fa-times text-danger";
		if($row["isOnline"]) {
	    	    $host_status = "fa-circle-o text-success";
		}
	    
		echo "<tr>
	    	    <td><i class='fa $host_status' aria-hidden='true'></i></td>
	    	    <td><a href='/host?id=$host_id'>$host_ip</a> <a href='http://$host_ip' target=_new><i class='fa fa-external-link w-25' aria-hidden='true'></i></a></td>
	    	    <td>$host_name</td>
	    	    <td>$host_note</td>
		</tr>";
		$num_res++;
	    }
?>
	    </tbody></table>
	</div>
<?php
	}

	$result = doQuery("SELECT hostId,Port,Proto,State,Banner FROM Services WHERE MATCH(Banner) AGAINST('$query' IN BOOLEAN MODE) OR 'Port' LIKE '%$query%';");
	if(mysqli_num_rows($result) > 0) {
?>
        <h2>Search results for '<?php echo $query;?>' in Services</h2>
        <div class="table-responsive">
    	    <table class="table table-striped"><thead>
		<tr>
		    <th>State</th>
		    <th>Host</th>
		    <th>Port/Proto</th>
		    <th>Banner</th>
		</tr>
	    </thead><tbody>
<?php
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	    $host = new Host($row["hostId"]);
	    $service_port = $row["Port"].'/'.$row["Proto"];
	    $service_banner = stripslashes($row["Banner"]);

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
    
	    $hostname = ($host->hostname ? $host->hostname:$host->ip);

	    echo "<tr>
	        <td><i class='fa $service_status' aria-hidden='true'></i></td>
	        <td><a href='/host?id=$host->id'>$hostname</a> <a href='http://$hostname' target=_new><i class='fa fa-external-link w-25' aria-hidden='true'></i></a></td>
	        <td>$service_port</td>
	        <td>$service_banner</td>
	    </tr>";
	    $num_res++;
	}
?>
	</tbody></table>
    </div>
<?php
	}
	if($num_res == 0) {
	    echo "<h2>Oh no !</h2>
	    <h4>We didn't found anything looking for '$query'</h4>";
	}
    } else {
	echo "<h2>Oooops !</h2>
	<h4>Nothing to search: please type something !</h4>";
    }
?>
</main>
<?php 

include "common_foot.php"; 

?>
