<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$pageTitle = "Groups";

include "common_head.php"; 

include_once "common_sidebar.php";

if($myUser->getACL('manageUsers')) {
?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Groups</h2>
    <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
		<th>Name</th>
		<th>ACL</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
	$result = doQuery("SELECT ID,Name,ACL FROM Groups ORDER BY Name;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	        $group_id = $row["ID"];
	        $group_name = stripslashes($row["Name"]);
		echo "<tr>
		    <td>$group_name</td>
		    <td>";
		foreach(unserialize(stripslashes($row["ACL"])) as $key => $value) {
		    if($value) {
			echo "<span class='badge badge-pill badge-info'> $key </span>&nbsp;";
		    }
		}
		echo "<td>";
		if($myUser->getACL("manageGroups")) {
		    echo "<a class='btn ajaxDialog' title='Edit group' href='/ajax?action=group_edit&id=$group_id'><i class='fa fa-pen-square' aria-hidden='true'></i></a>
	    		<a class='btn ajaxDialog' title='Remove group' href='/ajax?action=group_remove&id=$group_id'><i class='fa fa-times text-danger' aria-hidden='true'></i></a>";
		}
		echo "</td></tr>";
	    }
	}
?>
	</tbody></table>
    </div>
    <div class="btn-group" role="group" aria-label="Groups actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=group_edit" title="Add new group"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new group</a>
    </div>
</main>

<?php
} else {
    echo "Access denied by ACL configuration";
}

include "common_foot.php";

?>
