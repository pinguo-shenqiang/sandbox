<?php

class ImgProcHelperTest extends PHPUnit_Framework_TestCase
{	
	public function setUp()
	{
        
	}
	
	public function testGetIdcOfImages()
	{
        $imageIds = array(
            '51da526d5ae6e1601add1cbc',
            '51da526b59e6e1ce5a51443c',
            '51da1c4958e6e10b24b5e3a0',
            '51da1c3f5be6e19f3f998649',
            '51da1c3459e6e11a177c3118',
        );
        $ret = ImgProcHelper::getIdcOfImages($imageIds);
        print_r($ret);
		$this->assertTrue(!empty($ret));
	}
}