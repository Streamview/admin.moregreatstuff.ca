<?php
namespace Models;

use \Models;
use \Helpers;
use \Exceptions;

abstract class Model {
	protected static $qry;
	protected static $find_by_id_qry;
	protected static $find_by_criteria_qry;
	protected static $mysqli_data_types = array (
	    1 => 'tinyint',
	    2 => 'smallint',
	    3 => 'int',
	    4 => 'float',
	    5 => 'double',
	    7 => 'timestamp',
	    8 => 'bigint',
	    9 => 'mediumint',
	    10 => 'date',
	    11 => 'time',
	    12 => 'datetime',
	    13 => 'year',
	    16 => 'bit',
	    //252 is currently mapped to all text and blob types (MySQL 5.0.51a)
		252 => 'tinyblob|blob|mediumblob|longblob|tinytext|text|mediumtext|longtext',
		253 => 'varchar',
	    254 => 'char',
		255 => 'geometry',
	    246 => 'decimal'
	);
	protected static $parameter_marker = ":?";
	
	protected static $class;
	protected $insert_fields = "";
	protected $update_fields = "";
	protected $sql_placeholders = "";
	protected $insert_sql_placeholders = "";
	protected $insert_sql_datatypes_placeholders = "";
	protected $update_sql_datatypes_placeholders = "";
	protected $allowed_attributes = array();
	protected $additional_allowed_attributes = array('subid', 'label', 'value', 'type');
	protected $on_duplicate_key_update = false;
	protected $cache_me = true;
	
	
	
	/**
	 *
	 * @param string $class
	 */
	public function __construct($class = "") {
		self::$class = $class;
		$this->init();
	}
	
	private function init () {
		$this->setTablename()
		->setAttributesInfo()
		->setInsertFields()
		->setUpdateFields()
		->setSQLPlaceholders();
		return $this;
	}
	
	protected function postInit () {}
	
	
	/**
	 * @return Ambigous <NULL, Models_User>
	 */
	protected static function getObjectFromQry () {
		if (empty(self::$qry)) return NULL;
		
		$dbh = \Helpers\Db::getInstance();
		$object = NULL;
		
		self::formatQry();
		$result = $dbh->query(self::$qry);
		
		if (!empty($result) && $result->num_rows) {
			$record = $result->fetch_assoc();
			
			if (empty($record) || empty($record['id'])) return false;
			
			if ($dbh->field_count == 1) {
				//This is the ind by criteria call that only selects the 
				$class = self::$class;
				if (empty($record['id'])) return NULL;
				$object = $class::findById($record['id']);
				
				if (!empty($object)) {
					//$object->postInit();
				}
			} else if ($dbh->field_count > 1) {
				
				self::$find_by_id_qry = self::$qry;
				if (empty($object)) {
					$object 		= new self::$class(); 
					
					$object->addAdditionalAllowedAttributes($result->fetch_fields());
					$object->applyAttributes($record);
					$object->postInit();
				}
			}
		}  else if (!empty($dbh->error)) {
			$dbh->error(self::$qry);
		}
		return $object;
	}
	
	protected static function getObjectsFromQry () {
		if (empty(self::$qry)) return NULL;
		
		$dbh = \Helpers\Db::getInstance();
		$cacher = \Helpers\Cacher::getInstance();
		$objects = array();
		
		self::formatQry();
		$result = $dbh->query(self::$qry);
		
		if (!empty($result) && $result->num_rows) {
			while ($record = $result->fetch_assoc()) {
				$objects[] = $record; 
			}
		}  else if (!empty($dbh->error)) {
			$dbh->error(self::$qry);
		}
		return $objects;
	}
	
	private static function formatQry () {
		self::$qry = preg_replace("/\n{1,}/i", "", self::$qry);
		self::$qry = preg_replace("/(FROM)/", "\nFROM", self::$qry);
		self::$qry = preg_replace("/ (AND)/i", "\nAND", self::$qry);
		self::$qry = preg_replace("/(SELECT)/i", "\nSELECT", self::$qry);
		self::$qry = preg_replace("/(LEFT JOIN){1,}/i", "\nLEFT JOIN", self::$qry);
		self::$qry = preg_replace("/(WHERE){1,}/i", "\nWHERE", self::$qry);
	}
	
	private static function clear () {
		self::$qry = "";
		self::$class = "";
	}
	
	/**
	 * 
	 * @param array $finfo
	 */
	public function addAdditionalAllowedAttributes (array $finfo) {
		foreach ($finfo as $field) {
			if (!array_key_exists($field->name, $this->allowed_attributes)) {
				$this->additional_allowed_attributes[] = $field->name;
			}
		}
	}
	
	/**
	 * 
	 * @param array $attributes
	 */
	public function applyAttributes (array $attributes = array()) {
		foreach ($attributes as $attribute => $value) {
			if (array_key_exists($attribute, $this->allowed_attributes) ||
				in_array($attribute, $this->additional_allowed_attributes) ||
				empty($this->allowed_attributes)) {
				if ($value === 'on' || $value === true) {
					$this->{$attribute} = 1;
				} else if ($value === false || $value === 0) {
					$this->{$attribute} = 0;
				} else {
					$this->{$attribute} = $value;
				}
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @param array $fields
	 * @return stdClass $item
	 */
	public function getJSON (array $fields = array()) {
		return json_encode($this->get($fields));
	}
	
	/**
	 * 
	 * @param array $fields
	 * @return \stdClass
	 */
	public function get ($fields) {
		$object = $this;
		$item = new \stdClass;
		
		foreach ($fields as $field) {
			if (is_array($field)) {
				$real_field = $field[0];
				$object->{$real_field} = NULL;
				$item->{$real_field} = '';
		
				$field = array_slice($field, 1, 1);
		
				$field = array_shift($field);
				$method = array_shift($field);
		
				$object->{$method}();
		
				if (!empty($object->{$real_field})) {
					if (!empty($field)) {
						$item->{$real_field} = new \StdClass;
						foreach ($field as $object_field) {
							$item->{$real_field}->{$object_field} = $object->{$real_field}->{$object_field};
						}
					} else {
						$item->{$real_field} = $object->{$real_field};
					}
				}
			} else {
				if (!empty($object)) {
					$item->{$field} = $object->{$field};
				}
			}
		}
		return $item;
	}
	
	public function translatePlaceholders2Params () {
		$dbh = Helpers\Db::getInstance();
		
		foreach ($this->getInsertUpdateParams() as $key => $value) {
			//echo "$key => $value<br/>";
		
			$parameter_marker = preg_quote(self::$parameter_marker);
		
			if (self::isMySQLiIntegerType($this->allowed_attributes[$key]->type)) {
				$this->stmt = preg_replace("/{$parameter_marker}/", empty($value) ? 0 : $value, $this->stmt, 1, $count);
			} else if (self::isMySQLiFloatType($this->allowed_attributes[$key]->type)) {
				$this->stmt = preg_replace("/{$parameter_marker}/", empty($value) ? 0.0 : $value, $this->stmt, 1, $count);
			} else if (self::isMySQLiStringType($this->allowed_attributes[$key]->type)) {
				if ($value === 'NULL') {
					$this->stmt = preg_replace("/{$parameter_marker}/", "NULL", $this->stmt, 1);
				} else {
					$this->stmt = preg_replace("/{$parameter_marker}/", "'". $dbh->escape_string(empty($value) ? '' : $value) . "'", $this->stmt, 1);
				}
			} else if (self::isMySQLiGeometryType($this->allowed_attributes[$key]->type)) {
				if (preg_match("/(.*),(.*)/", $value)) {
					$values = split(",", $value);
					$value = "GeomFromText('POINT({$values[0]} {$values[1]})')";
				} else if (!empty($this->lat) && !empty($this->lng)) {
					$value = "GeomFromText('POINT({$this->lat} {$this->lng})')";
				} else {
					$value = "GeomFromText('POINT(0 0)')";
				}
				$this->stmt = preg_replace("/{$parameter_marker}/",$value, $this->stmt, 1);
			} else if (self::isMySQLiBitType($this->allowed_attributes[$key]->type)) {
				$this->stmt = preg_replace("/{$parameter_marker}/", empty($value) ? "b'0'" : "b'$value'", $this->stmt, 1);
			} else {
				die("Could not identify MySQLi type " . $this->allowed_attributes[$key]->type);
			}
		}
	}
	
	/**
	 * 
	 * @param callable $callback Function that gets called
	 * upson successful save
	 */
	public function save (callable $callback = NULL) {
		if (empty($this->id)) {
			$dbh = Helpers\Db::getInstance();
			$this->id = Helpers\UUID::id();
				
			$this->stmt = "INSERT INTO `{$this->tablename}` ({$this->insert_fields})
				VALUES ({$this->insert_sql_placeholders})";
			if ($this->on_duplicate_key_update) {
				$this->stmt .= " ON DUPLICATE KEY UPDATE id = id";
			}
			
			$this->translatePlaceholders2Params();
			
			
			if($mysqli_stmt = $dbh->prepare($this->stmt)) {
				$mysqli_stmt->execute();
				if (empty($mysqli_stmt->error) && empty($dbh->error)) {
					if (!empty($callback)) {
						$callback($this);
					}
					return true;
				} else {
					$dbh->error($this->stmt, $mysqli_stmt->error);
				}
			} else {
				$dbh->error($this->stmt, $mysqli_stmt->error);
			}
		} else {
			return $this->update();
		}
	}
	
	/**
	 * 
	 * @param string $callback
	 * @return boolean
	 */
	protected function update ($callback = NULL) {
		$dbh = Helpers\Db::getInstance();
		$this->stmt = "UPDATE `{$this->tablename}`
						SET {$this->update_fields}
						WHERE id = '{$this->id}'";
		
		$this->translatePlaceholders2Params();
		
		if($mysqli_stmt = $dbh->prepare($this->stmt)) {
			$mysqli_stmt->execute();
			
			if (empty($mysqli_stmt->error)) {
				if (!empty($callback)) {
					$callback($this);
				}
				return true;
			} else {
				$dbh->error($this->stmt, $mysqli_stmt->error);
			}
		} else {
			$dbh->error($this->stmt, $mysqli_stmt->error);
		}
	}
	
	
	/**
	 * 
	 * @return boolean
	 */
	protected function hardDeleted () {
		$dbh = Helpers\Db::getInstance();
		$statement = "DELETE FROM ? WHERE id = '{$this->id}'";
		
		if($mysqli_stmt = $dbh->prepare($statement)) {
			$mysqli_stmt->bind_param('ss', $this->tablename, $this->id);
			$mysqli_stmt->execute();
			if (empty($stmt->error)) {
				return true;
			} else {
				$dbh->error($statement);
			}
		} else {
			$dbh->error($statement);
		}
		return $this;
	}
	
	private function setTablename () {
		if (!empty(self::$class)) {
			$this->tablename = "";
			//Determine the db tablename from the class name
			$classname = str_replace("Models\\", "", self::$class);
			$tablename_parts = preg_split("/([A-Z]{1})/", $classname, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
				
			foreach ($tablename_parts as $key => $tablename_part) {
				if ($key != 0 && is_int( $key / 2)) {
					$this->tablename .= "_";
				}
				$this->tablename .= $tablename_part;
			}
			$this->tablename = strtolower($this->tablename);
		}	
		return $this;
	}
	
	private function setAttributesInfo () {
		if (!empty($this->tablename) && empty($this->allowed_attributes)) { 
			$dbh = Helpers\Db::getInstance();
			$qry = "SELECT * FROM `{$this->tablename}`";
			if($mysqli_result = $dbh->query($qry)) {
				$field_info = $mysqli_result->fetch_fields();
				foreach ($field_info as $info) {
					$this->allowed_attributes[$info->name] = $info;
				}
			} else {
				$dbh->error($qry);
			}
		}
		return $this;
	}
	
	private function setInsertFields () {
		if (empty($this->insert_fields)) {
			foreach ($this->allowed_attributes as $attribute => $attribute_info) {
				if (!empty($this->insert_fields)) {
					$this->insert_fields .= ", "; 
					$this->insert_sql_placeholders .= ", ";
				}
				
				$this->insert_fields .= "`{$attribute}`";
				if ($attribute == 'create_date') {
					$this->insert_sql_placeholders .= "'". Helpers\DateTime::getCurrentTimestamp() ."'";
				} else {
					$this->insert_sql_placeholders .= self::$parameter_marker;
				}
				
				if ($attribute == 'create_date') {
					continue;
				} else if (self::isMySQLiIntegerType($attribute_info->type)) {
					$this->insert_sql_datatypes_placeholders .= 'i';
				} else if (self::isMySQLiFloatType($attribute_info->type)) {
					$this->insert_sql_datatypes_placeholders .= 'd';
				} else if (self::isMySQLiStringType($attribute_info->type)) {
					$this->insert_sql_datatypes_placeholders .= 's';
				} else if (self::isMySQLiGeometryType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 's';
				} else if (self::isMySQLiBitType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 's';
				} else {
					throw new \Exception("Could not match datatype {$attribute_info->type}");
				}
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \Models\Model
	 */
	private function setUpdateFields () { 
		if (empty($this->update_fields)) {
			foreach ($this->allowed_attributes as $attribute => $attribute_info) {
				if ($attribute == 'create_date') continue;
				
				if (!empty($this->update_fields)) $this->update_fields .= ", ";
				$this->update_fields .= "`{$attribute}` = " . self::$parameter_marker;
			
				if (self::isMySQLiIntegerType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 'i';
				} else if (self::isMySQLiFloatType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 'd';
				} else if (self::isMySQLiStringType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 's';
				} else if (self::isMySQLiGeometryType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 's';
				} else if (self::isMySQLiBitType($attribute_info->type)) {
					$this->update_sql_datatypes_placeholders .= 's';
				} else {
					throw new \Exception("Could not match datatype {$attribute_info->type}");
				}
			}
		}
		return $this;
	}
	
	private function setSQLPlaceholders () {
		
	}
	
	protected function getInsertUpdateParams () {
		$this->insert_update_params = array();
		foreach (array_keys($this->allowed_attributes) as $attribute) {
			if($attribute != 'create_date') {
				if (empty($this->{$attribute}) && self::isMySQLiStringType($this->allowed_attributes[$attribute]->type)) {
					$this->insert_update_params[$attribute] = 'NULL';
				} else {
					$this->insert_update_params[$attribute] = $this->{$attribute};
				}
			}
		}
		return $this->insert_update_params;
	}
	
	/**
	 * 
	 * @param integer $type
	 * @return boolean
	 */
	protected static function isMySQLiIntegerType ($type) {
		if (in_array($type, array(1, 2, 3, 8, 9))) {
			return true;
		}
	}
	
	/**
	 * 
	 * @param integer $type
	 * @return boolean
	 */
	protected static function isMySQLiFloatType ($type) {
		if (in_array($type, array(4, 5, 246))) {
			return true;
		}
	}
	
	/**
	 * 
	 * @param int $type
	 * @return boolean
	 */
	protected static function isMySQLiStringType ($type) {
		if (in_array($type, array(7, 10, 11, 12, 13, 252, 253, 254))) {
			return true;
		}
	}
	
	/**
	 *
	 * @param int $type
	 * @return boolean
	 */
	protected static function isMySQLiGeometryType ($type) {
		if (in_array($type, array(255))) {
			return true;
		}
	}
	
	/**
	 * 
	 * @param unknown $type
	 * @return boolean
	 */
	protected static function isMySQLiBitType ($type) {
		if (in_array($type, array(16))) {
			return true;
		}
	}
}