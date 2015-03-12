<?php
/**
 *	@final class  Models\ProductList
 *	@extend Models\ModelList
 *	@author Ramone Burrell <burrellramone@gmail.com>
 *	@link http://burrellramone.com
 */
namespace Models;

use \Models;
use \Helpers;

final class ProductList extends Models\ModelList {
	public function __construct (array $criteria = array()) {
		parent::__construct(get_class(), $criteria);
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
		$group_by = "GROUP BY product.id";
		$order_by = array("product.id DESC");
		$limit = "";

		if(!empty($criteria['keywords'])) {
			$keywords = explode(' ', $criteria['keywords']);
			$where .= " \nand (";
				
			foreach ($keywords as $index => $keyword) {
				$where .= " (product.name LIKE '%$keyword%' OR product.description LIKE '%{$keyword}%')";
				if ($index < (count($keywords) - 1)) {
					$where .= " AND ";
				}
			}
			$where .= " )";
		}
		
		if(!empty($this->criteria['status'])) {
			$where .= " \nand product.status = '{$this->criteria['status']}'";
		}

		
		
		
		
		$where = preg_replace("/^ (\nand)/", "WHERE", $where);

		if (!empty($this->criteria['limit'])) {
			if (is_array($this->criteria['limit'])) {
				
			} else {
				$limit = "LIMIT {$this->criteria['limit']}";
			}
		}
		
		$order_by = implode(", ", $order_by);
		
		$this->qry = "SELECT DISTINCT product.id
				{$addional_select_fields}
				FROM product
				{$left_join}
				{$where}
				{$group_by}
				ORDER BY {$order_by}
				{$limit}";
				
				/*print "<pre>";
				print_r($this->criteria);
				echo $this->qry;
				exit;*/
				
		return $this->getObjectsFromQry();
	}
}
