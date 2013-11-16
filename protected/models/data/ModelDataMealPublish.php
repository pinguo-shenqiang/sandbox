<?php

class ModelDataMealPublish extends ModelDataMongoCollection
{
	public function __construct() 
	{
		parent::__construct('dbTools', 'innertools', "mealPublish");
	}
}