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
    <script src="/js/Chart.bundle.min.js"></script>
    <script src="/js/noty.min.js"></script>
    <script src="/js/common.js"></script>

<?php
$local_js = basename($_SERVER['SCRIPT_FILENAME'],".php").".js";

if(file_exists("./js/".$local_js)) {
    echo "\t<!-- local JS -->\n\t<script src=\"/js/".$local_js."\"></script>\n";
}

$result = doQuery("SELECT ID,Type,Message FROM SessionMessages WHERE sessionId='$mySession->ID' ORDER BY addDate DESC;");
if(mysqli_num_rows($result) > 0) {
?>
	<script type="text/javascript">
	$(document).ready(function () {
<?php
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$id = $row["ID"];
	$type = $row["Type"];
	$message = $row["Message"];
	echo "printNotice(\"$message\",\"$type\");\n";

	doQuery("DELETE FROM SessionMessages WHERE ID='$id';");
    }
    echo "});</script>";
}
?>

</body></html>
