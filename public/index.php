<?php
$hostname = gethostname();
if ($hostname == 'cloud-dev') {
	define('APPLICATION_ENV', 'development');
	define('YII_DEBUG', true);
	define('YII_TRACE_LEVEL', 3);
} elseif ($hostname == 'cloud-bak1') {
	define('APPLICATION_ENV', 'testing');
	define('YII_DEBUG', true);
	define('YII_TRACE_LEVEL', 3);
} elseif ($hostname == 'Y-baiziying') {
	define('APPLICATION_ENV', 'development');
} else {
	define('APPLICATION_ENV', 'production');
}

$yii = realpath(__DIR__.'/../../lib/yii-1.1.13/yii.php');
$config = realpath(__DIR__.'/../protected/config/'.strtolower(APPLICATION_ENV).'/main.php');

require_once($yii);

Yii::createWebApplication($config)->run();
