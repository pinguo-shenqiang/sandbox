<?php
/* @var $this SiteController */
/* @var $modelForm LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - 登录';
$this->breadcrumbs = array(
	'登录',
);
?>

<h1>登录</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'login-form',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
	)); ?>
	<div class="row">
		<?php echo $form->labelEx($modelForm, 'username'); ?>
		<?php echo $form->textField($modelForm, 'username'); ?>
		<?php echo $form->error($modelForm, 'username'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($modelForm, 'password'); ?>
		<?php echo $form->passwordField($modelForm, 'password'); ?>
		<?php echo $form->error($modelForm, 'password'); ?>
	</div>
	<div class="row rememberMe">
		<?php echo $form->checkBox($modelForm, 'rememberMe'); ?>
		<?php echo $form->label($modelForm, 'rememberMe'); ?>
		<?php echo $form->error($modelForm, 'rememberMe'); ?>
	</div>
	<div class="row">
		<?php echo $form->error($modelForm, 'error'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('登录'); ?>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- form -->
