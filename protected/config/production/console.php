<?php

// config need to be merged
$config = CMap::mergeArray(
	require(__DIR__.'/main.php'),
	array(
		'name' => 'My Console Application',
	)
);

// config need to be replaced
$config ['components'] ['log']['routes'] = array (
	'file' => array (
		'class' => 'CFileLogRoute',
		'levels' => 'error, warning, info',
		'maxFileSize' => 10 * 1024,
		'maxLogFiles' => 1024,
	),
	'profile' => array (
		'class' => 'CFileLogRoute',
		'levels' => 'profile',
		'maxFileSize' => 10 * 1024,
		'maxLogFiles' => 100,
		'logFile' => 'profile.log',
	) 
);

return $config;