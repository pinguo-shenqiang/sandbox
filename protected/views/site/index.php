<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>欢迎你访问<i><?php echo CHtml::encode(Yii::app()->name); ?></i>！</h1>

<?php if (Yii::app()->user->isGuest) { ?>
<p>请使用Camera360云相册帐号 <a href="/site/login">登录</a> 。</p>
<?php } else { ?>
	<p>截止到目前，从你的 <?php echo $imagesCount ?> 张相片中检测出了 <?php echo $facesCount ?>  张 <a href="/site/faces">人脸</a> ，
	识别出了 <?php echo $personsCount ?> 个 <a href="/site/persons">人物 </a>。</p>
<?php } ?>

