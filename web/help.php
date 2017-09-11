<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

include "common_head.php"; 

include_once "common_sidebar.php";


?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h1>Help</h1>
    Have you ever check the faboulous <a href='https://github.com/michelep/Nidan/wiki'>Nidan Wiki Pages</a> ? They really can help you :-)
</main>

<?php 

include "common_foot.php"; 

?>
