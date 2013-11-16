<?php

class ModelDataYammerToken extends ModelDataMongoCollection
{
	public function __construct() 
	{
		parent::__construct('dbTools', 'innertools', "yammerToken");
	}
}