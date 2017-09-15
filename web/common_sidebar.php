<?php

function isActive($page) {
    global $_SERVER;
    
    $this_page = $_SERVER['REQUEST_URI'];

    if(strcmp($this_page,$page)==0) {
	echo "active";
    }
}

?>

<nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
    <ul class="nav nav-pills flex-column">
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/'); ?>" href="/">Networks</a>
	</li>
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/host'); ?>" href="/host">Hosts</a>
	</li>
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/agents'); ?>" href="/agents">Agents</a>
	</li>
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/triggers'); ?>" href="/triggers">Triggers</a>
	</li>
    </ul>
    <ul class="nav nav-pills flex-column">
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/jobs'); ?>" href="/jobs">Jobs queue</a>
	</li>
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/log'); ?>" href="/log">Event log</a>
	</li>
    </ul>
    <ul class="nav nav-pills flex-column">
<?php
if($myUser->getACL('manageSystem')) {
?>
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/config'); ?>" href="/config">Configuration</a>
	</li>
<?php
}
if($myUser->getACL('manageUsers')) {
?>
	<li class="nav-item">
	    <a class="nav-link <?php isActive('/users'); ?>" href="/users">Users</a>
	</li>
<?php
}
?>
    </ul>
    <ul class="nav nav-pills flex-column">
	<li class="nav-item">
	    <a class="nav-link" href="?action=logout">Logout</a>
	</li>
    </ul>
</nav>
