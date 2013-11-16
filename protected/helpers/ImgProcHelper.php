<?php

class ImgProcHelper
{
    public static $idcNoToName = array(
        1 => 'ningbo',
        2 => 'beijing',
        3 => 'zhangmutou'
    );
    
    /**
     * 查询一批图片所在存储节点
     * @param array $imageIds
     * @return array
     * array(
        idc1 => array(imageId1, imageId2, ...),
        ...
     )
     */
	public static function getIdcOfImages($imageIds)
	{
		$modelDataPicture = new ModelDataCloudPicture();
		$mongoIds = array_map(function ($imageId) {
			return new MongoId($imageId);
		}, $imageIds);
		$images = $modelDataPicture->query(array('_id' => array('$in' => $mongoIds)),
			array('remoteId'));
		$bucket = 'cdn.360in.com';
		$keys = array_values(array_map(function ($image) {
			return $image['remoteId'];
		}, $images));
		$idcs = QboxHelper::idcInfoMulti($bucket, $keys);
		$idcToImages = array();
		foreach ($imageIds as $imageId) {
			if (!isset($images[$imageId])) {
				Yii::log("image not found: imageId-$imageId", CLogger::LEVEL_ERROR);
				continue;
			}
			$image = $images[$imageId];
			if (!isset($idcs[$image['remoteId']])) {
				Yii::log("idc not found: imageId-{$image['remoteId']}", CLogger::LEVEL_ERROR);
				continue;
			}
            $idc = self::$idcNoToName[$idcs[$image['remoteId']]];
            if (isset($idcToImages[$idc])) {
                $idcToImages[$idc][] = $imageId;
            } else {
                $idcToImages[$idc] = array($imageId);
            }
		}
        return $idcToImages;
	}
	
	public static function logProfile($label, $profile)
	{
		$durations = array();
		foreach ($profile as $item) {
			if ($item['name'] == 'tt') {
				continue;
			}
			$durations[$item['name']] = $item['duration'];
		}
		Yii::log("$label: total-".array_sum($durations)." detail-".json_encode($durations), 
			CLogger::LEVEL_PROFILE);
	}
	
	public static function logProfiles($label, $profiles)
	{
		$durations = array();
		$counts = array();
		foreach ($profiles as $profile) {
			foreach ($profile as $item) {
				if (!isset($durations[$item['name']])) {
					$durations[$item['name']] = $item['duration'];
					$counts[$item['name']] = 1;
				} else {
					$durations[$item['name']] += $item['duration'];
					$counts[$item['name']]++;
				}
			}
		}
		array_walk($durations, function (&$value, $key) use ($counts) {
			$value = $value / $counts[$key];
		});
		Yii::log("$label: total-".array_sum($durations)." detail-".json_encode($durations), 
			CLogger::LEVEL_PROFILE);
	}
}
