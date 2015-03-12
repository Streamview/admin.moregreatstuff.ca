<?php
/**
 *	@final class  Models\ModelList
 *	@author Ramone Burrell <burrellramone@gmail.com>
 *	@link http://burrellramone.com
 */
namespace Models;
use \Helpers;

class ModelList {
	protected $qry;
	protected $class;
	protected $unit_class;
	public $objects;
	public $select_options;
	public $criteria = array();
		
	public function __construct ($class) {
		$this->objects 		= array();
		$this->class 		= $class;
		$this->unit_class 	= str_replace("List", "", $this->class);
	}
	
	public function findAll () {return $this->findByCriteria(array("1"));}
	public function findByCriteria (array $criteria = array()) {}
	
	protected function getObjectsFromQry () {
		if (empty($this->unit_class)) return false;
		$dbh = Helpers\Db::getInstance();
		$this->formatQry();
		
		$result = $dbh->query($this->qry);
		
		if (!empty($result) && empty($dbh->error)) {
			$this->objects = array();
			while($record = $result->fetch_assoc()) {
				$unit_class = $this->unit_class;				
				$this->objects[] = $unit_class::findById($record['id']);
			}		
		} else if (!empty($dbh->error)) {
			$dbh->error($this->qry);
		}
	}
	
	private function formatQry () {
		$this->qry = preg_replace("/(LEFT JOIN)/i", "\nLEFT JOIN", $this->qry);
	}
	
	public function getIdsFromObjects () {
		$this->ids = array();
		foreach ($this->objects as $object) {
			$this->ids[] = $object->id;
		}
		return $this->ids;
	}
	
	/**
	 * @return integer $total
	 */
	public function getTotal () {
		return count($this->objects);
	}
	
	/**
	 * 
	 * @param string $field
	 * @return boolean|number
	 */
	public function sum ($field) {
		if (empty($field)) return false;
		$sum = 0;
		foreach ($this->objects as $object) {
			$sum += $object->{$field};
		}
		return $sum;
	}
	
	/**
	 * 
	 * @param unknown $field
	 * @param unknown $value
	 * @return boolean
	 */
	public function find ($field, $value) {
		if (empty($field) || empty($value)) return false;
		foreach ($this->objects as $object) {
			if ($object->{$field} == $value) {
				return true;
			}
		}
	}
	
	public function filter ($field, $value) {
		if ((is_array($field) && is_array($value)) 
				&& (!empty($field) && !empty($value))) {
			$this->filter($field[0], $value[0]);
			unset($field[0]);
			unset($value[0]);
			$field = array_filter($field);
			$value = array_filter($value);
		} else if (!is_array($field) && is_array($value)) {
			foreach ($value as $val) {
				$this->filter($field, $val);
			}
		} else {
			foreach ($this->objects as $key => $object) {
				if ($object->{$field} == $value) {
					unset($this->objects[$key]);
				}
			}
			$this->objects = array_filter($this->objects);
		}
	}
	
	public function getJSON (array $fields, $for_datatable = TRUE) {
		$items = $this->get($fields);
		
		if ($for_datatable) {
			return json_encode(array(
					"data" => $items,
					"draw" => NULL,
					"recordsTotal" => count($items),
					"recordsFiltered" => 0,
			), JSON_PRETTY_PRINT);
		} else {
			if (!empty($_REQUEST['callback'])) {
				return $_REQUEST['callback'] . "(". json_encode($items, JSON_PRETTY_PRINT).");";
			} else {
				return json_encode($items, JSON_PRETTY_PRINT);
			}
		}
	}
	
	/**
	 * 
	 * @param array $fields
	 * @return string
	 */
	public function get ($fields) {
		$items = array();
		
		if (!empty($fields)) {
			foreach ($this->objects as $object) {
				if (empty($object))  continue;
		
				$item = new \stdClass;
		
				foreach ($fields as $field) {
					if (is_array($field)) {
						$real_field = $field[0];
						$object->{$real_field} = NULL;
						$item->{$real_field} = '';
		
						$field = array_slice($field, 1, 1);
		
						$field = array_shift($field);
						$method = array_shift($field);
		
						$return_val = $object->{$method}();
		
						if (!empty($object->{$real_field})) {
							if (!empty($field)) {
								$item->{$real_field} = new \StdClass;
								foreach ($field as $object_field) {
									$item->{$real_field}->{$object_field} = $object->{$real_field}->{$object_field};
								}
							} else {
								$item->{$real_field} = $object->{$real_field};
							}
						} else {
							$item->{$real_field} = $return_val;
						}
					} else {
						if (!empty($object)) {
							$item->{$field} = $object->{$field};
						}
					}
				}
				$items[] = $item;
			}
		}
		return $items;
	}
	
	public function uncache () {
		$cacher = Helpers\Cacher::getInstance();
		return $cacher->delete(self::getMCKey());
	}
	
	/**
	 * 
	 * @throws Exception
	 */
	private function getMCKey () {
		if (empty($this->class) || empty($this->qry)) {
			throw new Exception("Unable to determin MC key. Qry and/or class is empty Class: {$this->class}, Qry: {$this->qry}");
		}
		return md5($this->class . " " . $this->qry);
	}
	
	/**
	 * 
	 * @param string $field
	 * @return string $fields
	 */
	public function stringifyField ($field) {
		return implode(",", $this->getFieldValues($field));		
	}
	
	/**
	 * 
	 * @param string $field
	 * @return array:NULL
	 */
	public function getFieldValues ($field) {
		$values = array();
		foreach ($this->objects as $object) {
			$values[] = $object->{$field};
		}
		return $values;
	}
	
	public function each (callable $function) {
		foreach ($this->objects as $stop) {
			$function($stop);
		}
	}
	
	
	public function save () {
		foreach ($this->objects as $object) {
			$object->save();
		}
		return true;
	}
	
	/**
	 * Does a hard delete one ach object in this list. Be careful
	 * with this method
	 */
	public function delete () {
		foreach ($this->objects as $object) {
			$object->delete(true);
		}
	}
	
	public function getSelectOptions () {
		if (empty($this->select_options)) {
			$this->select_options = array();
			foreach ($this->objects as $object) {
				$value = $object->label;
				if (empty($value)) $value = $object->title;
				if (empty($value)) $value = $object->name;
				
				$this->select_options[$object->id] =  $value;
			} 
		}
		return $this->select_options;
	}
}