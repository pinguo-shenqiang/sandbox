<?php

class ModelDataYammerUser extends ModelDataMongoCollection
{
	public function __construct() 
	{
		parent::__construct('dbTools', 'innertools', "yammerUser");
	}
}