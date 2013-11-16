<?php

class Controller extends CController
{
	public $layout = '//layouts/main';
	public $menu = array();
	public $breadcrumbs = array();

	public function init()
	{
		parent::init();

		Yii::app()->language = Yii::app()->request->preferredLanguage;
	}

	public function filterFaceUser($filterChain)
	{
		if (!Yii::app()->user->isFaceUser) {
			$this->redirect('/');
		} else {
			$filterChain->run();
		}
	}

	/**
	 * 获取IP地址
	 */
	protected function getIp()
	{
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		}
		else {
			$ip = $_SERVER ['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function renderJSON($data=null, $error='', $errno=0)
	{
		$this->layout=false;
		header('Content-type: application/json');
		echo json_encode(array('data'=>$data, 'error'=>$error, 'errno'=>$errno));
		Yii::app()->end();
		exit();
	}
}
