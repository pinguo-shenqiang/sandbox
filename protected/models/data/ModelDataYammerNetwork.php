<?php

class ModelDataYammerNetwork extends ModelDataMongoCollection
{
	public function __construct() 
	{
		parent::__construct('dbTools', 'innertools', "yammerNetwork");
	}
}