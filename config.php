<?php
ini_set("display_errors", 1);
error_reporting(E_ERROR | E_PARSE | E_STRICT);
//error_reporting(E_ALL);


define("DOCUMENT_ROOT",'/home/houspcom/public_html/admin.moregreatstuff.ca/');
define('CLASSES_DIR',DOCUMENT_ROOT.'src/');

require_once(DOCUMENT_ROOT ."includes/include.php");
session_start();