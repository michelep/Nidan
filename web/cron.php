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
	    $job->args = json_encode(array("net_addr" => $row["Network"],"scan_method" => ""));

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
// Check for active agent offline
//

$result = doQuery("SELECT ID,TIMESTAMPDIFF(MINUTE,lastSeen,NOW()) AS Age,isOnline FROM Agents WHERE isEnable=1;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$agent_age = $row["Age"];
	$agent_id = $row["ID"];
	$agent_isonline = $row["isOnline"];

	if(($agent_age > 15)&&($agent_isonline == 1)) {
	    raiseEvent($agent_id,NULL,'agent_offline',$args=NULL);

	    LOGWrite("AGENT $agent_id offline for $agent_age minutes",LOG_WARNING);

	    doQuery("UPDATE Agents SET isOnline=0 WHERE ID='$agent_id';");
	}
    }
}

//
// Clean up old events...
//
$event_keep = $myConfig->get("event_keep");
if($event_keep > 0) {
    doQuery("DELETE FROM EventsLog WHERE TIMESTAMPDIFF(MINUTE,addDate,NOW()) > $event_keep;");
}

//
// Process triggers and new events...
//
$result = doQuery("SELECT agentId,jobId,Event,Args,addDate,TIMESTAMPDIFF(MINUTE,addDate,NOW()) AS ETA FROM EventsLog;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$event = stripslashes($row["Event"]);
	$agent_id = intval($row["agentId"]);
	$job_id = intval($row["jobId"]);
	$args = stripslashes($row["Args"]);
	$eta = intval($row["ETA"]);
	$adddate = new DateTime($row["addDate"]);

	// Check matching trigger(s)
	$result_2 = doQuery("SELECT ID,Action,Priority,Args,userId FROM Triggers WHERE ((lastProcessed IS NULL) OR (TIMESTAMPDIFF(SECOND,lastProcessed,'".$adddate->format('Y-m-d H:i:s')."') > 0)) && (Event LIKE '$event') AND (agentId IS NULL OR agentId='$agent_id');");
	if(mysqli_num_rows($result_2) > 0) {
	    // Triggered !
	    while($row = mysqli_fetch_array($result_2,MYSQLI_ASSOC)) {
		$trigger_id = $row["ID"];
		$trigger_action = stripslashes($row["Action"]);
		$trigger_priority = stripslashes($row["Priority"]);
		$trigger_args = stripslashes($row["Args"]);

		$tmpUser = new User($row["userId"]);

		LOGWrite("TRIGGERed($trigger_id) action $event for user $tmpUser->name",LOG_NOTICE);

		doQuery("UPDATE Triggers SET lastRaised=NOW(),raisedCount=raisedCount+1,lastProcessed=NOW() WHERE ID='$trigger_id';");

		switch($trigger_action) {
		    case 'sendmail':
			// Compose mail body
			$msg = "Dear $tmpUser->name,\nas you requested, a new event '$event' was triggered by Agent Id $agent_id";
			sendMail($trigger_args,$tmpUser->name,"EVENT TRIGGERED - $event",$msg);
			break;
		    case '4bl.it':
			// Send via 4bl.it to Telegram Channel
			// #TODO
			break;
		    default:
			break;
		}
	    }
	}
    }
}
//
// Cleanup old events
//


?>