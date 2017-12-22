<?php

include __DIR__.'/common.inc.php';

if(isset($_GET["action"])) {
    $ajax_action = sanitize($_GET["action"]);
} else if(isset($_POST["action"])) {
    $ajax_action = sanitize($_POST["action"]);
}

if($mySession->isLogged()) {
    /* ===========================================
    Add or edit AGENTS
    =========================================== */
    if($ajax_action == "agent_edit") {
	if(isset($_GET["id"])) {
	    $agent_id = intval($_GET["id"]);

	    $agent = new Agent($agent_id);
	}
	echo "<form method='POST' id='ajaxDialog'>
	<input type='hidden' name='action' value='cb_agent_edit'>";
	if(isset($agent_id)) {
	    echo "<input type='hidden' name='agent_id' value='$agent_id'>";
	    $agent_apikey = $agent->apiKey;
	} else {
	    // Compute a random API Key for this agent
	    $agent_apikey = md5(APG(16));
	}
	echo "<div class='form-group'>
	    <span class='form-group-addon'>Name</span>
	    <input type='text' id='agent_name' name='agent_name' class='w-100 validate[required]' value='$agent->name'>
	    <p class='help-block'>An arbitrary name for this agent</p>
	</div><div class='form-group'>
	    <span class='form-group-addon'>API Key</span>
	    <input type='text' id='agent_apikey' name='agent_apikey' class='w-100 validate[required]' value='$agent_apikey' readonly>
	    <p class='help-block'>Remember to copy this key to agent's nidan.cfg file !</p>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Description</span>
	    <input type='text' id='agent_desc' name='agent_desc' class='w-100' value='$agent->description'>
	</div><div class='form-group'>
	    <input type='checkbox' name='is_enable' ".isChecked($agent->isEnable)."> Agent enabled
	    <p class='help-block'>If checked, this agent will be used for job(s)</p>
	</div>";
    }

    /* ===========================================
    Add or edit NETWORKS
    =========================================== */
    if($ajax_action == "network_edit") {
	if(isset($_GET["id"])) {
	    $net_id = intval($_GET["id"]);

	    $result = doQuery("SELECT ID,Network,Description,checkCycle,agentId,isEnable FROM Networks WHERE ID='$net_id';");
	    if(mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$net_id = $row["ID"];
		$net_address = stripslashes($row["Network"]);
		$net_desc = stripslashes($row["Description"]);
		$net_checkcycle = intval($row["checkCycle"]);
		$net_agentid = $row["agentId"];
		$net_isenable = $row["isEnable"];
	    }
	}

	echo "<form method='POST' id='ajaxDialog'>
	<input type='hidden' name='action' value='cb_network_edit'>";
	if(isset($net_id)) {
	    echo "<input type='hidden' name='net_id' value='$net_id'>";
	}
	echo "<div class='form-group'>
	    <span class='form-group-addon'>Network</span>
	    <input type='text' id='net_address' name='net_address' class='w-100 validate[required]' value='$net_address'>
	    <p class='help-block'>Network address in CIDR notation (ie. 192.168.0.0/24)</p>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Agent:</span>
	    <select data-placeholder='Choose which agent should scan this network' class='form-control' id='net_agentid' name='net_agentid'>
		<option value='0' ".isSelected(0,$net_agentid).">Any</option>";
	$result = doQuery("SELECT ID,Name FROM Agents WHERE isEnable=1;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$agent_id = $row["ID"];
		$agent_name = stripslashes($row["Name"]);
		echo "<option value='$agent_id' ".isSelected($agent_id,$net_agentid).">$agent_name</option>";
	    }
	}
	echo "</select>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Description</span>
	    <input type='text' id='net_desc' name='net_desc' class='w-100 validate[required]' value='$net_desc'>
	    <p class='help-block'>A quick description (ie. 'Home net')</p>
	</div><div class='form-group'>
	    <span for='net_checkcycle'>Check every (default 10) minutes:</label>
	    <input type='text' id='net_checkcycle' name='net_checkcycle' class='w-100 validate[required]' value='".(empty($net_checkcycle) ? "10":$net_checkcycle)."'>
	</div><div class='form-group'>
	    <input type='checkbox' name='is_enable' ".isChecked($net_isenable)."> Enabled
	    <p class='help-block'>If checked, this network will be scanned</p>
	</div></form>";
    }
    /* ===========================================
    Remove NETWORK
    =========================================== */
    if($ajax_action == "network_remove") {
	if(isset($_GET["id"])) {
	    $net_id = intval($_GET["id"]);

	    $result = doQuery("SELECT ID,Network,Description,checkCycle,isEnable FROM Networks WHERE ID='$net_id';");
	    if(mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$net_id = $row["ID"];
		$net_address = stripslashes($row["Network"]);
		echo "<form method='POST' id='ajaxDialog'>
		    <input type='hidden' name='action' value='cb_network_remove'>
		    <input type='hidden' name='net_id' value='$net_id'>
		    <div class='form-group'>
			<h4>Are you sure ?</h4>
			<p>You are going to <b>remove network $net_address and all its hosts and services</b>.</p>
			<p>Please note: this operation cannot be undone.</p>
		    </div>
    		</form>";
	    }
	}
    }

    /* ===========================================
    Refresh NETWORK
    =========================================== */
    if($ajax_action == "network_refresh") {
	// TODO
    }

    /* ===========================================
    Refresh HOST
    =========================================== */
    if($ajax_action == "host_refresh") {
	// TODO
    }

    /* ===========================================
    Clean JOB queue - Remove old complete jobs
    =========================================== */
    if($ajax_action == "job_clean") {
	doQuery("DELETE FROM JobsQueue WHERE startDate IS NOT NULL AND endDate IS NOT NULL;");
	echo "Completed jobs cleared successfully !";
    }

    /* ===========================================
    Send test e-mail
    =========================================== */
    if($ajax_action == "mail_test") {
	if(filter_var($myUser->eMail, FILTER_VALIDATE_EMAIL)) {
	    $msg = "Dear $myUser->name,\nas you requested, here's a test mail.";
	    if(sendMail($myUser->eMail,$myUser->name,"NIDAN TEST EMAIL",$msg)) {
		echo "Test mail sent to $myUser->eMail...";
	    } else {
		echo "Oops ! Something goes wrong while sending test e-mail to $myUser->eMail...";
	    }
	} else {
	    echo "Invalid e-mail address $myUser->eMail !";
	}
    }

    /* ===========================================
    Toggle user ACL
    =========================================== */
    if($ajax_action == "acl_toggle") {
	$acl = sanitize($_GET["acl"]);
	if($myUser->getACL('manageUsers')) {
	    if(array_key_exists($acl,$CFG["defaultUserAcl"])) {
		if($myUser->getACL($acl)) {
		    $myUser->setACL($acl, false);
		    echo "ACL disabled";
		} else {
		    $myUser->setACL($acl, true);
		    echo "ACL enabled";
		}
	    }
	} else {
	    echo "Sorry, you can't do this";
	}
    }

    /* ===========================================
    Remove USER
    =========================================== */
    if($ajax_action == "user_remove") {
	$user_id = intval($_GET["id"]);
	if($myUser->getACL('manageUsers')) {
	    if($user_id > 0) {
		$tmpUser = new User($user_id);
		echo "<form method='POST' id='ajaxDialog'>
		<input type='hidden' name='action' value='cb_user_remove'>
		<input type='hidden' name='user_id' value='$user_id'>
		<div class='form-group'>
		    <h2>Are your sure ?</h2>
		    <p class='help-block'>Do you really want to remove user $tmpUser->name ? This operation cannot be undone.</p>
		</div>
	        </form>";
	    }
	}
    }

    /* ===========================================
    Add or edit USER
    =========================================== */
    if($ajax_action == "user_edit") {
	if($myUser->getACL('manageUsers')) {
	    if(isset($_GET["id"])) {
		$user_id = intval(sanitize($_GET["id"]));
		$tmpUser = new User($user_id);
	    }
	    echo "<form method='POST' id='ajaxDialog'>
	    <input type='hidden' name='action' value='cb_user_edit'>";
	    if(isset($user_id)) {
		echo "<input type='hidden' name='user_id' value='$user_id'>";
	    }
	    echo "<div class='form-group'>
		<span class='form-group-addon'>User name:</span>
		<input type='text' id='user_name' name='user_name' class='form-control w-100 validate[required]' value='$tmpUser->name'>
		<p class='help-block'>Choose an unique username for this account</p>
	    </div><div class='form-group'>
		<span class='form-group-addon'>eMail address:</span>
		<input type='text' id='user_name' name='user_email' class='form-control w-100 validate[required]' value='$tmpUser->eMail'>
		<p class='help-block'>Account e-mail address, to be used for notifications and password</p>
	    </div><div class='form-group'>
		<span class='form-group-addon'>User alias:</span>
		<input type='text' id='user_alias' name='user_alias' class='form-control w-100' value='$tmpUser->alias'>
		<p class='help-block'>You can set an alias for this user</p>
	    </div><div class='form-group'>
		<span class='form-group-addon'>User rights</span>
		<div class='btn-group' data-toggle='buttons'>";
	    foreach(array_keys($CFG["defaultUserAcl"]) as $ACL) {
		echo "<label class='btn btn-success'>";
		if(isset($user_id)) {
		    echo "<input name='acl_$ACL' type='checkbox' ".(($tmpUser->getACL($ACL)) ? "checked":"")." autocomplete='off'> $ACL";
		} else {
		    echo "<input name='acl_$ACL' type='checkbox' autocomplete='off'> $ACL";
		}
		echo "</label>";
	    }
	    echo "</div>
	    </div>";
	    if(isset($user_id)) {
		echo "<div class='form-group'>
		    <input type='checkbox' name='reset_password' ".isChecked($user_id)."> Reset and send new password
		    <p class='help-block'>If checked, password will be reset and sent via mail to the user</p>
		</div>";
	    } else {
		echo "<p class='help-block'>New user password will be sent via mail to the user's e-mail</p>";
	    }
	    echo "</form>";
	}
    }

    /* ===========================================
    Add or edit TRIGGER
    =========================================== */
    if($ajax_action == "trigger_clear") {
	if(isset($_GET["id"])) {
	    $trigger_id = intval($_GET["id"]);
	    doQuery("UPDATE Triggers SET raisedCount=0 WHERE ID='$trigger_id';");
	}
	echo "Counter cleared !";
    }

    /* ===========================================
    Add or edit TRIGGER
    =========================================== */
    if($ajax_action == "trigger_edit") {
	if(isset($_GET["id"])) {
	    $trigger_id = intval($_GET["id"]);
	    $result = doQuery("SELECT Event,agentId,Action,Priority,Args,isEnable FROM Triggers WHERE userId='$mySession->userId' AND ID='$trigger_id';");
	    if(mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$trigger_event = stripslashes($row["Event"]);
		$trigger_agentid = $row["agentId"];
		$trigger_action = stripslashes($row["Action"]);
		$trigger_priority = stripslashes($row["Priority"]);
		$trigger_args = stripslashes($row["Args"]);
		$trigger_isenable = $row["isEnable"];
	    }
	}

	echo "<form method='POST' id='ajaxDialog'>
	<input type='hidden' name='action' value='cb_trigger_edit'>";
	if(isset($trigger_id)) {
	    echo "<input type='hidden' name='trigger_id' value='$trigger_id'>";
	}
	echo "<div class='form-group'>
	    <span class='form-group-addon'>Agent:</span>
	    <select data-placeholder='Choose agent' class='form-control' id='trigger_agentid' name='trigger_agentid'>
		<option value='0' ".isSelected(0,$trigger_agentid).">Any</option>";
	$result = doQuery("SELECT ID,Name FROM Agents WHERE isEnable=1;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$agent_id = $row["ID"];
		$agent_name = stripslashes($row["Name"]);
		echo "<option value='$agent_id' ".isSelected($agent_id,$trigger_agentid).">$agent_name</option>";
	    }
	}
	echo "</select>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Event:</span>
	    <select data-placeholder='Choose event' class='form-control' id='trigger_event' name='trigger_event'>
		<option value='new_host' ".isSelected('new_host',$trigger_event).">New host detected</option>
		<option value='new_service' ".isSelected('new_service',$trigger_event).">New service detected</option>
		<option value='net_change' ".isSelected('net_change',$trigger_event).">Net change</option>
		<option value='host_change' ".isSelected('host_change',$trigger_event).">Host changed</option>
		<option value='host_offline' ".isSelected('host_offline',$trigger_event).">Host offline</option>
		<option value='host_online' ".isSelected('host_online',$trigger_event).">Host online</option>
		<option value='service_change' ".isSelected('service_change',$trigger_event).">Service change</option>
		<option value='service_down' ".isSelected('service_down',$trigger_event).">Service down</option>
		<option value='service_up' ".isSelected('service_up',$trigger_event).">Service up</option>
		<option value='agent_offline' ".isSelected('agent_offline',$trigger_event).">Agent offline</option>
		<option value='agent_start' ".isSelected('agent_start',$trigger_event).">Agent start</option>
		<option value='agent_stop' ".isSelected('agent_stop',$trigger_event).">Agent stop</option>
		<option value='job_start' ".isSelected('job_start',$trigger_event).">Job start</option>
		<option value='job_error' ".isSelected('job_error',$trigger_event).">Job error</option>
		<option value='job_end' ".isSelected('job_end',$trigger_event).">Job end</option>
	    </select>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Action:</span>
	    <select data-placeholder='Choose action when the event happen' class='form-control' id='trigger_action' name='trigger_action'>
		<option value='none' ".isSelected('none',$trigger_action).">Nothing</option>
		<option value='notify' ".isSelected('notify',$trigger_action).">Send notification</option>
		<option value='sendmail' ".isSelected('sendmail',$trigger_action).">Send mail</option>
	    </select>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Notify</span>
	    <select data-placeholder='Choose the notification time for this trigger' class='form-control' id='trigger_priority' name='trigger_priority'>
		<option value='asap' ".isSelected('asap',$trigger_priority).">As soon as possible</option>
		<option value='hourly' ".isSelected('hourly',$trigger_priority).">Hourly</option>
		<option value='daily' ".isSelected('daily',$trigger_priority).">Daily</option>
		<option value='weekly' ".isSelected('weekly',$trigger_priority).">Weekly</option>
	    </select>
	</div><div class='form-group'>
	    <span class='form-group-addon'>Trigger args<span>
	    <textarea id='trigger_args' name='trigger_args' class='form-control w-100 validate[funcCall[checkJSON]]'>$trigger_args</textarea>
	    <p class='help-block'>Action arguments, like email address, regular expression and so on in JSON format</p>
	</div><div class='form-group'>
	    <input type='checkbox' name='is_enable' ".isChecked($trigger_isenable)."> Enabled
	    <p class='help-block'>If checked, this trigger is enable</p>
	</div></form>";
    }
    /* ===========================================
    Remove TRIGGER
    =========================================== */
    if($ajax_action == "trigger_remove") {
	$trigger_id = intval($_GET["id"]);
	if($trigger_id > 0) {
	    echo "<form method='POST' id='ajaxDialog'>
	    <input type='hidden' name='action' value='cb_trigger_remove'>
	    <input type='hidden' name='trigger_id' value='$trigger_id'>
	    <div class='form-group'>
		<h2>Are your sure ?</h2>
		<p class='help-block'>Do you really want to remove this trigger ? This operation cannot be undone.</p>
	    </div>
	    </form>";
	}
    }

    /* ===========================================
    Edit CONFIG
    =========================================== */
    if($ajax_action == "config_edit") {
	$config_name = sanitize($_GET["name"]);
	if(strlen($config_name) > 0) {
	    $config_value = $myConfig->get($config_name);
	    echo "<form method='POST' id='ajaxDialog'>
	    <input type='hidden' name='action' value='cb_config_edit'>
	    <input type='hidden' name='config_name' value='$config_name'>
	    <div class='form-group'>
		<span class='form-group-addon'>Field name: <b>$config_name</b>
	    </div><div class='form-group'>
		<span class='form-group-addon'>Field value<span>
		<textarea id='config_value' name='config_value' class='form-control w-100'>".htmlspecialchars($config_value)."</textarea>
	    </div></form>";
	}
    }

    /* ===========================================
    Return INBOX new message
    =========================================== */
    if($ajax_action == "inbox_check") {
	/* Check if there's any unread messages in inbox.. */
	$result = doQuery("SELECT ID FROM Inbox WHERE userId='$myUser->id' AND isRead=0;");
	if(mysqli_num_rows($result) > 0) {
	    echo mysqli_num_rows($result);
	}
    }

    /* ===========================================
    Read INBOX message
    =========================================== */
    if($ajax_action == "inbox_read") {
	$inbox_id = intval($_GET["id"]);
	if($inbox_id > 0) {
	    $result = doQuery("SELECT Title, Content, isRead, addDate, readDate FROM Inbox WHERE userId='$myUser->id' AND ID='$inbox_id';");
	    if(mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result,MYSQL_ASSOC);
		$inbox_title = stripslashes($row["Title"]);
		$inbox_content = stripslashes($row["Content"]);
		$inbox_is_read = $row["isRead"];
		$inbox_adddate = new DateTime($row["addDate"]);
		$inbox_readdate = new DateTime($row["readDate"]);
		echo "<p>
		    ".$inbox_content."
		</p><p>
		    <small class='text-muted'><i class='glyphicon glyphicon-time'></i> Received on ".$inbox_adddate->format("d-m-Y")."</small>
		</p>";

		doQuery("UPDATE Inbox SET isRead=1,readDate=NOW() WHERE ID='$inbox_id';");
	    }
	}
    }

    /* ===========================================
    Mark as read INBOX new message
    =========================================== */
    if($ajax_action == "inbox_mark_read") {
	$inbox_id = intval($_GET["id"]);
	if($inbox_id > 0) {
	    doQuery("UPDATE Inbox SET isRead=1,readDate=NOW() WHERE ID='$inbox_id' AND userId='$myUser->id';");
	    echo "Message $inbox_id mark as read";
	}
    }

    if($ajax_action == "inbox_mark_all_read") {
	doQuery("UPDATE Inbox SET isRead=1,readDate=NOW() WHERE userId='$myUser->id';");
	echo "All message(s) marked as read";
    }
    /* ===========================================
    Delete INBOX new message
    =========================================== */
    if($ajax_action == "inbox_delete") {
	$inbox_id = intval($_GET["id"]);
	if($inbox_id > 0) {
	    doQuery("DELETE FROM Inbox WHERE ID='$inbox_id' AND userId='$myUser->id';");
	    echo "Message $inbox_id deleted";
	}
    }

    if($ajax_action == "inbox_delete_read") {
	doQuery("DELETE FROM Inbox WHERE isRead='1' AND userId='$myUser->id';");
	echo "Readed message(s) removed";
    }

    /* ===========================================
    TABLES JSON Data
    =========================================== */
    if($ajax_action == "table_get_jobs") {

	$result = doQuery("SELECT COUNT(*) AS Total FROM JobsQueue;");
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	
	$total_rows = intval($row["Total"]);
	
    	$order_by = strtoupper(sanitize($_GET["order"]));
	$offset = intval($_GET["offset"]);
	$limit = intval($_GET["limit"]);

	$result = doQuery("SELECT Job,itemId,agentId,Args,scheduleDate,startDate,endDate,timeElapsed FROM JobsQueue ORDER BY addDate $order_by LIMIT $limit OFFSET $offset;");
	if(mysqli_num_rows($result) > 0) {
	    $ret_array = array("total" => $total_rows);

	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {

		$job_method = stripslashes($row["Job"]);
		$job_id = $row["itemId"];
		$job_agent_id = ($row["agentId"] ? $row["agentId"] : "Any");

		$job_scheduledate = new DateTime($row["scheduleDate"]);

		$job_startdate = false;
		if($row["startDate"]) {
		    $job_startdate = new DateTime($row["startDate"]);
		}

		$job_enddate = false;
		if($row["endDate"]) {
		    $job_enddate = new DateTime($row["endDate"]);
		}

		$job_timeelapsed = $row["timeElapsed"];

		$ret_array["rows"][] = array("job" => $job_method, "id" => $job_id, "agent_id" => $job_agent_id, "schedule_date" => $job_scheduledate->format("H:i:s d-M-Y"), "start_date" => ($job_startdate ? $job_startdate->format("H:i:s d-M-Y") : "Not yet"), "end_date" => ($job_enddate ? $job_enddate->format("H:i:s d-M-Y") : "Not yet"), "time_elapsed" => $job_timeelapsed);
	    }
	    header('Content-Type: application/json');
	    $json = json_encode($ret_array);
	    echo $json;
	}
    }

    if($ajax_action == "table_get_eventlog") {
	$result = doQuery("SELECT COUNT(*) AS Total FROM EventsLog;");
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	
	$total_rows = intval($row["Total"]);
	
    	$order_by = strtoupper(sanitize($_GET["order"]));
	$offset = intval($_GET["offset"]);
	$limit = intval($_GET["limit"]);

	$result = doQuery("SELECT addDate,jobId,Event,Args FROM EventsLog ORDER BY addDate $order_by LIMIT $limit OFFSET $offset;");
	if(mysqli_num_rows($result) > 0) {
	    $ret_array = array("total" => $total_rows);

	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    		$log_adddate = new DateTime($row["addDate"]);
		$log_jobid = $row["jobId"];
		$log_event = stripslashes($row["Event"]);
		$log_args = $row["Args"];

		$ret_array["rows"][] = array("add_date" => $log_adddate->format("H:i:s d-M-Y"),"event" => $log_event,"agent_id" => $log_agentid,"job_id" => $log_jobid, "args" => $log_args);
	    }

	    header('Content-Type: application/json');
	    $json = json_encode($ret_array);
	    echo $json;
	}
    }
}

?>