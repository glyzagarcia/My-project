<?php
require 'config.php';
if(!isset($_SESSION['u']) || !in_array($_SESSION['u'], $authorized)){
	header("Location: login.php");
	//header("Location: login.php?logout=1");
	exit();
}

if(isset($_POST['edit_entry'])){
	if(empty($_POST['lname'])){
		$error['lname'] = "Last Name empty.";
	}
	if(empty($_POST['fname'])){
		$error['fname'] = "First Name empty.";
	}
	if(empty($_POST['bdate'])){
		$error['bdate'] = "Birthdate empty.";
	}
	if(empty($_POST['address'])){
		$error['address'] = "Address is empty.";
	}
	if(empty($_POST['mnumber'])){
		$error['mnumber'] = "Mobile empty.";
	}
	if(strpos($_POST['email'],"djuban@careerfirstinstitute.com") === FALSE){
		if(is_good_email($_POST['email'], $_GET['id']) !== TRUE){
			$error['email'] = is_good_email($_POST['email']);
		}
	}
	if(empty($_POST['text_resume'])){
		$error['text_resume'] = "Text Resume empty.";
	}
	if(sizeof($error)==0){
		unset($_POST['edit_entry']);
		$db->updateQuery("applicants", $_POST, "id=".$_POST['id']);
		header("Location: view_info.php?id={$_POST['id']}");
		exit();
	}
}

if(isset($_POST['submit']) && isset($_GET['id'])){
	if(!empty($_POST['status'])){
		$current_stat = $db->selectSingleQuery("applicants", "status", "id=".$_GET['id']);
		if($current_stat !== $_POST['status']){
			$db->updateQuery("applicants", array('status'=>$_POST['status']), "id=".$_GET['id']);
			$old_status = $db->selectSingleQuery("applicant_status", "status", "id=$current_stat");
			$new_status = $db->selectSingleQuery("applicant_status", "status", "id={$_POST['status']}");
			$_POST['remarks'] = "<span class='success'>Changed status from <b>$old_status</b> to <b>$new_status</b></span> <br/>".$_POST['remarks'];
			$update[] = "Applicant Status updated.";
		}
	}
	if(!empty($_POST['remarks'])){
		$db->insertQuery("applicant_feedbacks", array('applicant_id'=>$_GET['id'], 'pt_username'=>$_SESSION['u'], 'remarks'=>$_POST['remarks'], 'date_created'=>"NOW()") );
		$update[] = "Remarks saved.";
	}
	
}

$result = $db->selectSingleQueryArray("applicants a", "a.*, a_s.status AS status, a.status AS status_id,a.uploads","a.id={$_GET['id']}","LEFT JOIN applicant_status a_s ON a_s.id=a.status" );

require 'includes/header.php';
if(!is_array($result) || sizeof($result) == 0):
	echo "<p class='error'>Applicant info not found.</p>";
else :
	if(!isset($_POST['edit_entry'])){
		$_POST = $result;
	}
?>
<style type="text/css">
.highlight {
    background-color: #fff34d;
    -moz-border-radius: 5px; /* FF1+ */
    -webkit-border-radius: 5px; /* Saf3-4 */
    border-radius: 5px; /* Opera 10.5, IE 9, Saf5, Chrome */
    -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7); /* FF3.5+ */
    -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7); /* Saf3.0+, Chrome */
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7); /* Opera 10.5+, IE 9.0 */
}

.highlight {
    padding:1px 4px;
    margin:0 -4px;
}
</style>
<div class='container'>
			<fieldset>
                  <legend><?php echo $_POST['fname']." ".$_POST['mname']." ".$_POST['lname']." ".$_POST['suffix']." ({$_POST['status']})"; echo get_error();?></legend>
                    	<!-- Checklist -->
						<?php 
                    		$checklists = $db->selectQuery("applicant_checkboxes", "id,item", "active ORDER BY item ASC");
                    		if(is_array($checklists)){
                    			$arr_checks = explode(",", $_POST['checkbox']);
                    			$read_only = is_admin()?"":"disabled";
                    			echo "<div id='load_checkboxes'></div>";
                    			echo "<p>";
                    			foreach($checklists AS $k => $v){
                    				$checked = in_array($v['id'], $arr_checks)? "checked":"";
                    				echo "<input $read_only $checked id='check_{$v['id']}' type='checkbox'> {$v['item']} <br/>";
                    				if(is_admin()):
                    				?>
	                    			<script>
										$("#check_<?php echo $v['id'];?>").change(function(){
											<?php echo get_ajax_loader("#load_checkboxes");?>
											var act = "";
											if($(this).is(":checked")){
												act = "check";
											}
											else{
												act = "uncheck";
											}
											$.ajax({
									  			type: "POST",
									  			url: "ajax.php",
									  			data: { action: act, id: "<?php echo $v['id'];?>", aid:"<?php echo $_POST['id'];?>" }
											}).done(function( result ) {
												$('#load_checkboxes').empty();
									  		});
										});
									</script>
	                    			<?php 
	                    			endif;
                    			}
                    			echo "</p>";
                    			echo "<hr/>";
                    		}
                    	?>
                    	<!-- Comments/Status -->
                    	<?php $form->setEditable(FALSE);?>
                    	<?php 
                    	if(is_admin()){
                    		$form->setEditable(TRUE);
                    		$status = $db->selectQuery("applicant_status", "id,status", "active=1 ORDER BY status ASC");
                    		$stat = array();
                    		if(is_array($status)){
                    			foreach($status AS $v){
                    				$stat[$v['id']] = $v['status'];	
                    			}
                    		}
                    	}
                    	else{
                    		$status = $db->selectQuery("applicant_status", "id,status", "id={$_POST['status_id']} AND active=1 ORDER BY status ASC");
                    		$stat = array();
                    		if(is_array($status)){
                    			foreach($status AS $v){
                    				$stat[$v['id']] = $v['status'];	
                    			}
                    		}
                    	}
                    	$form->formStart();
                    	$form->select("status",$_POST['status_id'],$stat,'class="form-control"',"Change Status");
                    	$form->setEditable(TRUE);
                    	$form->textarea("remarks","",'class="editable form-control" rows="6"',"Add Remarks");
                    	$form->button("submit","Submit","class='btn btn-primary'");
                    	$form->formEnd();
                    	echo "<p style='border-bottom: 1px solid #999;'>&nbsp;</p>";
                    	?>
						<?php $form->setEditable(FALSE);?>
                    	<?php
                    		$remarks = $db->selectQuery("applicant_feedbacks", "*", "applicant_id = ".$_GET['id']." ORDER BY timestamp DESC");
                    		if(is_array($remarks)){ 
                    			foreach($remarks AS $k => $v){
                    				$form->textarea("remarks",$v['remarks']."<p><span style='font-size: 0.8em'>By: {$v['pt_username']} | {$v['date_created']}</span></p>",'class="editable form-control"',"Remarks");
                    				echo "<p style='border-bottom: 1px solid #999;'>&nbsp;</p>";
                    			}
                    		}
                    	?> 	
                    	<div class="clear"></div>
                    	<hr/>
                    	<?php 
                    		if(is_admin() && empty($_GET['edit'])){
                    			echo "<a href='view_info.php?id={$_GET['id']}&edit=1#edit'>Edit</a>";
                    		}
                    	?>
						<?php $form->setEditable(FALSE);?>
						<?php 
							if(is_admin() && $_GET['edit'] == '1'){
                    			echo "<a href='view_info.php?id={$_GET['id']}'>Cancel Edit</a>";
                    			$form->setEditable(TRUE);
                    			$form->formStart();
                    			$form->hidden("id",$_POST['id']);
                    		}
						?>
						<a name='edit'></a>
						<?php $form->text("lname",$_POST['lname'],'class="form-control"',"Last Name","");?>
                    	<?php $form->text("fname",$_POST['fname'],'class="form-control"',"First Name","");?>
                    	<?php $form->text("mname",$_POST['mname'],'class="form-control"',"Middle Name");?>
                    	<?php $form->text("suffix",$_POST['suffix'],'class="form-control"',"Suffix");?>
                    	<?php $form->date("bdate",$_POST['bdate'],'class="form-control"',"Birthdate","");?>
                    	<?php $form->textarea("address",$_POST['address'],'class="form-control"',"Address","");?>
                    	<?php $form->text("mnumber",$_POST['mnumber'],'class="form-control"',"Mobile Number","Mobile #");?>
                    	<?php $form->email("email",$_POST['email'],'class="form-control"',"Email Address","");?>
                    	<?php 
                    		$pos = $db->selectSingleQuery("positions", "title", "id=".$_POST['position']);
                    		$pos1 = $db->selectSingleQuery("positions", "title", "id=".$_POST['position1']);
                    		$pos2 = $db->selectSingleQuery("positions", "title", "id=".$_POST['position2']);
                    		if(is_admin() && $_GET['edit'] == '1'){
	                    		$pos = $db->selectQuery("positions", "id,title", "active=1 ORDER BY title ASC");
	                    		$positions = array(" ");
	                    		if(is_array($pos)){
	                    			foreach($pos AS $v){
	                    				$positions[$v['id']] = $v['title'];	
	                    			}
	                    		}
	                    		$pos = $positions;
	                    		$pos1 = $positions;
	                    		$pos2 = $positions;
                    		}
                    	?>
                    	<?php $form->select("position",$_POST['position'],$pos,'class="form-control"',"Position applied(Primary)","");?>
                    	<?php $form->select("position1",$_POST['position1'],$pos1,'class="form-control"',"Position applied","");?>
                    	<?php $form->select("position2",$_POST['position2'],$pos2,'class="form-control"',"Position applied","");?>
                    	<?php $form->text("source",$_POST['source'],'class="form-control"',"Where did you hear about us");?>						
						<?php if(!empty($_POST['source_field'])) $form->text("source_field",$_POST['source_field'],'class="form-control"',""); ?>						
                    	<?php 
							if(!empty($_POST['link']))
								$label = $_POST['link'];
							else
								$label = "No Portfolio Link(s) available";
							$form->textarea("link",$_POST['link'],'class="form-control" rows="2"',"Portfolio Link(s)",$label);?>
                    	<?php $form->text("expected_salary",$_POST['expected_salary'],'class="form-control"',"Expected Salary","in PHP");?>
                    	<hr/>
                    	<?php $form->text("last_employer",$_POST['last_employer'],'class="form-control"',"Last Employer");?>
                    	<?php $form->text("employment_period",$_POST['employment_period'],'class="form-control"',"Employment Period");?>
						<div class="clear"></div>
						<div id='search_tags'>
							<a name="tags"></a>
                    	<?php $form->textarea("text_resume",$_POST['text_resume'],'class="editable form-control" rows="21" ',"Text Resume","");?>
                    	</div>
						<?php 
                    		if(is_admin() && $_GET['edit'] == '1'){
                    			$form->button("edit_entry","Edit","class='btn btn-primary'");
                    			$form->formEnd();
                    		}
                    	?>
                    	
                    	<div class='form-head-title' style='margin-top:21px;'><p><strong>Attachment: </strong></p><?php echo get_uploaded_file_icon("uploads/resumes/{$_POST['uploads']}/","large");?></div>
                    	<div class="clear"></div>
                    	<hr/>
                    	<h3>HR Uploads (<?php echo trim($_POST['suffix']." ".$_POST['fname']." ".$_POST['mname']." ".$_POST['lname']);?>)</h3>
                    	<div class='container'>
                    	<a name='attach_file'></a>
                    	<?php 
						    $dir = "uploads/applicants/{$_GET['id']}/";
						    $uploader = new uploader($dir, "Choose File...","Start Upload","Cancel Upload");
						    $uploader->set_multiple(TRUE);
						    $uploader->uploader_html();
              			?>
                    	</div>
			</fieldset>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var tag = window.location.search.substring(1);
		tag = tag.split('tag=');
		if( typeof tag[1] != 'undefined' ){
			$('#search_tags').highlight( tag[1] );
		}		
	});
</script>

<?php 
endif;
require 'includes/footer.php';
?>