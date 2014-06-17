<?php
require 'config.php';

if($_GET['logout'] == "1"){
	session_destroy();
	unset($_SESSION);
	header("Location: index.php");
	exit();
}

if(isset($_SESSION['u'])){
	$result = $ptDb->selectSingleQuery("staff", "COUNT(uid)", "username='{$_SESSION['u']}' AND password='{$_SESSION['pass']}'");
	if($result > 1 && in_array($_SESSION['u'],$authorized)){
		header("Location: index.php");
		exit();
	}
	else{
		unset($_SESSION);
	}
}

if(isset($_POST['submit'])){
	$result = $ptDb->selectSingleQueryArray("staff", array("username","password","sFirst","sLast"), "username='{$_POST['username']}' AND password='{$_POST['password']}'");
	if(is_array($result) && sizeof($result) != 0 && in_array($result['username'],$authorized)){
		$_SESSION['u'] = $result['username'];
		$_SESSION['pass'] = $result['password'];
		$_SESSION['name'] = $result['sFirst']." ".$result['sLast'];
		$_SESSION['level'] = $db->selectSingleQuery("pt_users", "level", "username = '{$result['username']}'");
		header("Location: applicants.php");
		exit();
	}
	else{
		$error['username'] = "Username or Password is invalid.";
		$error['password'] = "Please try again.";
	}
}

require 'includes/header.php';
$form->formStart("","POST",'class="bs-example form-horizontal"');
$form->text("username","","","Username","PT Username");
$form->password("password","","","Password");
$form->button("submit","Login","class='btn btn-primary'","class='col-lg-10 col-lg-offset-2'");
$form->formEnd();
require 'includes/footer.php';
?>