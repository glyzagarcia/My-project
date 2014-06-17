<?php
class formHelper{
	
	var $editable;
	var $showedit;
	
	function __construct(){
		$this->editable = TRUE;
		$this->showedit = TRUE;
	}
	
	function setEditable($edit = TRUE){
		$this->editable = $edit;
	}
	
	function setShowEdit($show = TRUE){
		$this->showedit = $show;
	}
	
	function formStart($action="", $method="post", $attributes=""){
		echo <<<EOF
		<form action="$action" method="$method" $attributes>
EOF;
	}
	
	function formEnd(){
		if($this->editable){
		echo <<<EOF
		</form>
EOF;
		}
	}
	
	function submit($name, $value="", $attributes="", $url="", $cancel=FALSE){
		if($this->showedit === FALSE)
			return;
		if(!$this->editable)
			echo "<a href='$url'>Edit</a>";
		if(empty($value))
			$value = $name;
		if($cancel){
			$url = substr($url,0,(strrpos($url, "edit")-1));
			$cancel_btn = "[ <a href='$url'>Cancel</a> ]";
		}
		echo <<<EOF
		<button type="submit" name="$name" $attributes />$value</button> $cancel_btn
EOF;
	}
	
	function hidden($name, $value="", $attributes=""){
		echo <<<EOF
		<input type="hidden" name="$name" value="$value" $attributes />
EOF;
	}
	
	function text($name, $value="", $attributes="", $label="", $placeholder="", $required=FALSE){
		$required = $required? "has-warning" : "";
		$placeholder = empty($placeholder)? $label : $placeholder;
		$read_only = $this->editable? "":"readonly";
		echo <<<EOF
		<div class="form-group $required">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
				<input $read_only type="text" name="$name" value="$value" placeholder="$placeholder" $attributes />
			</div>
       	</div>
EOF;
	}
	
	function password($name, $value="", $attributes="", $label="", $placeholder="", $required=FALSE){
		$required = $required? "has-warning" : "";
		$placeholder = empty($placeholder)? $label : $placeholder;
		if($this->editable)
			echo <<<EOF
		<div class="form-group $required">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
				<input type="password" name="$name" value="$value" Placeholder="$placeholder" $attributes />
			</div>
		</div>
EOF;
	}
	
	function email($name, $value="", $attributes="", $label="", $placeholder="", $required=FALSE){
		$required = $required? "has-warning" : "";
		$placeholder = empty($placeholder)? $label : $placeholder;
		$read_only = $this->editable? "":"readonly";
		echo <<<EOF
		<div class="form-group $required">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
				<input $read_only type="email" name="$name" value="$value" placeholder="$placeholder" $attributes />
			</div>
		</div>
EOF;
	}
	
	function textarea($name, $value="", $attributes="", $label="", $placeholder="", $required=FALSE){
		$required = $required? "has-warning" : "";
		$placeholder = empty($placeholder)? $label : $placeholder;
		$read_only = $this->editable? "":"readonly";
		if(!$this->editable && strpos($attributes,"editable")!==FALSE ){
			echo <<<EOF
		<div class='clear'></div>
		<div class="form-group $required">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
            	<div class="container">
					$value
				</div>
			</div>
		</div>
EOF;
		}
		else
			echo <<<EOF
		<div class="form-group $required">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
				<textarea $read_only name="$name" $attributes placeholder="$placeholder" >$value</textarea>
			</div>
		</div>
EOF;
	}
	
	function date($name, $value="", $attributes="", $label="", $placeholder="", $required=FALSE){
		$required = $required? "has-warning" : "";
		$placeholder = empty($placeholder)? $label : $placeholder;
		$read_only = $this->editable? "":"readonly";
		echo <<<EOF
		<div class="form-group $required">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
				<input $read_only type="date" name="$name" value="$value" Placeholder="YYYY-MM-DD" $attributes />
			</div>
		</div>
EOF;
	}
	
	function date_picker($name, $value="", $attributes=""){
		if($this->editable)
			echo <<<EOF
			<script>
			  $(function() {
			    $( "#datepicker" ).datepicker();
			  });
			</script>
		<input type="text" name="$name" value="$value" id="datepicker" $attributes />
EOF;
		else{
			$dateformat = strtotime($value);
			if($dateformat === FALSE || $value == "0000-00-00")
				echo "<span $attributes>Date Not Defined.</span>";
			else
				echo "<span $attributes>".date('M. j, Y',$dateformat)."</span>";
		}
	}
	
	function button($name, $value, $attributes="", $type="submit", $url = ""){
		if($this->editable){
			if(!empty($url)){
				$link = "<a href='$url'>";
				$end_link = "</a>";
			}
		echo <<<EOF
		<div class="form-group">
            <label for="inputEmail" class="col-lg-2 control-label">$label</label>
            <div class="col-lg-10">
				$link<button type="$type" name="$name" $attributes ><strong style="font-size: 2em;">$value</strong></button>$end_link
			</div>
		</div>
EOF;
		}
	}
	
	function select($name, $default="", $value="", $attributes="", $label="", $placeholder="", $required=FALSE){
		$required = $required? "has-warning" : "";
		$option = $optionB = "";
		$placeholder = empty($placeholder)? $label : $placeholder;
		if(!is_array($default))
			$default = explode(",", $default);
		if(is_array($value)){
			foreach($value AS $k => $v){
				$sel = "";
				if(in_array($k, $default))
					$sel = "selected";
				$option .= "<option value='$k' $sel >$v</option>\n";
			}
		}
		else{
			$option = "<option value='$value'>$value</option>";
		}
		if(strpos($attributes, "multiple") !== FALSE)
			$name .= "[]";
		$read_only = $this->editable? "":"readonly";
		echo <<<EOF
			<div class="form-group $required">
            	<label for="select" class="col-lg-2 control-label">$label</label>
                <div class="col-lg-10">
					<select $read_only name="$name" placeholder="$placeholder" $attributes >
					$option
					</select>
				</div>
			</div>
EOF;
	}
	
	function radio($name, $value, $attributes = "", $default = ""){
		$radio = "";
		if(is_array($value)){
			foreach($value AS $k => $v){
				$checked = ($k === $default)? "checked" : "";
				$radio .= "<div $attributes><input type='radio' name='$name' value='$k' $checked>$v</div>";
			}
		}
		else{
			$checked = ($value === $default)? "checked" : "";
			$radio .= "<div $attributes><input type='radio' name='$name' value='$value' $checked>$value</div>";
		}
		if($this->editable){
			echo $radio;
		}
		else{
			echo "<div $attributes>$default</div>";
		}
	}
	
	function google_recaptcha(){
		if($this->editable){
			$recaptcha = recaptcha_get_html(RECAPTCHA_PUBLIC_KEY);
			echo <<<EOF
		 <script type="text/javascript">
		 var RecaptchaOptions = {
		    theme : 'clean',
		    lang : 'en'
		 };
		 </script>
		<div class="form-group has-warning">
            <label for="inputEmail" class="col-lg-2 control-label">Enter Code:</label>
            <div class="col-lg-10">
            	$recaptcha
            </div>
        </div>
EOF;
		}
	}
}
?>