<?php
// Version
define('VERSION', '3.0.3.7');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}
function debug($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');