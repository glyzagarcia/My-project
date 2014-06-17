<?php
ini_set('session.gc_maxlifetime', 86400);
session_start();

date_default_timezone_set('Asia/Singapore');

function __autoload($class_name){
	require_once 'classes/'.$class_name.'.class.php';
}
$dbname="projectTracker";
$user="pt";
$pass="publish127";
$host="ptracker.clhfapw0bgm7.us-east-1.rds.amazonaws.com";
// $host="129.3.252.99";
$ptDb = new database($dbname, $user, $pass, $host);
$dbname="tatecareerph_db";
$user="root";
//$pass="";
$pass="seabiscuit";
// $host="localhost";
$host="129.3.252.99";
$db = new database($dbname, $user, $pass, $host);
$form = new formHelper();
$content = new content($db);
$settings = new setting($db);
$knowledgebase = new knowledgebase($db);
define("COMPANY","Tate Publishing Philippines");
define("TITLE",COMPANY." - Career Portal");
//define("PROJECT","careerph/");
//define("HOME_URL","http://localhost/".PROJECT);
define("PROJECT","");
define("HOME_URL","http://careerph.tatepublishing.net/".PROJECT);
define('DIR_DOWNLOAD', $_SERVER['DOCUMENT_ROOT'].'/'.PROJECT);
define('HTTP_SERVER', 'http://'.$_SERVER['HTTP_HOST'].'/'.PROJECT);
define('RECAPTCHA_PUBLIC_KEY', '6Lcc1-0SAAAAAGihBOrtubq2yiwxXFIxn6LY4gkP');
define('RECAPTCHA_PRIVATE_KEY', '6Lcc1-0SAAAAALzvntTV5P7BRC8dbB0mfjKVUtst');
//Local
//define('RECAPTCHA_PUBLIC_KEY', '6LcbVc0SAAAAAOVnReQRiUjd7AxXVBdghdWt76U8');
//define('RECAPTCHA_PRIVATE_KEY', '6LcbVc0SAAAAAMoXEuXLxJUgwIE1N6Qohult4Wgd');

$error = array();
$update = array();
if(empty($_SESSION['authorized'])){
	$result = $db->selectQuery("pt_users", "username");
	if(is_array($result)){
		$_SESSION['authorized'] = array();
		foreach($result AS $v){
			$_SESSION['authorized'][] = $v['username'];
		}
	}
}
$authorized = $_SESSION['authorized'];
$current_page = strstr(basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']),".",true);

require 'includes/functions.php';

?>