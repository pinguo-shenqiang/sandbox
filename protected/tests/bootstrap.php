<?php

$hostname = gethostname();
if ($hostname == 'cloud-dev') {
	define('APPLICATION_ENV', 'development');
	define('YII_DEBUG', true);
	define('YII_TRACE_LEVEL', 3);
} elseif ($hostname == 'cloud-bak1') {
	define('APPLICATION_ENV', 'testing');
} else {
	define('APPLICATION_ENV', 'production');
}

$yiit = realpath(__DIR__.'/../../../lib/yii-1.1.13/yiit.php');
$config = realpath(__DIR__.'/../config/'.strtolower(APPLICATION_ENV).'/test.php');

require_once($yiit);
require_once(__DIR__.'/WebTestCase.php');

Yii::createWebApplication($config);
