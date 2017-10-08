<?php

include "../config.inc.php";

$dbTables = array("Agents","Config","EventsLog","Hosts","JobsQueue","Log","Networks","Services","SessionMessages","Sessions","Stats","Triggers","Users");

global $mysqli;

function check_db($host,$port,$user,$password,$name) {
    global $mysqli;

    $mysqli = mysqli_init();
    if (!$mysqli) {
        return "mysqli_init() failed !";
    }

    if (!$mysqli->real_connect($host, $user, $password, $name)) {
        return mysqli_connect_errno();
    }
    return false;
}

function do_query($query) {
    global $mysqli;
    $result = $mysqli->query($query);
    if($result === false) {
	echo "Error while executing query '$query': ".$mysqli->error;
	return false;
    }
    usleep(500);
    return $result;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Nidan">
    <meta name="author" content="Michele 'O-Zone' Pinassi">
    <link rel="icon" href="/favicon.ico">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/tether.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/bootstrap-table.min.css" rel="stylesheet">
    <link href="/css/validationEngine.jquery.css" rel="stylesheet">
    <link href="/css/jquery-ui.min.css" rel="stylesheet">
    <link href="/css/noty.css" rel="stylesheet">
    <link href="/css/common.css" rel="stylesheet">

    <title>Nidan installer</title>
</head><body>
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
	<button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarTop" aria-controls="navbarTop" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	</button>
	<a class="navbar-brand" href="/"><img src="/img/header_logo.png" class="img-responsive header-logo" /> Nidan installer</a>
    </nav>
    <div class="container-fluid"><!-- CONTAINER -->
	<div class="row"><!-- ROW -->
	    <main class="col-sm-8 py-4 offset-sm-2" id="contentDiv">
<?php
if(isset($_POST["step"])) {
    $step = intval($_POST["step"]);
    if($step == 1) { // Check database connectivity
	$db_host = $_POST["db_host"];
	$db_port = $_POST["db_port"];
	$db_user = $_POST["db_user"];
	$db_name = $_POST["db_name"];
	$db_password = $_POST["db_password"];

	$res = check_db($db_host,$db_port,$db_user,$db_password,$db_name);
	if($res != false) {
	    echo "<div class='alert alert-warning'>
		Error $res
	    </div>";
	    switch($res) {
		case 1049: // No DB "nidan": create db and import database schema
		    $step = 1;
		    break;
		default:
		    $step = 0;
	    }
	} else {
	    echo "<div class='alert alert-success'>
		<strong>Well done!</strong> Successfully connected to the DB. Now check DB structure...
	    </div>";
	    //
	    $tables = do_query("SHOW TABLES;");
	    if($tables) {
		while ($row = $tables->fetch_array()) {
		    $tmpTables[] = $row[0];
		}
		// Free result set
		$tables->close();

		$result = array_diff($dbTables, $tmpTables);
		if(count($result) > 0) {
		    echo "<div class='alert alert-warning'>
			<strong>Ooops !</strong> DB seems to be not updated...
		    </div>";
		    $step = 2;
		} else {
		    echo "<div class='alert alert-success'>
			<strong>OK !</strong> DB seems to be ok, but we check deeply...
		    </div>";
		    foreach($dbTables as $table) {
			$tb = do_query("SHOW COLUMNS FROM $table;");
			while ($row = $tb->fetch_array()) {
			    array_unshift($row, $table);
			    print_r($row);
			}
			$tb->close();
		    }
		}
	    } else {
		echo "<div class='alert alert-warning'>
		    <strong>Ooops !</strong> No tables here ? 
		</div>";
		$step = 2;
	    }
	}
    }
} 

if($step == 0) {// First step: check DB connection
?>
		<p class="h1">1. Check connection with DBMS</p>
		<form method="POST">
		    <input type="hidden" name="step" value="1">
		    <div class="form-group">
			<label for="dbHost">Database hostname</label>
			<input type="text" class="form-control validate[required]" id="dbHost" name="db_host" placeholder="Hostname or IP of your DBMS (i.e. localhost)" value="<?php echo $CFG["db_host"]; ?>">
		    </div><div class="form-group">
			<label for="dbPort">Database port</label>
			<input type="text" class="form-control validate[required]" id="dbPort" name="db_port" placeholder="Port of your DBMS (i.e. 3306)" value="<?php echo $CFG["db_port"]; ?>">
		    </div><div class="form-group">
			<label for="dbUser">Database user</label>
			<input type="text" class="form-control validate[required]" id="dbUser" name="db_user" placeholder="DBMS user (i.e. root)" value="<?php echo $CFG["db_user"]; ?>">
		    </div><div class="form-group">
			<label for="dbUser">Database name</label>
			<input type="text" class="form-control validate[required]" id="dbName" name="db_name" placeholder="Database name (i.e. nidan)" value="<?php echo $CFG["db_name"]; ?>">
		    </div><div class="form-group">
			<label for="dbPassword">Database password</label>
			<input type="password" class="form-control validate[required]" id="dbPassword" name="db_password" placeholder="DBMS password" value="<?php echo $CFG["db_password"]; ?>">
		    </div><div class="form-group">
			<input type="submit" value="Check database">
		    </div>
		</form>
<?php
} else if ($step == 1) { // Import DB scripts
?>
    <p class="h1">2. Create database and tables</p>
    <form method="POST">
	<input type="hidden" name="step" value="2">

    </form>
<?php
} else if ($step == 2) { // Check tables

}
?>
	    </main>
	</div><!-- /ROW -->
    </div><!-- /CONTAINER -->
    <footer class="footer">
        <div class="container">
	    <div class="row justify-content-md-center">
    	        <div class="text-center">
            	    <h4>
			<strong>Nidan</strong>
        	    </h4>
        	    <p>Made with <i class="fa fa-heart fa-fw"></i> in Siena, Tuscany, Italy</p>
		    <p>by O-Zone &lt;<a href="mailto:o-zone@zerozone.it">o-zone@zerozone.it</a>&gt;</p>
        	</div>
    	    </div>
	</div>
    </footer>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/jquery.min.js"><\/script>')</script>
    <script src="/js/tether.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    <script src="/js/bootstrap-table.min.js"></script>
    <script src="/js/jquery.validationEngine-en.js"></script>
    <script src="/js/jquery.validationEngine.js"></script>
    <script src="/js/common.js"></script>
</body></html>
