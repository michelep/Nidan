<?php

// -----------------------------------
//
// REMEMBER TO CHANGE TO REFLECT YOUR CONFIGURATION
//

$CFG["mailServerHost"] = "localhost";
$CFG["mailServerPort"] = 25;

$CFG["mailFromMail"] = "nidan@localhost";
$CFG["mailFromName"] = "Nidan";

$CFG["mailTemplate"] = "<style>
p {
    text-align: justify;
}

table { border-collapse: collapse; }
th { border-bottom: 1px solid #CCC; border-top: 1px solid #CCC; background-color: #EEE; padding: 0.5em 0.8em; text-align: center; font-weight:bold; }
td { border-bottom: 1px solid #CCC;padding: 0.2em 0.8em; }
td+td { border-left: 1px solid #CCC;text-align: center; }
</style>
<div style='padding: 5px;'>
%body%
</div>
<div style='width:100%; border-top: 1px solid #ccc; background-color: #eee; padding: 5px; text-align: center;'>
<b>Nidan</b>
</div>";

$CFG["db_host"] = "localhost";
$CFG["db_user"] = "nidan";
$CFG["db_password"] = "nidan";
$CFG["db_name"] = "nidan";
// -----------------------------------

?>