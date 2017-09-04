<?php

include "common.inc.php";

//
// Check for periodic network scan
//
$result = doQuery("SELECT ID,agentId,TIMESTAMPDIFF(MINUTE,lastCheck,NOW()) AS Age,Network,checkCycle FROM Networks WHERE isEnable=1;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	if(($row["Age"] == NULL) || ($row["Age"] > $row["checkCycle"])) {
	    $job = new Job();

	    $job->job = "net_scan";
	    $job->itemId = $row["ID"];
	    $job->agentId = $row["agentId"];
	    $job->args = json_encode(array("net_addr" => $row["Network"],"net_scan_method" => ""));

	    $job->schedule();
	}
    }
}

//
// Check for host services scan
//

$result = doQuery("SELECT ID,netId,agentId,IP,TIMESTAMPDIFF(MINUTE,lastCheck,NOW()) AS Age,checkCycle FROM Hosts WHERE isOnline=1;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	if(($row["Age"] == NULL) || ($row["Age"] > $row["checkCycle"])) {
	    $job = new Job();

	    $job->job = "host_scan";
	    $job->itemId = $row["ID"];
	    $job->agentId = $row["agentId"];

	    $job->args = json_encode(array("host_addr" => $row["IP"],"scan_method" => ""));
	    $job->schedule();
	}
    }
}

//
// Process triggers and new events...
//
$result = doQuery("SELECT agentId,Event,Args,addDate,TIMESTAMPDIFF(MINUTE,addDate,NOW()) AS ETA FROM EventsLog WHERE isNew=1;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$event = stripslashes($row["Event"]);
	$agentid = intval($row["agentId"]);
	$args = stripslashes($row["Args"]);
	$eta = intval($row["ETA"]);
	$adddate = new DateTime($row["addDate"]);
	// Check matching trigger(s)
	$trigger = doQuery("SELECT ID,Action,Priority,Args,userId FROM Triggers WHERE ((Event LIKE '$event') AND (agentId IS NULL OR agentId='$agentid'));");
	if(mysqli_num_rows($trigger) > 0) {
	    // Triggered !
	    while($row = mysqli_fetch_array($trigger,MYSQLI_ASSOC)) {
		$trigger_id = $row["ID"];
		$trigger_action = stripslashes($row["Action"]);
		$trigger_priority = stripslashes($row["Priority"]);
		$trigger_args = stripslashes($row["Args"]);

		$tmpUser = new User($row["userId"]);

		doQuery("UPDATE Triggers SET lastRaised=NOW(),raisedCount=raisedCount+1 WHERE ID='$trigger_id';");

		switch($trigger_action) {
		    case 'sendmail':
			// Compose mail body
			$msg = "Dear $tmpUser->Name,\nas you requested, a new event '$event' was triggered by Agent Id $agentid";
		    default:
			break;
		}
	    }
	}
    }
}

?>