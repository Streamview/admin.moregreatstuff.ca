<?php
/**
 *	@final class  Models\ProductCategoryList
 *	@extend Models\ModelList
 *	@author Ramone Burrell <burrellramone@gmail.com>
 *	@link http://burrellramone.com
 */
namespace Models;

use \Models;
use \Helpers;

final class ProductCategoryList extends Models\ModelList {
	public function __construct (array $options = array()) {
		parent::__construct(get_class(), $options);
	}

	
	/**
	 * 
	 * @param array $criteria
	 */
	public function findByCriteria (array $criteria = array()) {
		if(empty($criteria)) return;
		$dbh = Helpers\Db::getInstance();
		
		$this->criteria = array_merge($this->criteria, $criteria);
		
		$where = "";
		$left_join = "";
		$left_join_arr = array();
		$addional_select_fields = "";
		$group_by = "";
		$order_by = array("product_category.id ASC");
		$limit = "";

		if(!empty($criteria['keywords'])) {
			$keywords = explode(' ', $criteria['keywords']);
			$where .= " \nand (";
				
			foreach ($keywords as $index => $keyword) {
				$where .= " (product_category.label LIKE '%$keyword%')";
				if ($index < (count($keywords) - 1)) {
					$where .= " OR ";
				}
			}
			$where .= " )";
		}
		
		if(!empty($this->criteria['label'])) {
			$where .= " \nand product_category.label = '{$this->criteria['label']}'";
		}
		
		$where = preg_replace("/^ (\nand)/", "WHERE", $where);

		if (!empty($this->criteria['limit'])) {
			if (is_array($this->criteria['limit'])) {
				
			} else {
				$limit = "LIMIT {$this->criteria['limit']}";
			}
		}
		
		$order_by = implode(", ", $order_by);
		
		$this->qry = "SELECT product_category.id
				{$addional_select_fields}
				FROM product_category
				{$left_join}
				{$where}
				{$group_by}
				ORDER BY {$order_by}
				{$limit}";
				
		return $this->getObjectsFromQry();
	}
}
