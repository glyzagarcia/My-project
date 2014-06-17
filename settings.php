<?php
require 'config.php';

if(!isset($_SESSION['u']) || !in_array($_SESSION['u'], $authorized)){
	header("Location: login.php?logout=1");
	exit();
}

if(!is_admin()){
	header("Location: applicants.php");
	exit();
}

if(isset($_POST['position_btn'])){
	if(empty($_POST['title'])){
		$error['title'] = "Title shouldn't be empty.";
	}
	else{
		$title = strtolower($_POST['title']);
		$count = $db->selectSingleQuery("positions", "COUNT(id)", "LOWER(CONVERT(title USING Latin1)) = '$title' AND id<>{$v['id']}");
		if($count > 0)
			$error['title'] = "Position title already exist.";
	}
	if(sizeof($error) == 0){
		unset($_POST['position_btn']);
		$_POST['pt_username'] = $_SESSION['u'];
		if(!isset($_POST['id']))
			$_POST['date_created'] = "NOW()";
		$db->insertUpdateQuery("positions", $_POST);
		if(isset($_POST['id'])){
			$edited_title = $db->selectSingleQuery("positions", "title", "id=".$_POST['id']);
			$update[] = "Position \"$edited_title\" edited!";
		}
		else
			$update[] = "New position added!";
		unset($_POST);
	}
}

if(isset($_POST['status_btn'])){
	if(empty($_POST['status'])){
		$error['status'] = "status shouldn't be empty.";
	}
	else{
		$status = strtolower($_POST['status']);
		$count = $db->selectSingleQuery("applicant_status", "COUNT(id)", "LOWER(CONVERT(status USING Latin1)) = '$status' AND id<>{$v['id']}");
		if($count > 0)
			$error['status'] = "Status already exist.";
	}
	if(sizeof($error) == 0){
		unset($_POST['status_btn']);
		$_POST['pt_username'] = $_SESSION['u'];
		if(!isset($_POST['id']))
			$_POST['date_created'] = "NOW()";
		$db->insertUpdateQuery("applicant_status", $_POST);
		if(isset($_POST['id'])){
			$edited_status = $db->selectSingleQuery("applicant_status", "status", "id=".$_POST['id']);
			$update[] = "Status \"$edited_status\" edited!";
		}
		else
			$update[] = "New status added!";
		unset($_POST);
	}
}

if(isset($_POST['checkbox_btn'])){
	if(empty($_POST['item'])){
		$error['status'] = "Checkbox name shouldn't be empty.";
	}
	else{
		$item = strtolower($_POST['item']);
		$count = $db->selectSingleQuery("applicant_checboxes", "COUNT(id)", "LOWER(CONVERT(status USING Latin1)) = '$item' AND id<>{$v['id']}");
		if($count > 0)
			$error['status'] = "Checkbox name already exist.";
	}
	if(sizeof($error) == 0){
		unset($_POST['checkbox_btn']);
		$_POST['pt_username'] = $_SESSION['u'];
		if(!isset($_POST['id']))
			$_POST['date_created'] = "NOW()";
		$db->insertUpdateQuery("applicant_checkboxes", $_POST);
		if(isset($_POST['id'])){
			$edited_status = $db->selectSingleQuery("applicant_checkboxes", "item", "id=".$_POST['id']);
			$update[] = "Checkbox \"$edited_status\" edited!";
		}
		else
			$update[] = "New checkbox added!";
		unset($_POST);
	}
}

if(isset($_POST['pt_user_btn'])){
	if(empty($_POST['username'])){
		$error['username'] = "status shouldn't be empty.";
	}
	else{
		$count = $db->selectSingleQuery("pt_users", "COUNT(id)", "username = '{$_POST['username']}'");
		if($count > 0)
			$error['username'] = "Username already exist.";
	}
	if(sizeof($error) == 0){
		unset($_POST['pt_user_btn']);
		$_POST['pt_username'] = $_SESSION['u'];
		$db->insertUpdateQuery("pt_users", $_POST);
		$update[] = "New PT User added!";
		unset($_POST);
	}
}
require 'includes/header.php';
?>
<script>
  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content",
      collapsible: true,
      active: false
    });
  });
</script>

<div class="container">
<?php echo get_error();?>
<div id="accordion">
<!-- POSITIONS -->
<h3 style="font-weight: bold;">Available Positions</h3>
<div>
<?php 
	$positions = $db->selectQuery("positions", "*", "1 ORDER BY title ASC");
	if(is_array($positions)){
		foreach($positions AS $k => $v){
			echo "<div id='container_p{$v['id']}' style='margin-bottom: 15px;'>";
			$act = $v['active']=="1"? "Yes":"No";
			echo "<p>{$v['title']} <span style='margin-left: 20px;font-size: 0.8em;'> Active=<b>".$act."</b>, Last update: {$v['timestamp']}, By: <b>{$v['pt_username']}</b></span> [<a id='position{$v['id']}'>edit</a>]</p>";
			echo "<p style='margin-left: 20px;'>-".nl2br($v['desc'])."</p>";
			echo "</div>";
			$loader = get_ajax_loader("#container_p{$v['id']}");
			echo <<<EOF
			<script>
				$("#position{$v['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "position_edit", id: "{$v['id']}" }
					}).done(function( result ) {
			    		$("#container_p{$v['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
		}
	}
	echo "<div style='margin-bottom: 15px;'>";
	echo "<p><strong>Add New Position</strong></p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("title",$_POST['title'],'class="form-control"',"Title","",TRUE);
	$form->textarea("desc",$_POST['desc'],'class="form-control"',"Description");
	$active = array("1"=>"Yes","0"=>"No");
	$form->select("active",$_POST['active'],$active,'class="form-control"',"Active");
	$form->button("position_btn","Add Position","class='btn btn-primary'");
	$form->formEnd();
	echo "</div>";
?>
</div>
<!-- STATUS -->
<h3 style="font-weight: bold;">Status</h3>
<div>
<?php 
	$status = $db->selectQuery("applicant_status", "*", "1 ORDER BY status ASC");
	if(is_array($status)){
		foreach($status AS $k => $v){
			echo "<div id='container_s{$v['id']}' style='margin-bottom: 15px;'>";
			$act = $v['active']=="1"? "Yes":"No";
			echo "<p>{$v['status']} <span style='margin-left: 20px;font-size: 0.8em;'> Active=<b>".$act."</b>, Last update: {$v['timestamp']}, By: <b>{$v['pt_username']}</b></span> [<a id='status{$v['id']}'>edit</a>]</p>";
			echo "<p style='margin-left: 20px;'>-".nl2br($v['desc'])."</p>";
			echo "</div>";
			$loader = get_ajax_loader("#container_s{$v['id']}");
			echo <<<EOF
			<script>
				$("#status{$v['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "status_edit", id: "{$v['id']}" }
					}).done(function( result ) {
			    		$("#container_s{$v['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
		}
	}
	echo "<div style='margin-bottom: 15px;'>";
	echo "<p><strong>Add New Status</strong></p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("status",$_POST['status'],'class="form-control"',"Status","",TRUE);
	$form->textarea("desc",$_POST['desc'],'class="form-control"',"Description");
	$active = array("1"=>"Yes","0"=>"No");
	$form->select("active",$_POST['active'],$active,'class="form-control"',"Active");
	$form->button("status_btn","Add Status","class='btn btn-primary'");
	$form->formEnd();
	echo "</div>";
?>
</div>
<!-- CHECKBOXES -->
<h3 style="font-weight: bold;">Applicant Checklists</h3>
<div>
<?php 
	$checkbx = $db->selectQuery("applicant_checkboxes", "*", "1 ORDER BY item ASC");
	if(is_array($checkbx)){
		foreach($checkbx AS $k => $v){
			echo "<div id='container_c{$v['id']}' style='margin-bottom: 15px;'>";
			$act = $v['active']=="1"? "Yes":"No";
			echo "<p>{$v['item']} <span style='margin-left: 20px;font-size: 0.8em;'> Active=<b>".$act."</b>, Last update: {$v['timestamp']}, By: <b>{$v['pt_username']}</b></span> [<a id='checkbox{$v['id']}'>edit</a>]</p>";
			echo "<p style='margin-left: 20px;'>-".nl2br($v['desc'])."</p>";
			echo "</div>";
			$loader = get_ajax_loader("#container_c{$v['id']}");
			echo <<<EOF
			<script>
				$("#checkbox{$v['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "checkbox_edit", id: "{$v['id']}" }
					}).done(function( result ) {
			    		$("#container_c{$v['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
		}
	}
	echo "<div style='margin-bottom: 15px;'>";
	echo "<p><strong>Add New Checkbox</strong></p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("item",$_POST['item'],'class="form-control"',"Checkbox Name","",TRUE);
	$form->textarea("desc",$_POST['desc'],'class="form-control"',"Description");
	$active = array("1"=>"Yes","0"=>"No");
	$form->select("active",$_POST['active'],$active,'class="form-control"',"Active");
	$form->button("checkbox_btn","Add Status","class='btn btn-primary'");
	$form->formEnd();
	echo "</div>";
?>
</div>
<!-- USERS -->
<h3 style="font-weight: bold;">PT Authorized Users</h3>
<div>
<?php 
	$pt_users = $db->selectQuery("pt_users", "*");
	if(is_array($pt_users)){
		foreach($pt_users AS $k => $v){
			echo "<div id='container_user{$v['id']}' style='margin-bottom: 15px;'>";
			$delete = $_SESSION['u'] != $v['username']? "[<a id='pt_user{$v['id']}'>delete</a>]" : "";
			$level = $v['level']==1?"HR" : "Hiring Manager";
			echo "<p>{$v['username']} [<b>{$level}</b>]<span style='margin-left: 20px;font-size: 0.8em;'>Date added: {$v['timestamp']}, Added By: <b>{$v['pt_username']}</b></span> $delete</p>";
			echo "</div><div id='container_user_prompt{$v['id']}'></div>";
			$loader = get_ajax_loader("#container_user_prompt{$v['id']}");
			echo <<<EOF
			<script>
				$("#pt_user{$v['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "delete_user", id: "{$v['id']}" }
					}).done(function( result ) {
			    		$("#container_user_prompt{$v['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
		}
	}
	echo "<div style='margin-bottom: 15px;'>";
	echo "<p><strong>Add New PT User</strong></p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("username",$_POST['status'],'class="form-control"',"PT Username","",TRUE);
	$level_opt = array(1=>"HR",2=>"Hiring Manager");
	$form->select("level",$_POST['level'],$level_opt,'class="form-control"',"Credential Level");
	$form->button("pt_user_btn","Add PT User","class='btn btn-primary'");
	$form->formEnd();
	echo "</div>";
?>
</div>
</div>

</div>
<?php 
require 'includes/footer.php';
?>