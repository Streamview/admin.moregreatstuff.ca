<?php
/**
 *	@final class  Models\ProductCategory
 *	@extend Models\Model
 *	@author Ramone Burrell <burrellramone@gmail.com>
 *	@link http://burrellramone.com
 */
namespace Models;

use \Models;
use \Helpers;

final class ProductCategory extends Models\Model {
	public function __construct() {
		parent::__construct(get_class());
	}
	
	public function postInit () {
		parent::postInit();
	}
	
	public static function findById($id) {
		if (empty($id)) return NULL;
		self::$class = get_called_class();
		
		self::$qry = "SELECT product_category.*
					FROM product_category
					WHERE product_category.id = '$id'";
		return self::getObjectFromQry();
	}
	
	/**
	 * 
	 * @param array $criteria
	 */
	public static function findByCriteria (array $criteria = array()) {
		if(empty($criteria)) return;
		self::$class = get_called_class();
		
		$where = "";
		$left_join = "";
		$order_by = "";
		$limit = "LIMIT 1";

		
		if(!empty($criteria['label'])) {
			$where .= " and product_category.label = '{$criteria['label']}'";
		}
		
		$where = preg_replace("/^ (and)/", "where", $where);
		
		self::$qry = "SELECT product_category.id
				FROM product_category
				{$left_join}
				{$where}
				{$order_by}
				{$limit}";
		
		return self::getObjectFromQry();
	}
}