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
	    addEvent(NULL,'agent_offline',$args=array('agent_id' => $agent_id, 'timeout' => $agent_age));

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
$event_lastid = ($myConfig->get("event_lastid") ? $myConfig->get("event_lastid"):0);

$result = doQuery("SELECT ID,jobId,Event,Args,addDate,TIMESTAMPDIFF(MINUTE,addDate,NOW()) AS ETA FROM EventsLog WHERE ID > $event_lastid AND jobId IS NULL ORDER BY ID;");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$event = stripslashes($row["Event"]);
	$event_id = $row["ID"];
	$job_id = intval($row["jobId"]);
	$tmp_job = new Job($job_id);

	$args = stripslashes($row["Args"]);
	$eta = intval($row["ETA"]);
	$add_date = new DateTime($row["addDate"]);

	// Check matching trigger(s)
	$triggers = doQuery("SELECT ID,Action,Priority,Args,userId FROM Triggers WHERE (Event LIKE '$event') AND (agentId IS NULL OR agentId='$tmp_job->agentId');");
	if(mysqli_num_rows($triggers) > 0) {
	    // Triggered !
	    while($row = mysqli_fetch_array($triggers,MYSQLI_ASSOC)) {
		$trigger_id = $row["ID"];
		$trigger_action = stripslashes($row["Action"]);
		$trigger_priority = stripslashes($row["Priority"]);
		$trigger_args = json_decode(stripslashes($row["Args"]),true);

		$tmpUser = new User($row["userId"]);

		LOGWrite("TRIGGERed($trigger_id) action $event for user $tmpUser->name",LOG_NOTICE);

		doQuery("UPDATE Triggers SET lastRaised=NOW(),raisedCount=raisedCount+1,lastProcessed=NOW() WHERE ID='$trigger_id';");

		$job_details = $tmp_job->getDetails();

		// <============== Compose message
		$msg = "Dear $tmpUser->name,<br/>
		as you requested, a new event '$event' was triggered ";
		if($job_id > 0) {
		    $msg .= "by Job $job_id related to:<br/>
		    <blockquote>
		        IP/Network: ".$job_details["target"]." (".$job_details["target_type"].")<br/>
		        Scan method: ".$job_details["scan_method"]."<br/>
		    </blockquote>";
		}

		$msg .= "by Agent Id $agent_id:<br/>
		<blockquote>
		    ".print_r($args, true)."
		</blockquote><br/>
		Your tireless employee, Nidan<br/>";
		// ==============>

		switch($trigger_action) {
		    case 'notify':
			doQuery("INSERT INTO Inbox(userId,Title,Content,isRead,addDate) VALUES ($tmpUser->id,'".mysqli_real_escape_string($DB,"EVENT TRIGGERED - $event")."','".mysqli_real_escape_string($DB,$msg)."',0,NOW());");
			break;
		    case 'sendmail':
			$dest_email = $trigger_args["email"];
			if(filter_var($dest_email, FILTER_VALIDATE_EMAIL)) {
			    sendMail($dest_email,$tmpUser->name,"EVENT TRIGGERED - $event",$msg);
			}
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
	$myConfig->set("event_lastid",$event_id);
    }
}

//
// Update cron lastrun watchdog
//
$myConfig->set("cron_lastrun",date(DateTime::ISO8601));

?>