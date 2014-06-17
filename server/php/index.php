<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

//Local
//require '../../config.php';

//Online
session_start();
define('DIR_DOWNLOAD', $_SERVER['DOCUMENT_ROOT'].'/');
define ('HTTP_SERVER' , 'http://'.$_SERVER['HTTP_HOST'].'/');

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');

$upload_handler = new UploadHandler(	array(
	'upload_dir' => DIR_DOWNLOAD . "/" . $_SESSION['upload_dir'],
	'upload_url' => HTTP_SERVER  . "/" . $_SESSION['upload_dir'] )
);