<?php

// for console program, have no memory limit.
ini_set('memory_limit', -1);

$hostname = gethostname();
if ($hostname == 'cloud-dev') {
	define('APPLICATION_ENV', 'development');
	define('YII_DEBUG', true);
	define('YII_TRACE_LEVEL', 3);
} elseif ($hostname == 'cloud-bak1') {
	define('APPLICATION_ENV', 'testing');
} elseif ($hostname == 'Y-baiziying') {
	define('APPLICATION_ENV', 'development');
}  else {
	define('APPLICATION_ENV', 'production');
}

$yiic = realpath(__DIR__.'/../../lib/yii-1.1.13/yiic.php');
$config = realpath(__DIR__.'/config/'.strtolower(APPLICATION_ENV).'/console.php');

require_once($yiic);
