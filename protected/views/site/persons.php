<?php
/* @var $this SiteController */
/* @var $modelForm LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - 人物列表';
$this->breadcrumbs = array(
	'人物列表',
);
?>
<style>
	.personList .first {
		margin: 10px 0;
	}
	.personList .first img{
		width:100px;
		height:100px;
	}
	.personList .list {
	    float: left;
	    margin: 0;
	    padding: 0;
	}	
	.personList .list li {
	    float: left;
	    list-style: none outside none;
	    margin-right: 5px;
	}	
	.personList .list img{
		width:50px;
		height:50px;
	}
	hr {
		height: 2px;
	}
</style>

<div class="personList">
	<?php foreach ($persons as $person) { ?>
	<div class="first">
		<a href="/site/image?id=<?php echo $person['cover']['image'] ?>" title="该人物下共有 <?php echo $person['facesCount'] ?> 张人脸"> 
			<img alt=""	src="<?php echo FaceHelper::faceUrl($person['cover']['fileId']) ?>"/>
		</a>
	</div>
	<ul class="list">
		<?php foreach ($person['faces'] as $face) { ?>
		<li>
			<a href="/site/image?id=<?php echo $face['image'] ?>"> 
				<img alt=""	src="<?php echo FaceHelper::faceUrl($face['fileId']) ?>"/>
			</a>
		</li>
		<?php } ?>
	</ul>
	<hr />
	<div style="clear:both;"></div>
	<?php } ?>
</div>

<div style="clear:both;"></div>
		
<div class="pageContainer">
	<?php $this->widget('CLinkPager', array(
		'pages' => $pages
	) )?>
</div>	

<div class="clear"></div>

