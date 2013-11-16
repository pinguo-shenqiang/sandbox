<?php
/* @var $this SiteController */
/* @var $modelForm ContactForm */
/* @var $form CActiveForm */

$this->pageTitle=Yii::app()->name . ' - 联系我们';
$this->breadcrumbs=array(
	'联系我们',
);
?>

<?php if(Yii::app()->user->hasFlash('contact')): ?>
	<div class="flash-success">
		<?php echo Yii::app()->user->getFlash('contact'); ?>
	</div>
<?php else: ?>
	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'contact-form',
			'enableClientValidation'=>true,
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
		)); ?>
		<div class="row">
			<?php echo $form->labelEx($modelForm, 'name'); ?>
			<?php echo $form->textField($modelForm, 'name'); ?>
			<?php echo $form->error($modelForm, 'name'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($modelForm, 'email'); ?>
			<?php echo $form->textField($modelForm, 'email'); ?>
			<?php echo $form->error($modelForm, 'email'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($modelForm, 'subject'); ?>
			<?php echo $form->textField($modelForm, 'subject', array('size'=>60, 'maxlength'=>128)); ?>
			<?php echo $form->error($modelForm, 'subject'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($modelForm, 'body'); ?>
			<?php echo $form->textArea($modelForm, 'body', array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($modelForm, 'body'); ?>
		</div>
		<?php if(CCaptcha::checkRequirements()): ?>
		<div class="row">
			<?php echo $form->labelEx($modelForm, 'verifyCode'); ?>
			<div>
			<?php $this->widget('CCaptcha'); ?>
			<?php echo $form->textField($modelForm, 'verifyCode'); ?>
			</div>
			<div class="hint">请输入图片中的验证码，不区分大小写。</div>
			<?php echo $form->error($modelForm, 'verifyCode'); ?>
		</div>
		<?php endif; ?>
		<div class="row buttons">
			<?php echo CHtml::submitButton('Submit'); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div><!-- form -->
<?php endif; ?>