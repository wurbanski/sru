<?
/**
 * wyciagniecie pojedynczego uzytkownika
 */
class UFmap_SruAdmin_UserHistory_List
extends UFmap {

	protected $columns = array(
		'id'             => 'u.id',
		'userId'         => 'u.user_id',
		'login'          => 'u.login',
		'name'           => 'u.name',
		'surname'        => 'u.surname',
		'email'          => 'u.email',
		'gg'             => 'u.gg',
		'facultyId'      => 'u.faculty_id',
		'facultyName'    => 'f.name',
		'facultyNameEn'  => 'f.name_en',
		'facultyAlias'   => 'f.alias',
		'studyYearId'    => 'u.study_year_id',
		'locationId'     => 'u.location_id',
		'locationAlias'  => 'l.alias',
		'dormitoryId'    => 'l.dormitory_id',
		'dormitoryAlias' => 'd.alias',
		'dormitoryName'  => 'd.name',
		'dormitoryNameEn'=> 'd.name_en',
		'modifiedById'   => 'u.modified_by',
		'modifiedBy'     => 'a.name',
		'modifiedAt'     => 'u.modified_at',
		'comment'        => 'u.comment',
		'active'         => 'u.active',
		'referralStart'	 => 'u.referral_start',
		'referralEnd'	 => 'u.referral_end',
		'registryNo'	 => 'u.registry_no',
		'servicesAvailable'	=> 'u.services_available',
		'updateNeeded'	=> 'u.update_needed',
		'changePasswordNeeded'	=> 'u.change_password_needed',
		'passwordChanged'	=> 'u.password_changed',
		'lang'		 => 'u.lang',
		'typeId'	=> 'u.type_id',
		'address'		=> 'u.address',
		'documentType'	=> 'u.document_type',
		'documentNumber'=> 'u.document_number',
		'nationality'	=> 'u.nationality',
		'nationalityName' => 'c.nationality',
		'pesel'			=> 'u.pesel',
		'birthDate'		=> 'u.birth_date',
		'birthPlace'	=> 'u.birth_place',
		'userPhoneNumber'	=> 'u.user_phone_number',
		'guardianPhoneNumber'	=> 'u.guardian_phone_number',
		'sex'			=> 'u.sex',
		'lastLocationChange' => 'u.last_location_change',
	);
	protected $columnTypes = array(
		'id'             => self::INT,
		'userId'         => self::INT,
		'login'          => self::TEXT,
		'name'           => self::TEXT,
		'surname'        => self::TEXT,
		'email'          => self::TEXT,
		'gg'             => self::TEXT,
		'facultyId'      => self::NULL_INT,
		'facultyName'    => self::TEXT,
		'facultyNameEn'  => self::TEXT,
		'facultyAlias'   => self::TEXT,
		'studyYearId'    => self::NULL_INT,
		'locationId'     => self::INT,
		'locationAlias'  => self::TEXT,
		'dormitoryId'    => self::INT,
		'dormitoryAlias' => self::TEXT,
		'dormitoryName'  => self::TEXT,
		'dormitoryNameEn'=> self::TEXT,
		'modifiedById'   => self::NULL_INT,
		'modifiedBy'     => self::TEXT,
		'modifiedAt'     => self::TS,
		'comment'        => self::TEXT,
		'active'         => self::BOOL,
		'referralStart'	 => self::TS,
		'referralEnd'	 => self::TS,
		'registryNo'	 => self::NULL_INT,
		'servicesAvailable'	=> self::BOOL,
		'updateNeeded'	=> self::BOOL,
		'changePasswordNeeded'	=> self::BOOL,
		'passwordChanged'	=> self::TS,
		'lang'           => self::TEXT,
		'typeId'	=> self::INT,
		'address'		=> self::TEXT,
		'documentType'	=> self::INT,
		'documentNumber'=> self::TEXT,
		'nationality'	=> self::INT,
		'nationalityName' => self::TEXT,
		'pesel'			=> self::TEXT,
		'birthDate'		=> self::NULL_TS,
		'birthPlace'	=> self::TEXT,
		'userPhoneNumber'	=> self::TEXT,
		'guardianPhoneNumber'	=> self::TEXT,
		'sex'			=> self::BOOL,
		'lastLocationChange' => self::TS,
	);
	protected $tables = array(
		'u' => 'users_history',
	);
	protected $joins = array(
		'f' => 'faculties',
		'l' => 'locations',
		'd' => 'dormitories',
		'a' => 'admins',
		'c' => 'countries',
	);
	protected $joinOns = array(
		'f' => 'u.faculty_id=f.id',
		'l' => 'u.location_id=l.id',
		'd' => 'l.dormitory_id=d.id',
		'a' => 'u.modified_by=a.id',
		'c' => 'u.nationality=c.id',
	);
	protected $pk = 'u.id';
}
