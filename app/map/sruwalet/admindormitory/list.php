<?
/**
 * wyciagniecie listy przypisan admina Waleta do DSu
 */
class UFmap_SruWalet_AdminDormitory_List
extends UFmap {

	protected $columns = array(
		'id'             => 'a.id',
		'admin'          => 'a.admin',
		'adminName'	 => 'm.name',
		'adminType'	 => 'm.type_id',
		'dormitory'      => 'a.dormitory',
		'dormitoryName'  => 'd.name',
		'dormitoryAlias' => 'd.alias',
		'dormitoryId'	 => 'd.id',
		'display_order'  => 'd.display_order'
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'admin'          => self::INT,
		'adminName'      => self::TEXT,
		'adminType'	 => self::INT,
		'dormitory'      => self::INT,
		'dormitoryName'  => self::TEXT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryId'	 => self::INT,
		'display_order'  => self::INT
	);
	protected $tables = array(
		'a' => 'admins_dormitories',
	);
	protected $joins = array(
		'd' => 'dormitories',
		'm' => 'admins',
	);
	protected $joinOns = array(
		'd' => 'a.dormitory = d.id',
		'm' => 'a.admin = m.id',
	);
	protected $pk = 'a.id';
}
