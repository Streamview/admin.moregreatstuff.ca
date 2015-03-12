<?php
// set_include_path(get_include_path() . PATH_SEPARATOR . CLASSES_DIR);
//set_include_path(get_include_path() . PATH_SEPARATOR . DOCUMENT_ROOT. "vendor/");

spl_autoload_extensions('.php,.class.php');
spl_autoload_register(function ($class) {
	$class = str_replace("\\", "/",  $class);
	$path = CLASSES_DIR;
	$class_parts = explode("/", $class);

	foreach($class_parts as $key => $part) {
		if($key != 0){
			$path .= '/';
		}
		$path .= $part;
	}

	$file = $path . '.class.php';

	if (!file_exists($file)) {
		throw new Exception("File {$file} does not exist.");
	}

	//echo "REQUIRE $file";
	require_once($file);
});