<?php

class ActivateForm extends CFormModel
{
	public $invitationCode;

	public function rules()
	{
		return array(
			array('invitationCode', 'required'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'invitationCode' => '邀请码',
		);
	}

	public function activate()
	{
		$modelFaceCompute = new ModelLogicFaceCompute();
		try {
			$modelFaceCompute->activate(Yii::app()->user->id, $this->invitationCode);
		} catch (ModelLogicException $e) {
			$this->addError('invitationCode', $e->getMessage());
			return false;
		}
		Yii::app()->user->isFaceUser = true;
		return true;
	}
}
