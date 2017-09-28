<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$pageTitle = "System configuration";

include "common_head.php"; 

include_once "common_sidebar.php";

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>System Configuration</h2>
    <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
    		<th>Name</th>
		<th>Value</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
	$result = doQuery("SELECT Name, Value FROM Config ORDER BY Name;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	        $config_name = stripslashes($row["Name"]);
		$config_value = stripslashes($row["Value"]);
		echo "<tr>
		    <td>$config_name</td>
		    <td>".htmlspecialchars($config_value)."</td>
		    <td>
			<a class='ajaxDialog' title='Edit field' href='/ajax?action=config_edit&name=$config_name'><i class='fa fa-pencil-square' aria-hidden='true'></i></a>
		    </td>
		</tr>";
	    }
	}
?>
	</tbody></table>
	<div class="clearfix">&nbsp;</div>
	<div class="btn-group" role="group" aria-label="Actions">
	    <a class="btn btn-secondary ajaxCall" href="/ajax?action=mail_test"><i class="fa fa-envelope-o" aria-hidden="true"></i> Send test mail </a>
	</div>
    </div>
</main>

<?php

include "common_foot.php";

?>
