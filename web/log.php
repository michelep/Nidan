<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$pageTitle = "Event Log";

include "common_head.php"; 

include_once "common_sidebar.php";

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Events log</h2>
    <div class="table-responsive">
	<table id="table"
               data-toggle="table"
               data-url="/ajax/?action=table_get_eventlog"
               data-height="100%"
               data-side-pagination="server"
               data-pagination="true"
               data-page-list="[5, 10, 20, 50, 100, 200]"
               data-search="true">
            <thead><tr>
		<th data-field="add_date" data-sortable="true">Date Time</th>
		<th data-field="event" data-sortable="true">Event</th>
		<th data-field="job_id" data-sortable="true">Job ID</th>
		<th data-field="args" data-sortable="true">Args</th>
	    </tr></thead>
	</table>
    </div>
</main>

<?php 

include "common_foot.php"; 

?>
