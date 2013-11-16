<?php
/* @var $this SiteController */
/* @var $modelForm LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - 相片';
if (isset(Yii::app()->session['breadcrumbs'])) {
	$this->breadcrumbs += Yii::app()->session['breadcrumbs'];
}
$this->breadcrumbs += array('相片');
?>

<style>
ul.faceList, ul.imageList {
	margin: 0;
	padding: 0;
	overflow: hidden;
}
.faceList li {
	list-style: none;
	float: left;
	padding:4px;
}	
.faceList img {
	width:50px;
	height:50px;
	cursor:pointer;
	border:2px solid #ccc;
}
.imageList li {
	list-style: none;
	float: left;
	padding:4px;
}	
.imageList img {
	width:100px;
	height:100px;
	cursor:pointer;
	border:2px solid #ccc;
}
</style>
<script>
var image = <?php echo json_encode($image) ?>;
var faces = <?php echo json_encode($faces) ?>;

function getFaceInfo(){
	return faces;
}
//返回image真实高度，用于设置缩放比例
function getImgOriginalWidth(){

	return image.width;
}

//设置swf的size
function setSwfSize(width,height){
	$(document).ready(function(){		
		$("#faceSwf").css({"height":height,"width":width});	
	});
}

$(document).ready(function() {
	$(".faceList img").click(function (){
		document.getElementById("faceSwf").drawFeaturePoints(faces,$(this).attr("id"));
	});
});
</script>
<div>

	<object>
		<embed id="faceSwf" src="/swf/FaceImage.swf?v=1.1" wmode="transparent" flashvars="imgUrl=<?php echo PhotoHelper::url($image['remoteId'], 2, 900) ?>" allowScriptAccess="sameDomain" >
		</embed>
	</object>
	
	<p><b>检测到的人脸：</b></p>
	<ul class = "faceList">
		<?php foreach ($faces as $face) { ?>
		<li>		
			<img alt="" id="<?php echo ($face['fileId']) ?>" src="<?php echo FaceHelper::faceUrl($face['fileId']) ?>" />			
		</li>
		<?php } ?>
	</ul>
	
	<p><b>相同图片（V<?php echo Yii::app()->params['signatureIdentityVersion'] ?>）：</b></p>
	<ul class = "imageList">
		<?php foreach ($sameImages as $sameImage) { ?>
		<li>
			<a href="/site/image?id=<?php echo $sameImage['_id'] ?>"> 
				<img alt="" id="<?php echo ($sameImage['remoteId']) ?>" src="<?php echo PhotoHelper::url($sameImage['remoteId'], 1, 100, 100) ?>" />
			</a>
			[<?php echo implode(' ', SignatureHelper::identityToHex($signatureIdentitys[(string) $sameImage['_id']]['identity']))?>]			
		</li>
		<?php } ?>
	</ul>
	
	<p>
		<b>相同特征码（V<?php echo Yii::app()->params['signatureIdentityVersion'] ?>）：</b><br />
		<?php if ($signatureIdentity) { ?>
			[<?php echo implode(' ', SignatureHelper::identityToHex($signatureIdentity['identity']))?>]
		<?php } ?>
	</p>
	
	<p><b>相似图片（V<?php echo Yii::app()->params['signatureSimilarityVersion'] ?>）：</b></p>
	<ul class = "imageList">
		<?php foreach ($similarImages as $similarImage) { ?>
		<li>
			<a href="/site/image?id=<?php echo $similarImage['_id'] ?>"> 
				<img alt="" id="<?php echo ($similarImage['remoteId']) ?>" src="<?php echo PhotoHelper::url($similarImage['remoteId'], 1, 100, 100) ?>" />
			</a>			
		</li>
		<?php } ?>
	</ul>
	
	<p>
		<b>相似特征码（V<?php echo Yii::app()->params['signatureSimilarityVersion'] ?>）：</b><br />
		<?php if ($signatureSimilarity) { ?>
			颜色：<br />
			[<?php echo implode(' ', $signatureSimilarity['color1'])?>]<br />
			[<?php echo implode(' ', $signatureSimilarity['color2'])?>]<br />
			[<?php echo implode(' ', $signatureSimilarity['color3'])?>]<br />
			[<?php echo implode(' ', $signatureSimilarity['color4'])?>]<br />
			纹理：<br />
			[<?php echo implode(' ', $signatureSimilarity['texture'])?>]
		<?php } ?>
	</p>
</div>
