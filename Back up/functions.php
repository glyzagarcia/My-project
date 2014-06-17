<?php
function pre($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function is_admin(){
	if($_SESSION['level']==1)
		return TRUE;
	else
		return FALSE;
}

function get_error(){
	global $error, $update;
	if(is_array($error) && sizeof($error) > 0){
		$style = "<style>";
		foreach($error AS $k => $v){
			if($k == "password")
				$style .= "input[name=re$k],";
			$style .= "input[name=$k],";
			$style .= "textarea[name=$k],";
			$style .= "select[name=$k],";
			$style .= "radio[name=$k],";
		}
		$style = substr($style,0,-1);
		$style .= "{border-color: #FF0000;box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(102, 175, 233, 0.6);outline: 0 none;}</style>";
		$err = implode("</p><p>", $error);
		$error_msg = "$style<div class='error'><p>$err</p></div>";
	}
	if(is_array($update) && sizeof($update) > 0){
		$update = implode("</p><p>", $update);
		$update_msg = "<div class='success'><p>$update</p></div>";
	}
	return $error_msg.$update_msg;
}

function nicetime($date)
{
    if(empty($date)) {
        return "No date provided";
    }
    
    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths         = array("60","60","24","7","4.35","12","10");
    
    $now             = time();
    $unix_date         = strtotime($date);
    
       // check validity of date
    if(empty($unix_date)) {    
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {    
        $difference     = $now - $unix_date;
        $tense         = "ago";
        
    } else {
        $difference     = $unix_date - $now;
        $tense         = "from now";
    }
    
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
    
    $difference = round($difference);
    
    if($difference != 1) {
        $periods[$j].= "s";
    }
    
    return "$difference $periods[$j] {$tense}";
}

function get_profile_pic($uid, $name="", $full_image=FALSE){
	$dir = $full_image? "uploads/profiles/$uid/":"uploads/profiles/$uid/thumbnail";
	$size = $full_image? "height='400' width='400'":"height='80' width='80'";
	$profile_pic = get_file($dir);
	if($profile_pic === FALSE){
		return "<img class='profile-pic' src='images/default_profile_pic.jpg' $size />";
	}
	else{
		$p_dir = urlencode("uploads/profiles/$uid/{$profile_pic[0]}");
		$name = urlencode($name);
		return "<a href='profile_image.php?name=$name&dir=$p_dir&&iframe=true&width=80%&height=100%' rel='launcher'><img class='profile-pic' src='$dir/{$profile_pic[0]}' $size /></a>";
	}
}

function get_uploaded_file_icon($dir,$size=NULL){
	$size = is_null($size)? "" : "_".$size;
	$file = get_file($dir);
	if($file && sizeof($file) != 0 && !empty($file[0])){
		return "<a href='$dir/{$file[0]}'><img src='img/attach_document$size.png'></a>";
	}
	return "&nbsp;";
}

function get_file($dir){
	if(is_dir($dir)){
		$files = array();
		$content  = opendir($dir);
		while (false !== ($file = readdir($content))) {
			if($file === "." || $file === ".." || strpos($file, ".") === FALSE)
				continue;
		    $files[] = $file;
		}
		return $files;
	}
	else{
		return FALSE;
	}
}

function is_good_email($email, $id=NULL){
	global $db;
	if(!is_null($id)){
		$filter = "AND id<>$id";
	}
	$email = trim($email);
	if(empty($email)){
		return "Email empty.";
	}
	else{
		$count = $db->selectSingleQuery("applicants", "COUNT(email)", "email = '{$email}' $filter");
		if($count > 0){
			return "Email already exist.";
		}
		else if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
			return "Invalid Email.";
		}
	}
	return TRUE;
}

function get_ajax_loader($element, $image='img/loading.gif'){
	if(!empty($image)){
		$img = "<img src='$image' /> ";
	}
	return <<<EOF
	$("$element").html("<div style='text-align:center;'>{$img}Loading...</div>");
EOF;
}
?>