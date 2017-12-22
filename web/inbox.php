<?php 

include_once "common.inc.php";

if(!$mySession->isLogged()) {
    header("Location: /signin.php");
    exit();
}

include "common_head.php"; 

include_once "common_sidebar.php";

?>
<div id="inbox-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="inbox-modal-label" aria-hidden="true">
    <div class="modal-dialog">
	<div class="modal-content">
	    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    		<h4 class="modal-title"></h4>
	    </div>
	    <div class="modal-body">
		<i class='fa fa-refresh fa-spin fa-3x fa-fw'></i><span class='sr-only'>Loading...</span>
	    </div>
	    <div class="modal-footer">
	        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	    </div>
	</div>
    </div>
</div>
<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3" id="contentDiv">
    <h2><i class="fa fa-envelope-o" aria-hidden="true"></i> Inbox</h2>
    <div class="row">
	<table class="table table-hover">
	    <thead>
	        <tr>
	    	    <th></th>
		    <th>Title</th>
		    <th></th>
		    <th>Date</th>
		    <th></th>
		</tr>
	    </thead>
	    <tbody id="inboxList">
<?php
	    if(isset($_GET["p"])) {
		$page_num = abs(intval(sanitize($_GET["p"])));
	    } else {
		$page_num = 1;
	    }

	    $row_offs = ($page_num-1) * 10;

	    $result = doQuery("SELECT ID FROM Inbox WHERE userId=$myUser->id;");
	    $total_rows = mysqli_num_rows($result);

	    $result = doQuery("SELECT ID, Title, Content, isRead, addDate FROM Inbox WHERE userId=$myUser->id ORDER BY addDate DESC LIMIT 10 OFFSET $row_offs;");
	    if(mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) {
		    $inbox_id = $row["ID"];
		    $inbox_title = stripslashes($row["Title"]);
		    $inbox_content = stripslashes($row["Content"]);
		    $inbox_is_read = $row["isRead"];
		    $inbox_adddate = new DateTime($row["addDate"]);

		    echo "<tr class=\"".($inbox_is_read ? "":"table-info")."\">
			<td></td>
			<td>$inbox_title</td>
			<td class='ajax-clickable' data-href='ajax?action=inbox_read&id=$inbox_id' data-title='$inbox_title'>".getExcerpt(strip_tags($inbox_content),20)."</td>
			<td>".$inbox_adddate->format('d-m-Y h:m:s')."</td>
			<td>
			    <a class='nav-link ajaxCall' title='Delete' href='/ajax?action=inbox_delete&id=$inbox_id'><i class='fa fa-trash' aria-hidden='true'></i></a>";
		    if(!$inbox_is_read) {
			echo "<a class='nav-link ajaxCall' title='Mark as read' href='/ajax?action=inbox_mark_read&id=$inbox_id'><i class='fa fa-eye' aria-hidden='true'></i></a>";
		    }
		    echo "</td>
		    </tr>";
		}
	    } else {
		echo "<tr><td colspan=10>No messages</td></tr>";
	    }
?>
	    <tr>
		<td colspan=10>
		    <?php getPagination($page_num,$total_rows,'/inbox',10); ?>
		</td>
	    </tr></tbody>
	</table>
	<div class="clearfix">&nbsp;</div>
	<div class="btn-group" role="group" aria-label="Network actions">
	    <a class="btn btn-secondary ajaxCall" href="/ajax?action=inbox_delete_read" title="Remove readed message"><i class="fa fa-trash" aria-hidden="true"></i> Remove readed messages</a>
	    <a class="btn btn-secondary ajaxCall" href="/ajax?action=inbox_mark_all_read" title="Mark all as readed"><i class="fa fa-trash" aria-hidden="true"></i> Mark all as readed</a>
	</div>
    </div>
</main>

<?php 

include "common_foot.php";

?>