<?php 
require 'config.php';
require_once 'includes/recaptchalib.php';
if(isset($_POST['submit'])){
	//Google captcha
	$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY,
								$_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
	if(!$resp->is_valid){
		$error[] = "Invalid code.";
	}
	unset($_POST["recaptcha_challenge_field"]);
	unset($_POST["recaptcha_response_field"]);
	//end Google captcha
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
		if(is_good_email($_POST['email']) !== TRUE){
			$error['email'] = is_good_email($_POST['email']);
		}
	}
	if(empty($_POST['source'])){
		$error['source'] = "Where did you hear about Tate Publishing is empty.";		
	} else {
		if($_POST['source'] == "Referred by a Tate Employee" || $_POST['source'] == "Online Portals" || $_POST['source'] == "Other"){
			if(empty($_POST['source_field']))
				$error['source_field'] = "Referal box is empty.";		
		}
	}
	
	if(empty($_POST['text_resume'])){
		$error['text_resume'] = "Text Resume empty.";
	}
	if(sizeof($error)==0){
		unset($_POST['submit']);
		$_POST['ipaddress'] = $_SERVER["REMOTE_ADDR"]."|".$_SERVER['HTTP_X_FORWARDED_FOR'];
		$_POST['date_created'] = "NOW()";
		$db->insertQuery("applicants", $_POST);
		$update[] = "<h2>Thank You!</h2><p>You have successfully sent your Application Form to us. Please check your mobile and email inbox regularly for updates on your application.</p><p>For more info, please visit our website at <a href='https://www.tatepublishing.com'>https://www.tatepublishing.com</a></p>";
		unset($_POST);
		unset($_SESSION['uploads']);
	}
}

require 'includes/header.php';
?>
    <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="container">
        <div class="well">
        	<?php echo get_error();?>
        	<?php if(sizeof($update) == 0):?>
        	<!-- Form -->
              <?php $form->formStart("","POST",'class="bs-example form-horizontal"');?>
                <fieldset>
                  <legend>Application Form    <br/><font size="2px;" color="red"><b>Check your mobile and email inbox regularly for updates on your application. </b></font></legend>
				
                    	<?php $form->text("lname",$_POST['lname'],'class="form-control"',"Last Name","",TRUE);?>
                    	<?php $form->text("fname",$_POST['fname'],'class="form-control"',"First Name","",TRUE);?>
                    	<?php $form->text("mname",$_POST['mname'],'class="form-control"',"Middle Name");?>
                    	<?php $form->text("suffix",$_POST['suffix'],'class="form-control"',"Suffix");?>
                    	<?php $form->date("bdate",$_POST['bdate'],'class="form-control"',"Birthdate","",TRUE);?>
                    	<?php $form->textarea("address",$_POST['address'],'class="form-control"',"Address","",TRUE);?>
                    	<?php $form->text("mnumber",$_POST['mnumber'],'class="form-control"',"Mobile Number","Mobile #",TRUE);?>
                    	<?php $form->email("email",$_POST['email'],'class="form-control"',"Email Address","",TRUE);?>
                    	<?php 
                    		$pos = $db->selectQuery("positions", "id,title", "active=1 ORDER BY title ASC");
                    		$positions = array(" ");
                    		if(is_array($pos)){
                    			foreach($pos AS $v){
                    				$positions[$v['id']] = $v['title'];	
                    			}
                    		}
                    	?>
                    	<?php $form->select("position",$_POST['position'],$positions,'class="form-control"',"Position applied (Primary)","",TRUE);?>
                    	<?php $form->select("position1",$_POST['position1'],$positions,'class="form-control"',"Position applied");?>
                    	<?php $form->select("position2",$_POST['position2'],$positions,'class="form-control"',"Position applied");?>
                    	
						<div class="form-group $required">
							<label for="select" class="col-lg-2 control-label">Where did you hear about Tate Publishing?</label>
							<div class="col-lg-10">
								<select class="form-control" onchange='onchangeSource(this.value);' id="source" name="source">
									<option></option>
									<option value="Career First Institute" >Career First Institute</option>
									<option value="Friends">Friends</option>									
									<option value="Job Fair">Job Fair</option>
									<option value="Orient Express">Orient Express</option>
									<option value="Online Portals">Online Portals</option>
									<option value="Other">Other</option>
									<option value="Referred by a Tate Employee">Referred by a Tate Employee</option>									
									<option value="Walk In">Walk In</option>
								</select>
							</div>
							
						</div>
						<?php $form->text("source_field",$_POST['expected_salary'],'class="form-control" style="display:none;" id="source_field"',"","");?>
                    	<?php $form->textarea("link",$_POST['link'],'class="form-control" rows="2"',"Portfolio Link(s)");?>
                    	<?php $form->text("expected_salary",$_POST['expected_salary'],'class="form-control"',"Expected Salary","in PHP");?>
                    	<hr/>
                    	<?php $form->text("last_employer",$_POST['last_employer'],'class="form-control"',"Last Employer");?>
                    	<?php $form->text("employment_period",$_POST['employment_period'],'class="form-control"',"Employment Period from your Last Employer");?>
                    	<?php $form->textarea("text_resume",$_POST['text_resume'],'class="editable form-control" rows="21"',"Text Resume","",TRUE);?>
                    	<div class="clear"></div>
                    	<?php $random = isset($_SESSION['uploads'])?$_SESSION['uploads']:md5(time()); $form->hidden("uploads",$random);?>
                    	<?php $form->google_recaptcha(); ?>
                    	<?php $form->button("submit","Submit","id='submit_button' class='btn btn-primary' style='display:none;'");?>
                		<hr/>
                </fieldset>
              <?php $form->formEnd();?>
              <div class="form-head-title">
              <?php 
              				$_SESSION['uploads'] = $random;
						    $dir = "uploads/resumes/$random/";
						    $uploader = new uploader($dir, "Upload file resume...",FALSE,FALSE);
						    $uploader->set_num_file(1);
						    $uploader->uploader_html();
              ?>
              <a id='submit_decoy' class='btn btn-primary'><b style='font-size: 1.5em;'>Submit</b></a>
              </div>
              <?php endif;?>
            </div>
		<!-- End Well -->
      </div>
      <script>
	
		function onchangeSource(value) {		
			if(value == "Referred by a Tate Employee"){								
				document.getElementById("source_field").style.display= "block";
				document.getElementById("source_field").placeholder= "Enter name of Tate Employee here";			
			}
			
			else if(value == "Online Portals"){								
				document.getElementById("source_field").style.display= "block";
				document.getElementById("source_field").placeholder= "What Online Portals?";			
			}
			else if(value == "Other"){								
				document.getElementById("source_field").style.display= "block";
				document.getElementById("source_field").placeholder= "Please list down here.";			
			}
			else {
				document.getElementById("source_field").style.display= "none";
			}
			
		}
	  
	  
      	$('#submit_decoy').click(function(){
			$('#submit_button').trigger('click');
        });
      </script>
<?php 
	require 'includes/footer.php';
?>
