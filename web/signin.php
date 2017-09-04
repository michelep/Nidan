<?php 

include_once "common.inc.php";

if($mySession->isLogged()) {
    header("Location: /");
    exit();
}

include "common_head.php"; 

?>

<div class="container-fluid">
    <div class="login-container">
	<div class="text-center">
	    <img src="/img/logo-login.png" alt="Nidan logo" class="img-circle">
	</div>
	<form class="form-signin" method="POST">
	    <input type="hidden" name="action" value="signin">
	    <h2 class="form-signin-heading">Please sign in</h2>

	    <label for="inputEmail" class="sr-only">Email address</label>
	    <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>

    	    <label for="inputPassword" class="sr-only">Password</label>
    	    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>

	    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
	</form>
    </div>
</div>

<?php 

include "common_foot.php"; 

?>
