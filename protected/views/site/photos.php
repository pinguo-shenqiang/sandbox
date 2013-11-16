<?php
/* @var $this SiteController */
/* @var $modelForm LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - 相片列表';
$this->breadcrumbs = array(
	'相片列表',
);
?>
<style>
	ul li {
		list-style: none;
	}
	.photos {
		float: left;
		margin: 0;
		padding: 0;
	}
	.photos li {
		float: left;
		margin: 13px;
	}
	.photos li img {
		width: 200px;
	}
	p.sort-Order {
		margin: 0;
	}
</style>

<p class="sort-Order">
	<?php if ($sort == 1) { ?>
	<span>正序</span>
	<a href="<?php echo $this->createUrl('site/photos', array('sort' => -1)) ?>">倒序</a>
	<?php } else { ?>
	<a href="<?php echo $this->createUrl('site/photos', array('sort' => 1)) ?>">正序</a>
	<span>倒序</span>
	<?php } ?>
</p>

<ul class="photos">
	<?php foreach ($photos as $photo) { ?>
	<li photoId="<?php echo $photo['_id'] ?>">
		<a href="/site/image?id=<?php echo $photo['_id'] ?>"> 
			<img alt=""	src="<?php echo PhotoHelper::url($photo['remoteId'], 1, 200, 200) ?>"/>
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
