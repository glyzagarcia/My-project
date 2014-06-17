<?php
require 'config.php';

if(!isset($_SESSION['u']) || !in_array($_SESSION['u'], $authorized)){
	header("Location: login.php");
	//header("Location: login.php?logout=1");
	exit();
}

require 'includes/header.php';

$position = empty($_GET['position'])?"All":$_GET['position'];
$status = empty($_GET['status'])?"All":$_GET['status'];

$filter = $position=="All"?"1":"a.position=$position";
$filter .= $status=="All"?"":" AND a.status=$status";
$result = $db->selectQuery("applicants a", "a.id,a.lname,a.fname,a.mname,a.email,p.title AS position,a.date_created,a_s.status,a.uploads,a.timestamp,a.text_resume",$filter,"LEFT JOIN positions p ON a.position=p.id LEFT JOIN applicant_status a_s ON a_s.id = a.status");

?>
<?php 
	$pos = $db->selectQuery("positions", "id,title", "1 ORDER BY title ASC");
    	$positions = array('All' => "View All");
        if(is_array($pos)){
        	foreach($pos AS $v){
            	$positions[$v['id']] = $v['title'];	
            }
       	}
    $pos = $db->selectQuery("applicant_status", "id,status", "1 ORDER BY status ASC");
    	$stats = array('All' => "View All");
        if(is_array($pos)){
        	foreach($pos AS $v){
            	$stats[$v['id']] = $v['status'];	
            }
       	}
    $form->formStart("","GET",'id="filter" class="bs-example form-horizontal"');
	$form->select("position",$position,$positions,'class="form-control" style="width: 200px;"',"Select Position");
	$form->select("status",$status,$stats,'class="form-control" style="width: 200px;"',"Select Status");
	$form->formEnd();
?>
<div id='animate_loading'>
	<?php if(is_admin()):?>
	<a style='margin-left: 260px;' class='btn btn-primary' href='reports.php?position=<?php echo $_GET['position']?>&status=<?php echo $_GET['status']?>&report=1'>Generate reports &raquo;</a>
	<?php endif;?>
</div>
<hr/>
<script>
	<?php $loader = get_ajax_loader("#animate_loading","");?>
	$("select[name='position']").change(function(){
		<?php echo $loader;?>
		$('#filter').trigger('submit');
	});
	$("select[name='status']").change(function(){
		<?php echo $loader;?>
		$('#filter').trigger('submit');
	});
		
</script>
<style>
.hide_details { display:none; }
</style>
<link href="css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
	var oTable;
	oTable = $('#applicants').dataTable( {
		"aaSorting": [[4,"desc"],[5,"desc"],[0,"asc"]],
		"sDom": 'RC<"clear">lfrtip',
		"oLanguage": {
			"sSearch": "Search all columns:"
		},
		"bSortCellsTop": true
	} );
	
	/// Add the events etc before DataTables hides a column 
	$("thead input").keyup( function () {
		// Filter on the column (the index) of this element
		oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex( 
			oTable.fnSettings(), $("thead input").index(this) ) );
	} );
	
	// Support functions to provide a little bit of 'user friendlyness' to the textboxes
	$("thead input").each( function (i) {
		this.initVal = this.value;
	} );
	
	$("thead input").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	
	$("thead input").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = this.initVal;
		}
	} );
	
	
	
	$("#sTags").keyup( function () {
		// Filter on the column (the index) of this element
		oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex( 
			oTable.fnSettings(), 8 ) );	
			$('div').removeClass('hide_details');
	} );
	$("#sTags").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	$("#sTags").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = this.initVal;			
		}
	} );
	$("#sTags").each( function (i) {
		this.initVal = this.value;
	} );
	
	
} );


</script>
<div style='text-align: right; width: 100%;'><input type="checkbox" name="advance_search" checked/> Advance Search</div>
<div style='text-align: right; width: 100%; font-size:12px; padding-bottom:5px'>Search by Tags: <input type="text" name="sTags" id="sTags"/></div>
<table id="applicants" style="width: 100%;">
	<thead>
	<tr>
		<th>Name</th>
		<th>Email</th>
		<th>Position Applied</th>
		<th>Status</th>
		<th>Last Update</th>
		<th>Date Applied</th>
		<th>Applicant<br/>Attachment</th>
		<th>HR<br/>Attachment</th>
		<th>Tags</th>
	</tr>
	<tr id='advance_search'>
		<td><input style="width:100%" type="text" placeholder="Search Name" class='search_init'/></td>
		<td><input style="width:100%;" type="text" placeholder="Search Email" class='search_init'/></td>
		<td><input style="width:100%;" type="text" placeholder="Search Position Applied" class='search_init'/></td>
		<td><input style="width:100%;" type="text" placeholder="Search Status" class='search_init'/></td>
		<td><input style="width:100%;" type="text" placeholder="Search Last Update" class='search_init'/></td>
		<td><input style="width:100%;" type="text" placeholder="Search Date Applied" class='search_init'/></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td style="width:80px;">&nbsp;</td>		
	</tr>	
	</thead>
	<tbody>
	<?php $ctr=0; foreach($result AS $k => $v):?>
	<tr>
		<td><?php echo "<a href='view_info.php?id=".$v['id']."'>".$v['fname']." ".$v['mname']." ".$v['lname']." ".$v['suffix']."</a>";?></td>
		<td><?php echo $v['email'];?></td>
		<td><?php echo $v['position'];?></td>
		<td><?php echo $v['status'];?></td>
		<td><?php if($v['timestamp'] != $v['date_created']) echo $v['timestamp']; else echo "";?></td>
		<td><?php echo $v['date_created'];?></td>		
		<td>
			<?php echo get_uploaded_file_icon("uploads/resumes/{$v['uploads']}/");?>
		</td>
		<td>
			<a href="view_info.php?id=<?php echo $v['id'];?>#attach_file"><img src='img/attach_file.png' /></a>
		</td>
		<td>
			<a href="javascript:void(0)" onclick="open_tags('<?php echo $v['id'];?>')">Show details</a>
			<div style="display:none;" id="text_<?php echo $v['id'];?>"><?php echo strip_tags( $v['text_resume'] );?></div>
		</td>		
	</tr>
	<?php $ctr++; endforeach;?>
	</tbody>
</table>
<div class="clear"></div>
<script>
	function open_tags( id ){
		var s = '';
		if( $('#sTags').val() != '' )
			s = '&tag='+$('#sTags').val();
		window.open('view_info.php?id='+ id + s+'#tags');
	}

	$('input[name="advance_search"]').change(function(){
		if($(this).is(":checked"))
			$("#advance_search").css("visibility", "visible");
		else{
			$(".search_init").val('');
			$("#advance_search").css("visibility", "hidden");
		}
	});
</script>
<?php 
require 'includes/footer.php';
?>