<?php

// A comfortable script to backup your nidan database

include __DIR__."/../common.inc.php";

$filename = "backup-" . date("d-m-Y") . ".sql";

if ( php_sapi_name() === 'cli' ) {
    $cmd = "mysqldump --single-transaction -u ".$CFG["db_user"]." --password=".$CFG["db_password"]." -h ".$CFG["db_host"]." ".$CFG["db_name"]." > ".$filename;
    passthru($cmd);
} else {
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$filename");

    $cmd = "mysqldump --single-transaction -u ".$CFG["db_user"]." --password=".$CFG["db_password"]." -h ".$CFG["db_host"]." ".$CFG["db_name"];
    passthru($cmd);
}

?>