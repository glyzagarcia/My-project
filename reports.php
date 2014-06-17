<?php
require 'config.php';

if(!isset($_SESSION['u']) || !in_array($_SESSION['u'], $authorized)){
	header("Location: login.php");
	//header("Location: login.php?logout=1");
	exit();
}

if(!is_admin()){
	header("Location: applicants.php");
	exit();
}
$year = $_GET['year']=='All'?"":$_GET['year'];
$month = $_GET['month']=='All'?"":$_GET['month'];
$position = $_GET['position']=="All"?"":$_GET['position'];
$status = $_GET['status']=="All"?"":$_GET['status'];
$filter .= !empty($year)?" AND a.date_created LIKE '$year%' ":"";
$filter .= !empty($month)?" AND a.date_created LIKE '%-$month%' ":"";
$filter .= !empty($position)?" AND a.position = $position ":"";
$filter .= !empty($status)?" AND a.status = $status ":"";
$result = $db->selectQuery("applicants a LEFT JOIN positions p ON p.id = a.position LEFT JOIN applicant_status a_s ON a_s.id=a.status","CONCAT(fname,' ',mname,' ',lname,' ',suffix) AS name,bdate,address,mnumber,email,p.title AS position,source,expected_salary,last_employer,employment_period,a.date_created,a_s.status","1 $filter");
require "includes/header.php";
?>
<?php 
	$months = array(
		'All' => "All",
		'01' => "January",
		'02' => "February",
		'03' => "March",
		'04' => "April",
		'05' => "May",
		'06' => "June",
		'07' => "July",
		'08' => "August",
		'09' => "September",
		'10' => "October",
		'11' => "November",
		'12' => "December"
	);
	if(empty($month)){
		//$month = date('m');
		$month = '00';
	}
	$cur_year = date('Y');
	$years = array("All"=>"All",$cur_year => $cur_year);
	for($i=1;$i<=10;$i++){
		$years[($cur_year-$i)] = $cur_year-$i;
	}
    $form->formStart("","GET",'id="position" class="bs-example form-horizontal"');
	$form->select("year",$year,$years,'class="form-control" style="width: 200px;"',"Select Year");
	$form->select("month",$month,$months,'class="form-control" style="width: 200px;"',"Select Month");
	
	$pos = $db->selectQuery("positions", "id,title", "1 ORDER BY title ASC");
    	$positions = array('All' => "All");
        if(is_array($pos)){
        	foreach($pos AS $v){
            	$positions[$v['id']] = $v['title'];	
            }
       	}
    $pos = $db->selectQuery("applicant_status", "id,status", "1 ORDER BY status ASC");
    	$stats = array('All' => "All");
        if(is_array($pos)){
        	foreach($pos AS $v){
            	$stats[$v['id']] = $v['status'];	
            }
       	}
	$form->select("position",$position,$positions,'class="form-control" style="width: 200px;"',"Select Position");
	$form->select("status",$status,$stats,'class="form-control" style="width: 200px;"',"Select Status");
	
	$form->button("report","Generate Reports","class='btn btn-success'");
	$form->formEnd();
?>
<?php if(isset($_GET['report'])):?>
<hr/>
<style type="text/css" title="currentStyle">
	@import "css/demo_page.css";
	@import "css/demo_table.css";
	@import "TableTools/media/css/TableTools.css";
</style>
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="TableTools/media/js/TableTools.js"></script>
<script type="text/javascript" language="javascript" src="TableTools/media/ZeroClipboard/ZeroClipboard.js"></script>
<script>
$(document).ready( function () {
	TableToolsInit.sPrintMessage = "ITEMS: 3";
    $('#reports').dataTable( {
    	"aaSorting": [[ 10, "desc" ]],
        "sDom": 'T<"clear">lfrtip',
        "oTableTools": {
            "sSwfPath": "/swf/copy_csv_xls_pdf.swf"
        }
    } );
} );
</script>
<h3><strong>Generated Reports:</strong></h3>
<table id='reports' style='width:100%'>
	<thead>
	<tr>
		<th>Name</th>
		<th>Birth Date</th>
		<th>Address</th>
		<th>Mobile Number</th>
		<th>Email Address</th>
		<th>Position Applied</th>
		<th>Status</th>
		<th>Date Applied</th>
		<th>Source</th>
		<th>Expected Salary</th>
		<th>Last Employer</th>
		<th>Employment_period</th>
	</tr>
	</thead>
	<tbody>
	<?php 
		if(is_array($result)):
			foreach($result AS $k=>$v):
	?>
	<tr>
		<td><?php echo $v['name']?></td>
		<td><?php echo $v['bdate']?></td>
		<td><?php echo $v['address']?></td>
		<td><?php echo $v['mnumber']?></td>
		<td><?php echo $v['email']?></td>
		<td><?php echo $v['position']?></td>
		<td><?php echo $v['status']?></td>
		<td><?php echo $v['date_created']?></td>
		<td><?php echo $v['source']?></td>
		<td><?php echo $v['expected_salary']?></td>
		<td><?php echo $v['last_employer']?></td>
		<td><?php echo $v['employment_period']?></td>
	</tr>
		<?php endforeach;?>
	<?php endif;?>
	</tbody>
</table>
<?php endif;?>
<?php 
require "includes/footer.php";
?>