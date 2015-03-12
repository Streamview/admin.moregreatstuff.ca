<?php
/**
 *	@final class  Models\Product
 *	@extend Models\Model
 *	@author Ramone Burrell <burrellramone@gmail.com>
 *	@link http://burrellramone.com
 */
namespace Models;

use \Models;
use \Helpers;
use \Config;
use \Exception;

final class Product extends Models\Model {
	public $status = 'Active';
	
	/**
	 * 
	 * @param string $name
	 * @param string $path
	 */
	public function __construct() {
		parent::__construct(get_class());
	}
	
	public static function findById($id) {
		if (empty($id)) return NULL;
		self::$class = get_called_class();
	
		self::$qry = "SELECT product.*,
			SUBSTRING(id, 1, 4) as subid
			FROM product
			WHERE product.id = '$id'";
		return self::getObjectFromQry();
	}
	
	public function getCategory () {
		if (empty($this->category)) {
			$this->category = Models\ProductCategory::findById(!empty($this->category_id) ? $this->category_id : NULL);
		}
		return $this->category;
	}
	
	public function getImg () {
		if (empty($this->img)) {
			$this->img = Models\File::findById(!empty($this->img_id) ? $this->img_id : NULL);
		}
		return $this->img;
	}
}