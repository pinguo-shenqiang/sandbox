<?php
/* @var $this SiteController */
/* @var $modelForm LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - 人脸列表';
$this->breadcrumbs = array(
	'人脸列表',
);
?>
<style>
	.faceList {
	    float: left;
	    margin: 0;
	    padding: 0;
	}
	.faceList li {
	    float: left;
	    list-style: none outside none;
	    margin: 6px;
	}	
	.faceList img{
		width: 100px;
		height: 100px;
	}		
</style>

<ul class="faceList">
	<?php foreach ($faces as $face) { ?>
	<li>
		<a href="/site/image?id=<?php echo $face['image'] ?>"> 
			<img alt=""	src="<?php echo FaceHelper::faceUrl($face['fileId']) ?>"/>
		</a>
	</li>
	<?php } ?>
</ul>

<div style="clear:both;"></div>

<div class="pageContainer">
	<?php $this->widget('CLinkPager', array(
		'pages' => $pages
	) )?>
</div>

<div class="clear"></div>
