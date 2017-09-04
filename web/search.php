<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

$query = $_GET["q"];

$pageTitle = "Search for '$query'";

include "common_head.php"; 

include_once "common_sidebar.php";

?>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2>Search results</h2>
    <div class="table-responsive">
	<table id="table"
               data-toggle="table"
               data-url="/ajax/?action=table_get_search_results&search_id="
               data-height="100%"
               data-side-pagination="server"
               data-pagination="true"
               data-page-list="[5, 10, 20, 50, 100, 200]"
               data-search="true">
            <thead><tr>
		<th data-field="job">Job</th>
		<th data-field="id" data-sortable="true">ID</th>
		<th data-field="add_date" data-sortable="true">Added on</th>
		<th data-field="next_check" data-sortable="true">Next check</th>
		<th data-field="start_date" data-sortable="true">Start Date</th>
		<th data-field="end_date" data-sortable="true">End Date</th>
	    </tr></thead>
	</table>
    </div>
</main>

<?php 

include "common_foot.php"; 

?>
