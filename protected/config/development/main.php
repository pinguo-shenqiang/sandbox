<?php

// config need to be merged
$config = CMap::mergeArray(
	require(__DIR__.'/../production/main.php'),
	array(
		'components' => array(
			'dbCloud' => array(
 				'server' => 'mongodb://cloud-dev:27017',
				//'server' => 'mongodb://wws3:27194',
			),
			'dbCloudLogBackup' => array(
 				'server' => 'mongodb://cloud-dev:27017',
			),
			'dbFace' => array(
				'server' => 'mongodb://cloud-dev:27017',
			),
			'dbPhotoMeta' => array(
				'server' => 'mongodb://cloud-dev:27017',
			),
			'dbImgProc' => array(
				'server' => 'mongodb://cloud-dev:27017',
				//'server' => 'mongodb://127.0.0.1:27017,127.0.0.1:27018,127.0.0.1:27019/?replicaSet=rs0',
			),
			'log' => array(
				'routes'=>array(
					'file' => array(
						'levels' => 'error, warning, info, trace',
					),
					//'web' => array(
					//	'class' => 'CWebLogRoute',
					//	'levels' => 'error, warning, info',
					//),
// 					'profileLogRoute' => array(
// 						'class' => 'CProfileLogRoute',
// 						'report' => 'summary',
// 					),
				),
			),
		),
		'params' => array(
			'faceBucket' => 'facedev',
			'zabbixServer' => array(
				'host' => 'cloud-dev'
			),
			'uploadTempDir' => '/home/lihai/data/www/runtime/imgproc/tempfile',
			'siteUrl'	=> 'http://imgproc-lihai.camera360.com/',
		),
	)
);

// config need to be replaced
$config ['components'] ['dbCloud']['options'] = array(
	'connect' => false,
);
$config ['components'] ['dbCloudLogBackup']['options'] = array(
	'connect' => false,
);
$config ['components'] ['dbFace']['options'] = array(
	'connect' => false,
);
$config ['components'] ['dbPhotoMeta']['options'] = array(
	'connect' => false,
);
$config ['components'] ['dbImgProc']['options'] = array(
	'connect' => false,
);

$config ['components'] ['dbFaceMQ']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 6379),
);
$config ['components'] ['dbFaceRedisDB']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 6379),
);
$config ['components'] ['dbSignatureMQ']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 6379),
);
$config ['components'] ['dbSeparateMQ']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 6379),
);
$config ['components'] ['dbAudioImageMQ']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 6379),
);
$config ['components'] ['cacheSession']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 6379),
);

$config ['params'] ['faceDetectService']['servers'] = array(
	array('host' => '192.168.1.46', 'port' => 9090),
);
$config ['params'] ['faceRecognizeService']['servers'] = array(
	array('host' => '192.168.1.46', 'port' => 9090),
);
$config ['params'] ['signatureSignService']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 9090),
);
$config ['params'] ['signatureSimilarService']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 9090),
);
$config ['params'] ['signatureSameService']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 9090),
);
$config ['params'] ['audioimageService']['servers'] = array(
	array('host' => 'cloud-dev', 'port' => 9091)
);

return $config;
