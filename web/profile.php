<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$pageTitle = "User profile";

include "common_head.php"; 

include_once "common_sidebar.php";

$myUser = new User($mySession->userId);

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <div class="panel panel-default col-8">
	<div class="panel-heading">
	    <h3 class="panel-title">Account details</h3>
	</div>
	<form method="POST">
	    <input type="hidden" name="action" value="cb_account_edit">
	    <div class="panel-body">
		<div class="form-group">
		    <label for="user-email">E-Mail</label>
		    <input type="text" class="form-control" placeholder="E-mail address" id="user-email" aria-describedby="user-email" name="email" value="<?php echo $myUser->eMail; ?>">
	        </div>
		<div class="form-group">
		    <label for="user-name">Username</label>
		    <input type="text" class="form-control" placeholder="Username" id="user-name" aria-describedby="user-name" name="username" value="<?php echo $myUser->Name; ?>">
	        </div>
		<div class="form-group">
		    <label for="user-password">Password</label>
		    <input type="password" class="form-control" placeholder="Password" id="user-password" aria-describedby="user-password" name="password">
		</div>
		<div class="form-group">
		    <label for="user-password-val">Validate</label>
		    <input type="password" class="form-control" placeholder="Type again your new password or left blank to skip" id="user-password-val" aria-describedby="user-password-val" name="password-val">
		</div>
		<div class="form-group">
		    <label for="user-alias">Alias</label>
		    <input type="text" class="form-control" placeholder="Alias" id="user-alias" aria-describedby="user-alias" name="alias" value="<?php echo $myUser->Alias; ?>">
		</div>
		<button type="submit" class="btn btn-default">Save</button>
	    </div>
	</form>
    </div>
</main>

<?php 

include "common_foot.php"; 

?>
