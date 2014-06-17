<?php
require_once "config.php";

if($_POST['action'] == "position_edit"){
	$_POST = $db->selectSingleQueryArray("positions", "*", "id=".$_POST['id']);
	echo "<p><strong>Edit - {$_POST['title']}</strong> [<a id='{$_POST['id']}'>Cancel</a>]</p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("title",$_POST['title'],'class="form-control"',"Title","",TRUE);
	$form->textarea("desc",$_POST['desc'],'class="form-control"',"Description");
	$active = array("1"=>"Yes","0"=>"No");
	$form->select("active",$_POST['active'],$active,'class="form-control"',"Active");
	$form->hidden("id",$_POST['id']);
	$form->button("position_btn","Edit Position","class='btn btn-default'");
	$form->formEnd();
	$loader = get_ajax_loader("#container_p{$_POST['id']}");
	echo <<<EOF
			<script>
				$("#{$_POST['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "position_edit_cancel", id: "{$_POST['id']}" }
					}).done(function( result ) {
			    		$("#container_p{$_POST['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
}
else if($_POST['action'] == "position_edit_cancel"){
	$v = $db->selectSingleQueryArray("positions", "*", "id=".$_POST['id']);
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
else if($_POST['action'] == "status_edit"){
	$_POST = $db->selectSingleQueryArray("applicant_status", "*", "id=".$_POST['id']);
	echo "<p><strong>Edit - {$_POST['status']}</strong> [<a id='{$_POST['id']}'>Cancel</a>]</p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("status",$_POST['status'],'class="form-control"',"Status","",TRUE);
	$form->textarea("desc",$_POST['desc'],'class="form-control"',"Description");
	$active = array("1"=>"Yes","0"=>"No");
	$form->select("active",$_POST['active'],$active,'class="form-control"',"Active");
	$form->hidden("id",$_POST['id']);
	$form->button("status_btn","Edit Status","class='btn btn-default'");
	$form->formEnd();
	$loader = get_ajax_loader("#container_s{$_POST['id']}");
	echo <<<EOF
			<script>
				$("#{$_POST['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "status_edit_cancel", id: "{$_POST['id']}" }
					}).done(function( result ) {
			    		$("#container_s{$_POST['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
}
else if($_POST['action'] == "status_edit_cancel"){
	$v = $db->selectSingleQueryArray("applicant_status", "*", "id=".$_POST['id']);
	echo "<div id='status_p{$v['id']}' style='margin-bottom: 15px;'>";
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
else if($_POST['action'] == "checkbox_edit"){
	$_POST = $db->selectSingleQueryArray("applicant_checkboxes", "*", "id=".$_POST['id']);
	echo "<p><strong>Edit - {$_POST['item']}</strong> [<a id='{$_POST['id']}'>Cancel</a>]</p>";
	$form->formStart("","POST",'class="bs-example form-horizontal"');
	$form->text("item",$_POST['item'],'class="form-control"',"Checkbox Name","",TRUE);
	$form->textarea("desc",$_POST['desc'],'class="form-control"',"Description");
	$active = array("1"=>"Yes","0"=>"No");
	$form->select("active",$_POST['active'],$active,'class="form-control"',"Active");
	$form->hidden("id",$_POST['id']);
	$form->button("checkbox_btn","Edit checkbox","class='btn btn-default'");
	$form->formEnd();
	$loader = get_ajax_loader("#container_c{$_POST['id']}");
	echo <<<EOF
			<script>
				$("#{$_POST['id']}").click(function(){
					$loader
					$.ajax({
			  			type: "POST",
			  			url: "ajax.php",
			  			data: { action: "checkbox_edit_cancel", id: "{$_POST['id']}" }
					}).done(function( result ) {
			    		$("#container_c{$_POST['id']}").html(result);
			  		});
			  	});
			</script>
EOF;
}
else if($_POST['action'] == "checkbox_edit_cancel"){
	$v = $db->selectSingleQueryArray("applicant_checkboxes", "*", "id=".$_POST['id']);
	echo "<div id='checkbox_c{$v['id']}' style='margin-bottom: 15px;'>";
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
else if($_POST['action'] == "delete_user"){
	echo "Confirm Delete? <a id='yes{$_POST['id']}'>Yes</a> | <a id='no{$_POST['id']}'>No</a>";
	$loader = get_ajax_loader("#container_user_prompt{$v['id']}");
	echo <<<EOF
	<script>
		$("#no{$_POST['id']}").click(function(){
			$loader
			$("#container_user_prompt{$_POST['id']}").empty();
	  	});
	  	$("#yes{$_POST['id']}").click(function(){
	  		$loader
			$.ajax({
	  			type: "POST",
	  			url: "ajax.php",
	  			data: { action: "delete_user_confirmed", id: "{$_POST['id']}" }
			}).done(function( result ) {
	    		$("#container_user{$_POST['id']}").empty();
	    		$("#container_user_prompt{$_POST['id']}").empty();
	  		});
	  	});
	</script>
EOF;
}
else if($_POST['action'] == "delete_user_confirmed"){
	$db->deleteQuery("pt_users", "id=".$_POST['id']);
}
else if($_POST['action'] == 'check'){
	$id = $_POST['id'];
	$aid = $_POST['aid'];
	$checks = $db->selectSingleQuery("applicants", "checkbox", "id=".$aid);
	$arr_c = explode(",", $checks);
	if(!in_array($id, $arr_c)){
		if(empty($checks) || $checks == "")
			$checks = "$id";
		else
			$checks .= ",$id";
		$db->updateQuery("applicants",array('checkbox'=>$checks),"id=$aid");
	}
}	
else if($_POST['action'] == 'uncheck'){
	$id = $_POST['id'];
	$aid = $_POST['aid'];
	$checks = $db->selectSingleQuery("applicants", "checkbox", "id=".$aid);
	$arr_c = explode(",", $checks);
	if(in_array($id, $arr_c)){
		$checks = "";
		foreach($arr_c AS $k=>$v){
			if($v==$id)
				continue;
			$checks .= "$v,";
		}
		if(!empty($checks) || $checks != "")
			$checks = substr($checks, 0, -1);
		$db->updateQuery("applicants",array('checkbox'=>$checks),"id=$aid");
	}
}
?>