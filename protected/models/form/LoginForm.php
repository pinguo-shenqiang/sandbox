<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	public function rules()
	{
		return array(
			array('username, password', 'required'),
			array('username', 'email'),
			array('rememberMe', 'boolean'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'username' => '用户名',
			'password' => '密码',
			'rememberMe' => '记住我',
		);
	}

	public function login($verifyPassword = true)
	{
		$identity = new ModelLogicUserIdentity($this->username, $this->password);
		try {
			$identity->authenticate($verifyPassword);
		} catch (ModelLogicException $e) {
			if ($e->getCode() == 1) {
				$this->addError('username', $e->getMessage());
			} elseif ($e->getCode() == 2) {
				$this->addError('password', $e->getMessage());
			} else {
				$this->addError('error', $e->getMessage());
			}
			return false;
		}
		$duration = $this->rememberMe ? 30 * 86400 : 0;
		Yii::app()->user->login($identity, $duration);
		return true;
	}
}
