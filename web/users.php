<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$pageTitle = "Users";

include "common_head.php"; 

include_once "common_sidebar.php";

if($myUser->getACL('manageUsers')) {
?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Users</h2>
    <div class="table-responsive">
	<table class="table table-striped"><thead>
	    <tr>
		<th>Alias</th>
    		<th>Username</th>
		<th>eMail</th>
		<th>lastLogin</th>
		<th>ACL</th>
		<th></th>
	    </tr>
	</thead><tbody>
<?php
	$result = doQuery("SELECT ID,Name,eMail,Alias,ACL,addDate,lastLogin FROM Users ORDER BY Name;");
	if(mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	        $user_id = $row["ID"];
	        $user_name = stripslashes($row["Name"]);
	        $user_email = stripslashes($row["eMail"]);
	        $user_alias = stripslashes($row["Alias"]);
		$user_adddate = new DateTime($row["addDate"]);
		$user_lastlogin = new DateTime($row["lastLogin"]);

		echo "<tr>
		    <td>$user_alias</td>
		    <td>$user_name</td>
		    <td>$user_email</td>
		    <td>".($user_lastlogin ? $user_lastlogin->format("H:i:s d-M-Y"): "None")."</td>
		    <td></td>
		    <td>
			<a class='btn ajaxDialog' title='Edit user' href='/ajax?action=user_edit&id=$user_id'><i class='fa fa-pencil-square' aria-hidden='true'></i></a>
	    		<a class='btn ajaxDialog' title='Remove user' href='/ajax?action=user_remove&id=$user_id'><i class='fa fa-times text-danger' aria-hidden='true'></i></a>
		    </td>
		</tr>";
	    }
	}
?>
	</tbody></table>
    </div>
    <div class="btn-group" role="group" aria-label="Users actions">
	<a class="btn btn-secondary ajaxDialog" href="/ajax?action=user_edit" title="Add new user"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add new user</a>
    </div>
</main>

<?php
} else {
    echo "Access denied by ACL configuration";
}

include "common_foot.php";

?>
