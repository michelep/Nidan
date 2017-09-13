<?php

use \RestServer\RestException;

class NidanController
{
    var $agent_id=NULL;

    /**
     * Agent authentication
     *
     */
    function authorize() {
	global $DB;

	// All requests include the following headers by default:

	// 'X-Authentication-Key' - The API Key provided when creating the ApiClient object.
	$headers = apache_request_headers();

	$agent_apikey = sanitize($headers["X-Authentication-Key"]);
	
	$result = doQuery("SELECT ID FROM Agents WHERE apikey='$agent_apikey' AND isEnable=1;");
	if(mysqli_num_rows($result) > 0) {
	    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	    $this->agent_id = $row["ID"];
	    doQuery("UPDATE Agents SET lastSeen=NOW(),isOnline=1 WHERE ID='$this->agent_id';");
	    return true;
	}
	return false;
    }

    /**
    * Agent Start
    * 
    * @url POST /agent/start
    * @url POST /agent/start/$id
    */
    public function agent_start($id=NULL) {
	global $DB;

	LOGWrite("REST::agent_start($id)",LOG_DEBUG);

	if($this->agent_id) {
	    $hostname = mysqli_real_escape_string($DB,$_POST["hostname"]);

	    doQuery("UPDATE Agents SET isOnline=1,IP='".getClientIP()."',Hostname='$hostname' WHERE ID='$this->agent_id';");

	    // Mark as not started staled JOBs for this agent...
	    doQuery("UPDATE JobsQueue SET startDate=NULL WHERE agentId='$this->agent_id' AND endDate IS NULL");
	
	    $args = array('hostname' => $hostname, 'ip' => getClientIP());
	    raiseEvent($this->agent_id,NULL,"agent_start",$args);

	    return array("success" => "OK");
	} else {
	    throw new RestException(403, "Access denied");
	}
	
    }

    /**
    * Agent Stop
    * 
    * @url POST /agent/stop
    * @url POST /agent/stop/$id
    */
    public function agent_stop($id=NULL) {
	global $DB;

	LOGWrite("REST::agent_stop($id)",LOG_DEBUG);

	if($this->agent_id) {
	    $reason = mysqli_real_escape_string($DB,$_POST["reason"]);
	    
	    doQuery("UPDATE Agents SET isOnline=0 WHERE ID='$this->agent_id';");

	    $args = array('reason' => $reason, 'ip' => getClientIP());
	    raiseEvent($this->agent_id,NULL,"agent_stop",$args);

	    return array("success" => "OK");
	} else {
	    throw new RestException(403, "Access denied");
	}
	
    }
    
    /**
     * Get job id (or next job) for calling Agent
     *
     * @url POST /job/get
     * @url POST /job/get/$id
     */
    public function job_get($job_id=NULL) {
	global $DB;
    
	LOGWrite("REST::job_get($job_id)::".var_export($_POST, true),LOG_DEBUG);

	if($this->agent_id) {
	    $agent = new Agent($this->agent_id);
	    
	    if($agent) {
		if($job_id == NULL) {
		    $job_id = $agent->getNextJob();
		}

		if($job_id) {
	    	    $job = new Job($job_id);
	    	    $job->setStart($this->agent_id);

		    // If it's a network scan, ON START take a snapshot of the situation for latter comparison...
		    if($job->job == "net_scan") {
			$network = new Network($job->itemId);
			$job->setCache($network->getHosts());
		    }
		    // ...or, if it's an host scan, take a services snapshots
		    if($job->job == "host_scan") {
			$host = new Host($job->itemId);
			$job->setCache($host->getServices());
		    }
		    raiseEvent($this->agent_id,$job->id,"job_start");

		    return array("success" => "$job_id", "job_id"=> $job->id, "job_type" => $job->job, "job_args" => $job->args);
	        } else {
		    return array("success" => "0");
		}
	    } else {
		throw new RestException(500, "Agent ID error");
	    }
	} else {
	    throw new RestException(403, "Access denied");
	}
    }

    /**
     * Set agent job status
     *
     * @url POST /job/set/$id
     */
    public function job_set($id=NULL) {
	global $DB;

	LOGWrite("REST::job_set($id)::".var_export($_POST, true),LOG_DEBUG);

	// REST::job_set(11)::array (#012  'status' => 'complete',#012  'scantime' => '18.7868750095',#012)

	if($this->agent_id) {
	    $agent = new Agent($this->agent_id);
	    
	    if($agent) {
		$job = new Job($id);
	    	if($job) {
		    switch($_POST["status"]) {
//		    	REST::job_set(2087)::array (#012  'status' => 'error',#012  'reason' => '<traceback object at 0x7f7d8dcc5a28>',#012)
			case 'error':
			    $reason = sanitize($_POST["reason"]);
			    raiseEvent($this->agent_id,$job->id,"job_error",array("reason" => $reason));
			    break;
		        case 'complete':
// 			REST::job_set(1034)::array (#012  'status' => 'complete',#012  'scantime' => '0.521373987198',#012)
			    $scantime = floatval($_POST["scantime"]);
			    $job->setEnd($this->agent_id,$scantime);

			    // If it's a network scan, update other fields and check for changes...
			    if($job->job == "net_scan") {
				doQuery("UPDATE Networks SET scanTime='$scantime',lastCheck=NOW() WHERE ID='$job->itemId';");
				// Retrieve previous scenario
				$network = new Network($job->itemId);
				$old_net_scenario = $job->getCache();
				$new_net_scenario = $network->getHosts();

				if(($old_net_scenario) && ($new_net_scenario)) {
				    $arr_res = array_diff_assoc($new_net_scenario,$old_net_scenario);
				    if(count($arr_res) > 0) {
					LOGWrite("REST::net_scan_compare::".var_export($arr_res, true),LOG_DEBUG);
					raiseEvent($this->agent_id,$job->id,"net_change",array("changes" => $arr_res));
				    } else {
					LOGWrite("REST::net_scan_compare::NO CHANGES",LOG_DEBUG);
				    }
				} else {
				    LOGWrite("REST::net_scan_compare($job->id)::FIRST TIME::".var_export($old_net_scenario, true)."-".var_export($new_net_scenario, true)."",LOG_DEBUG);
				}
			    }
			    // ...or, if it's an host scan, do the same: check for services changes
			    if($job->job == "host_scan") {
				doQuery("UPDATE Hosts SET scanTime='$scantime',lastCheck=NOW() WHERE ID='$job->itemId';");
				// Retrieve previous scenario
				$host = new Host($job->itemId);
				$old_host_scenario = $job->getCache();
				$new_host_scenario = $host->getServices();

				if(($old_host_scenario) && ($new_host_scenario)) {
				    $arr_res = array_diff_assoc($new_host_scenario,$old_host_scenario);
				    if(count($arr_res) > 0) {
					LOGWrite("REST::host_scan_compare::".var_export($arr_res, true),LOG_DEBUG);
					raiseEvent($this->agent_id,$job->id,"host_change",array("changes" => $arr_res));
				    } else {
					LOGWrite("REST::host_scan_compare::NO CHANGES",LOG_DEBUG);
				    }
				} else {
				    LOGWrite("REST::host_scan_compare($job->id)::FIRST TIME::".var_export($old_host_scenario, true)."::".var_export($new_host_scenario, true)."",LOG_DEBUG);
				}
			    }

			    // and finally, raise event !
			    raiseEvent($this->agent_id,$job->id,"job_end");
			    break;
			default:
			    break;
		    }
		}
		return array("success" => "0");
	    } else {
	        throw new RestException(500, "Agent Error");
	    }
        } else {
    	    throw new RestException(403, "Access denied");
	}
	
    }

    /**
     * Host add - Called by Nidan agent while scanning a network
     *
     * @url POST /host/add
     */
    public function host_add() {
	global $DB;

	LOGWrite("REST::host_add()::".var_export($_POST, true),LOG_DEBUG);

	// array (#012  'vendor' => '',#012  'job_id' => '9',#012  'ip' => '172.16.40.12',#012  'hostname' => 'pc-vannucchi-gd.ammi.unisi.it',#012  'state' => 'up',#012  'mac' => '',#012)

	if($this->agent_id) {
	    $agent = new Agent($this->agent_id);
	    
	    if($agent) {
		$job_id = sanitize($_POST["job_id"]);

		$ip = sanitize($_POST["ip"]);
		$hostname = sanitize($_POST["hostname"]);
		$mac = sanitize($_POST["mac"]);
		$vendor = sanitize($_POST["vendor"]);
		$state = sanitize($_POST["state"]);

		$job = new Job($job_id);
		// $job->itemId contains netId
		if($job) {
		    /* Check if this host is new or not... */
		    $result = doQuery("SELECT ID FROM Hosts WHERE netId='$job->itemId' AND IP='$ip';");
		    if(mysqli_num_rows($result) > 0) {
			/* Host already there: check for changes */
			$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
			$host = new Host($row["ID"]);
			// Prepare array for comparison...
			$old_host["hostname"] = $hostname;
			$old_host["mac"] = $mac;
			$old_host["vendor"] = $vendor;
			$old_host["state"] = $state;
		    
			$new_host["hostname"] = $host->hostname;
			$new_host["mac"] = $host->mac;
			$new_host["vendor"] = $host->vendor;
			$new_host["state"] = $host->state;

			$arr_res = array_diff_assoc($new_host,$old_host);
			if(count($arr_res) > 0) {
			    //Something has changed...so raise event !
			    raiseEvent($this->agent_id,$job_id,"host_change",$arr_res);
			}

			return array("success" => "OK");
		    } else {
			// New host: add to DB
			doQuery("INSERT INTO Hosts(netId,agentId,IP,MAC,Vendor,Hostname,State,isOnline,addDate,stateChange,checkCycle) VALUES ('$job->itemId','$this->agent_id','$ip','$mac','$vendor','$hostname','$state','1',NOW(),NOW(),10);");
			$host_id = mysqli_insert_id($DB);
			if($host_id > 0) {
			    // Prepare to raise event...
			    $args = array('id' => $host_id, 'hostname' => $hostname, 'ip' => $ip);
			    raiseEvent($this->agent_id,$job_id,"new_host",$args);
			}
			return array("success" => "OK");
		    }
		} else {
		    throw new RestException(500, "Job ID Error");
		}
	    } else {
	        throw new RestException(500, "Agent Error");
	    }
	} else {
	    throw new RestException(403, "Access denied");
	}
    }
    /**
     * Service add - Called when an agent found an open port while scanning a host
     *
     * @url POST /service/add
     */
    public function service_add() {
	global $DB;

	LOGWrite("REST::service_add()::".var_export($_POST, true),LOG_DEBUG);

	if($this->agent_id) {
	    $agent = new Agent($this->agent_id);
	    
	    if($agent) {
		$job_id = sanitize($_POST["job_id"]);

		$ip = sanitize($_POST["ip"]);
		$port = sanitize($_POST["port"]);
		$proto = sanitize($_POST["proto"]);
		$state = sanitize($_POST["state"]);
		$banner = mysqli_real_escape_string($DB,sanitize($_POST["banner"]));

		$job = new Job($job_id);
		// $job->itemId contains hostId
		if($job) {
		    $result = doQuery("SELECT ID,State,Banner FROM Services WHERE hostId='$job->itemId' AND Port='$port' AND Proto='$proto';");
		    if(mysqli_num_rows($result) > 0) {
			// Seems that this service was already there: check for changes
			$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
			$old_service["state"] = $row["State"];
			$old_service["banner"] = stripslashes($row["Banner"]);

			$new_service["state"] = $state;
			$new_service["banner"] = stripslashes($banner);

			$arr_res = array_diff_assoc($new_service,$old_service);
			if(count($arr_res) > 0) {
			    //Something has changed...so raise event !
			    raiseEvent($this->agent_id,$job_id,"service_change",$arr_res);
			}

			return array("success" => "OK");
		    } else {
			// New service found
			doQuery("INSERT INTO Services(hostId,Port,Proto,State,Banner,addDate,lastSeen) VALUES ('$job->itemId','$port','$proto','$state','$banner',NOW(),NOW());");
			$service_id = mysqli_insert_id($DB);

			if($service_id > 0) {
			    // Prepare to raise event..
			    $args = array('id' => $service_id, 'port' => $port, 'proto' => $proto, 'state' => $state);
			    raiseEvent($this->agent_id,$job_id,"new_service",$args);
			}

			return array("success" => "OK");
		    }
		} else {
		    throw new RestException(500, "Job ID Error");
		}
	    } else {
	        throw new RestException(500, "Agent Error");
	    }
	} else {
	    throw new RestException(403, "Access denied");
	}
    }
}