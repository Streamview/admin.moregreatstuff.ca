<?php
/**
*	@class D
*	@Author Ramone Burrell <burrell@burrellramone.com>
*/
namespace Helpers;

use \Helpers;
use \Exceptions;
use \Config;

class Db extends \mysqli {
	static private $object;
	
	protected function __construct () {		
		parent::__construct(\Config\App::DATABASE_HOST, 
							\Config\App::DATABASE_USER,
						    \Config\App::DATABASE_PASSWORD,
							\Config\App::DATABASE_NAME);
		
		if(!empty($this->error)){	
			die($this->error);
		} else {
			$this->set_charset("utf8");
		}
	} 
	
	static function getInstance(){
		if(!self::$object){
			self::$object = new \Helpers\Db();
		}
		return self::$object;
	}
	
	/**
	 * @param unknown $items
	 * @param string $glue
	 * @return string
	 */
	public function quoteArray($items, $glue = ",") {
		$str = '';
		foreach($items as $key => $item) {
			$str .= $item;
			if ($key < (count($items) - 1)) {
				$str .= $glue;
			}
		}
		return $str;
	}
	
	/**
	 * 
	 * @param string $statement
	 * @param unknown $object
	 * @throws \Exception
	 */
	public function error ($statement = "", $error = NULL) {
		$message = json_encode(array(
			'success' => false,
			'message' => "SQL Excecution Failed",
			'errors' => array($this->error, $error),
			'error_no' => $this->errno,
			'sql' => $statement				
		), JSON_PRETTY_PRINT);
		
		throw new \Exception($message);
	}
}