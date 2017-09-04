<?php

include __DIR__ . '/../common.inc.php';
require __DIR__ . '/RestServer/RestServer.php';
require __DIR__ . '/NidanController.php';

$server = new \RestServer\RestServer('debug');
$server->addClass('NidanController');
$server->handle();
