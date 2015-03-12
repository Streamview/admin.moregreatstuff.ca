<?php
ini_set("display_errors", "1");
include '/home/houspcom/public_html/admin.moregreatstuff.ca/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$success = false;
	$message  = "";
	$errors = array();
	$product_id = NULL;
	
	//print_r($_POST);
	//print_r($_FILES);
	//exit;
	
	if (empty($_REQUEST['code'])) {
		$errors[] = "Provide a product code";
	} elseif (empty($_REQUEST['name'])) {
		$rrors[] = "Provide a name for the product";
	} elseif (empty($_REQUEST['quantity'])) {
		$errors[] = "Specify a quantity";
	} elseif (empty($_REQUEST['price'])) {
		$errors[] = "Provide a price for the product";
	} elseif (empty($_REQUEST['description'])) {
		$errors[] = "Provide a description for the product";
	} else {
		$dbh = Helpers\Db::getInstance();
		//$dbh->begin_transaction();
			
		$product = getProduct();
		if (empty($product)) {
			$product = new Models\Product();
		}
			
		$img = NULL;
			

		$product->applyAttributes($_POST);
			
		//If file, capture and save that file
		if (!empty($_FILES['img']['name'])) {
			$img = $product->getImg();
	
			if (empty($img)) {
				$img = new Models\File($_FILES['img']['name'], $_FILES['img']['tmp_name'], "", $_FILES['img']['type']);
			} else {
				$img->reconstruct($_FILES['img']['name'], $_FILES['img']['tmp_name'], "", $_FILES['img']['type']);
			}
	
			$img->save();
			$product->img_id = $img->id;
		} else {
			$img = $product->getImg();
			if (empty($img)) {
				$img = new Models\File("package.png", "/home/houspcom/public_html/admin.moregreatstuff.ca/resources/img/package.png", "png", "image/png");
				$img->save();
				$product->img_id = $img->id;
			}
		}
	
		if ($product->save()) {
			//$dbh->commit();
			$success = true;
			$message = "Product successfully saved";
			$product_id = $product->id;
		} else {
			//$dbh->rollback();
			$errors[] = "Unable to save product";
		}
	}
	
	echo json_encode(array(
		"success" => $success,
		"message" => $message,
		"errors" => $errors,
		"product_id" => $product_id
	));exit;
}

function getProduct () {
	return Models\Product::findById($_POST['product_id']);
}