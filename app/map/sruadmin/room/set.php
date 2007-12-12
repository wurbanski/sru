<?php

/**
 * modyfikacja pokoju
 */

class UFmap_SruAdmin_Room_Set
extends UFmap {
	protected $columns = array(
		'id'			=> 'id',
		'alias'			=> 'alias',
		'comment'		=> 'comment',	
		'userCount'		=> 'users_count',
		'computerCount' => 'computers_count',
		'dormitoryId'	=> 'dormitory_id',		
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'alias'          => self::TEXT,
		'comment'         => self::TEXT,
		'userCount'     => self::INT,
		'computerCount' => self::INT,
		'dormitoryId'    => self::INT,	
	);
	protected $tables = array(
		'' => 'locations',
	);
	protected $pk = 'id';
}