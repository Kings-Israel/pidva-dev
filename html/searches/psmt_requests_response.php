<?php require_once('../../Connections/connect.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($connect,$theValue) : mysqli_escape_string($connect,$theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_getrequest = "-1";
if (isset($_GET['request_id'])) {
  $colname_getrequest = $_GET['request_id'];
}
mysqli_select_db($connect,$database_connect);
$query_getrequest = sprintf("SELECT * FROM pel_psmt_request WHERE request_id = %s", GetSQLValueString($colname_getrequest, "int"));
$getrequest = mysqli_query_ported($query_getrequest, $connect) or die(mysqli_error($connect));
$row_getrequest = mysqli_fetch_assoc($getrequest);
$totalRows_getrequest = mysqli_num_rows($getrequest);

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
			<title>Upload Repsonse- Peleza Admin</title>

		<meta name="description" content="Common form elements and layouts" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.css" />
		<link rel="stylesheet" href="../../assets/css/font-awesome.css" />

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.css" />
		<link rel="stylesheet" href="../../assets/css/chosen.css" />
		<link rel="stylesheet" href="../../assets/css/datepicker.css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.css" />
		<link rel="stylesheet" href="../../assets/css/daterangepicker.css" />
		<link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.css" />
		<link rel="stylesheet" href="../../assets/css/colorpicker.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/css/ace-fonts.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="../../assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="../../assets/css/ace-part2.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="../../assets/css/ace-ie.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="../../assets/js/ace-extra.js"></script>
             <script type="text/javascript" src="xlsx.core.min.js"></script>
        <script type="text/javascript" src="excelplus-2.3.min.js"></script>
        

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="../../assets/js/html5shiv.js"></script>
		<script src="../../assets/js/respond.js"></script>
		<![endif]-->
</head>

	<body class="no-skin">
		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>
			<?php include('../header2.php');?>
		</div>

		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<!-- #section:basics/sidebar -->
			<div id="sidebar" class="sidebar responsive">
			  <script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>
				<?php include('../sidebarmenu2.php');?>
				<!-- #section:basics/sidebar.layout.minimize -->
				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>
				<!-- /section:basics/sidebar.layout.minimize -->
			  <script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
		  </div>

			<!-- /section:basics/sidebar -->
			<div class="main-content">
				<div class="main-content-inner">
					<!-- #section:basics/content.breadcrumbs -->
					<div class="breadcrumbs" id="breadcrumbs">
					  <script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="#">Home</a>
							</li>
							<li>
								<a href="#">PSMT Request</a>
							</li>
							<li class="active">Upload Repsonse</li>
						</ul><!-- /.breadcrumb -->

						<!-- #section:basics/content.searchbox -->
						<div class="nav-search" id="nav-search">
						</div><!-- /.nav-search -->

						<!-- /section:basics/content.searchbox -->
					</div>

					<!-- /section:basics/content.breadcrumbs -->
					<div class="page-content">
				

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-12">
									  	<h3 align="left" class="header smaller lighter blue">PSMT REQUEST REPORT UPLAODER</h3>
										</div>
										<div class="clearfix">
											<div class="row">
													<div class="col-xs-12">
														<!-- PAGE CONTENT BEGINS -->
														<?php
															ini_set('max_execution_time', 10000);

															// this verify is the file csv upload
															if (isset($_POST['request_ref_number']))  {
																if (is_uploaded_file($_FILES['id-input-file-2']['tmp_name'])) {
																	// $STAFF_ID = $_SESSION['MM_USR_STAFF_ID'];  
																	date_default_timezone_set('Africa/Nairobi');
																	$date_insert = date('Y-m-d h:i:s');

																	$uploadedby = $_SESSION['MM_full_names'];
																	$USR_ID = $_SESSION['MM_USR_ID']; 
																	$request_ref_number = $_POST['request_ref_number'];	
																	
																	$request_plan =  $_POST['request_plan'];
																	$bg_dataset_name = $_POST['bg_dataset_name'];
																	$client_id =  $_POST['client_id'];
																	$ID =  $_POST['ID'];
																	$notify_by = $_POST['notify_by'];
																	$notify_date =  $_POST['notify_date'];
																	$status =  $_POST['status'];

																	if($status == '11') {
																		$statuscode = 'FINAL REPORT';
																	}
																	if($status == '33') {
																		$statuscode = 'INTERIM REPORT';
																	}
																	$date_insert2 = date('dmYhis');

																	$file = $_FILES['id-input-file-2']['name'];

																	$ext = strtolower(end(explode('.', $file)));

																	$a = $request_ref_number."_";
																	"Upload: ".$a."_". $_FILES["id-input-file-2"]["name"];
																	$rawname = $_FILES['id-input-file-2']['name'];
																	$file="reportfiles/".$a."_". $_FILES["id-input-file-2"]["name"];

																	// require_once "../../uploads.php";
																	$prefix = "id-input-file-2";
																	$filenameuploaded = uploadFile($prefix,"searches-reportfiles",$a."_".$_FILES[$prefix]["name"]);

																	$query_getclient = "SELECT * FROM pel_client WHERE client_id='$client_id'";
																	$getclient = mysqli_query_ported($query_getclient, $connect) or die(mysqli_error($connect));
																	$row_getclient = mysqli_fetch_assoc($getclient);
																	$totalRows_getclient = mysqli_num_rows($getclient);

																	$clientname =$row_getclient['client_first_name'];
																	$toemail= $row_getclient['client_email_address'];

																	require ("../../PHPMailer/PHPMailer.php");

																	require("../../PHPMailer/SMTP.php");
																	require("../../PHPMailer/Exception.php");

																	//$mail = new PHPMailer;
																	$mail = new PHPMailer\PHPMailer\PHPMailer();

																	$mail->isSMTP();   
																	//$mail->isMail();                          // Set mailer to use SMTP
																	$mail->Host = 'two.deepafrica.com';             // Specify main and backup SMTP servers
																	$mail->SMTPAuth = true;                     // Enable SMTP authentication
																	$mail->Username = 'verify@edcheckafrica.com';          // SMTP username
																	$mail->Password = 'vkr67XpjsBnVkKK5'; // SMTP password
																	$mail->SMTPSecure = 'ssl';                  // Enable TLS encryption, `ssl` also accepted
																	$mail->Port = 465;                          // TCP port to connect to

																	$mail->setFrom('verify@edcheckafrica.com', 'Backgorund Checks>> Peleza International');

																	$mail->addAddress($toemail);
																	//$mail->addAddress('omintolbert@gmail.com');   // Add a recipient
																	//$mail->addCC('mwendemarita@gmail.com');
																	//$mail->addBCC('omintolbert@gmail.com');

																	$mail->isHTML(true);  // Set email format to HTML

																	$bodyContent = '<p><img src="https://admin.pidva.africa/assets/images/PelezaLogo.png" width="166" height="60" /></p>
																									<p><strong>Hi '.$clientname.',</strong></p>
																									<p>The background search report for  '.$bg_dataset_name.' has been uploaded kindly login to view it.</p>
																									<p><strong>STATUS :  '.$statuscode.'</strong></p>
																									<p><strong><a href="https://psmt.pidva.africa/">LOGIN</a></strong></p>
																									<p>  - The Peleza Team<br />
																										Support: +254 796 111 020 or +254  Email:&nbsp;<a href="mailto:verify@peleza.com">verify@peleza.com</a>&nbsp;<br />
																										® Peleza Int, 2018. All rights reserved.
																									</p>';
																	//$bodyContent .= '<p>This is the HTML email sent from localhost using PHP script by <b>CodexWorld</b></p>';

																	$mail->Subject = 'Confidential Background Check Report';
																	$mail->Body    = $bodyContent;

																	if(!$mail->send()) {
																		echo 'Message could not be sent.';
																		echo 'Mailer Error: ' . $mail->ErrorInfo;
																	} else {
																		echo 'Message has been sent'; 

																		$updateSQL = sprintf("UPDATE pel_psmt_request SET status=%s, notify_by=%s, notify_date=%s, report_file=%s  WHERE request_id=%s",
																								GetSQLValueString($_POST['status'], "text"),
																								GetSQLValueString($_POST['notify_by'], "text"),
																								GetSQLValueString($_POST['notify_date'], "text"),
																								GetSQLValueString($filenameuploaded, "text"),
																			GetSQLValueString($_POST['ID'], "int"));

																		mysqli_select_db($connect,$database_connect);
																		$Result1 = mysqli_query_ported($updateSQL, $connect) or die(mysqli_error($connect));
																	
																		$updateGoTo = "psmt_requests.php";
																		?>
																			<div class="alert alert-success"> <h4> REPORT UPLOADED SUCCESFULLY </h4> <p><a href="psmt_requests.php">Go PSMT Requests</a></p></div>
																		<?php
																	}
																}			
															} else {
																$editFormAction = $_SERVER['PHP_SELF'];
																if (isset($_SERVER['QUERY_STRING'])) { $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }
																?>
																	<div> 
																		<form class="form-horizontal" ENCTYPE='multipart/form-data' action='<?php echo $_SERVER["PHP_SELF"];?>' method='post'>
																			<!-- #section:elements.form -->
																			<div class="space-4"></div> 
																			<hr color="#ff0000" size="2px"> 
																			<div class="space-4"></div>
																			<div class="form-group">	
																				<input type="hidden" id="ID" name="ID"  class="col-xs-10 col-sm-5" value="<?php echo $row_getrequest['request_id']; ?>"/>
																				<input type="hidden" id="client_id" name="client_id"  class="col-xs-10 col-sm-5" value="<?php echo $row_getrequest['client_id']; ?>"/>
																				<input type="hidden" id="notify_by" name="notify_by"  class="col-xs-10 col-sm-5" value="<?php echo $_SESSION['MM_full_names']; ?>"/>
																				<input type="hidden" id="notify_date" name="notify_date"  class="col-xs-10 col-sm-5" value="<?php echo date('Y-m-d h:i:s'); ?>"/>
																				<!--<input type="hidden" id="status" name="status"  class="col-xs-10 col-sm-5" value="11"/>-->
																				<input type="hidden" id="bg_dataset_name" name="bg_dataset_name"  class="col-xs-10 col-sm-5" value="<?php echo $row_getrequest['bg_dataset_name']; ?>"/>
																				<input type="hidden" id="request_plan" name="request_plan"  class="col-xs-10 col-sm-5" value="<?php echo $row_getrequest['request_plan']; ?>"/>
																				<input type="hidden" id="request_ref_number" name="request_ref_number"  class="col-xs-10 col-sm-5" value="<?php echo $row_getrequest['request_ref_number']; ?>"/>
																				<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">Upload Data File</label>
																				<div class="col-sm-6">
																					<span id="sprytextfield2">
																						<input class="col-xs-10 col-sm-5" id="id-input-file-2" name="id-input-file-2" type="file"/>
																						<span class="textfieldRequiredMsg">*</span>
																					</span>
																				</div>
																			</div>
																			<div class="form-group">	
																				<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">Report Type</label>
																				<div class="col-sm-6">
																					<span id="spryselect7">
																						<label>
																							<select class="select form-control" name="status" id="status" required>
																								<option value="">Choose Report Type</option>
																								<option value="33">Interim</option>
																								<option value="11">Final</option>
																							</select>
																						</label>
																						<span class="selectInvalidMsg">*.</span>									  
																					</span>
																				</div>
																			</div>
																			<div class="space-4"></div>
																			<div class="clearfix form-actions">
																				<div class="col-md-offset-3 col-md-9">
																					<button onClick="submit" type="submit" value="submit" class="btn btn-info">
																						<i class="ace-icon fa fa-check bigger-110"></i>
																						Submit											
																					</button>
																					<button class="btn" type="reset">
																						<i class="ace-icon fa fa-undo bigger-110"></i>
																						Reset											
																					</button>
																				</div>
																			</div>   
																		</form>
																</div>    
															</div>
														</div>		
														<?php
													}
												?>                 
									</div>
								</div>
							</div><!-- /.col -->
					  </div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<div class="footer">
				<div class="footer-inner">
					<!-- #section:basics/footer -->
					<div class="footer-content">
						<span class="bigger-120">
							<span class="blue bolder">Peleza</span>
							Admin &copy; 2018						
						</span>
						&nbsp;&nbsp;											
					</div>
					<!-- /section:basics/footer -->
				</div>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i></a>
		</div><!-- /.main-container -->


		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='../../assets/js/jquery.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
		window.jQuery || document.write("<script src='../../assets/js/jquery1x.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='../../assets/js/jquery.mobile.custom.js'>"+"<"+"/script>");
		</script>
		<script src="../../assets/js/bootstrap.js"></script>

		<!-- page specific plugin scripts -->
        
		<!-- page specific plugin scripts -->
		<script src="../../assets/js/dataTables/jquery.dataTables.js"></script>
		<script src="../../assets/js/dataTables/jquery.dataTables.bootstrap.js"></script>
		<script src="../../assets/js/dataTables/extensions/TableTools/js/dataTables.tableTools.js"></script>
		<script src="../../assets/js/dataTables/extensions/ColVis/js/dataTables.colVis.js"></script>

		<!--[if lte IE 8]>
		  <script src="../../assets/js/excanvas.js"></script>
		<![endif]-->
		<script src="../../assets/js/jquery-ui.custom.js"></script>
		<script src="../../assets/js/jquery.ui.touch-punch.js"></script>
		<script src="../../assets/js/chosen.jquery.js"></script>
		<script src="../../assets/js/fuelux/fuelux.spinner.js"></script>
		<script src="../../assets/js/date-time/bootstrap-datepicker.js"></script>
		<script src="../../assets/js/date-time/bootstrap-timepicker.js"></script>
		<script src="../../assets/js/date-time/moment.js"></script>
		<script src="../../assets/js/date-time/daterangepicker.js"></script>
		<script src="../../assets/js/date-time/bootstrap-datetimepicker.js"></script>
		<script src="../../assets/js/bootstrap-colorpicker.js"></script>
		<script src="../../assets/js/jquery.knob.js"></script>
		<script src="../../assets/js/jquery.autosize.js"></script>
		<script src="../../assets/js/jquery.inputlimiter.1.3.1.js"></script>
		<script src="../../assets/js/jquery.maskedinput.js"></script>
		<script src="../../assets/js/bootstrap-tag.js"></script>

		<!-- ace scripts -->
		<script src="../../assets/js/ace/elements.scroller.js"></script>
		<script src="../../assets/js/ace/elements.colorpicker.js"></script>
		<script src="../../assets/js/ace/elements.fileinput.js"></script>
		<script src="../../assets/js/ace/elements.typeahead.js"></script>
		<script src="../../assets/js/ace/elements.wysiwyg.js"></script>
		<script src="../../assets/js/ace/elements.spinner.js"></script>
		<script src="../../assets/js/ace/elements.treeview.js"></script>
		<script src="../../assets/js/ace/elements.wizard.js"></script>
		<script src="../../assets/js/ace/elements.aside.js"></script>
		<script src="../../assets/js/ace/ace.js"></script>
		<script src="../../assets/js/ace/ace.ajax-content.js"></script>
		<script src="../../assets/js/ace/ace.touch-drag.js"></script>
		<script src="../../assets/js/ace/ace.sidebar.js"></script>
		<script src="../../assets/js/ace/ace.sidebar-scroll-1.js"></script>
		<script src="../../assets/js/ace/ace.submenu-hover.js"></script>
		<script src="../../assets/js/ace/ace.widget-box.js"></script>
		<script src="../../assets/js/ace/ace.settings.js"></script>
		<script src="../../assets/js/ace/ace.settings-rtl.js"></script>
		<script src="../../assets/js/ace/ace.settings-skin.js"></script>
		<script src="../../assets/js/ace/ace.widget-on-reload.js"></script>
		<script src="../../assets/js/ace/ace.searchbox-autocomplete.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
				$('#id-disable-check').on('click', function() {
					var inp = $('#form-input-readonly').get(0);
					if(inp.hasAttribute('disabled')) {
						inp.setAttribute('readonly' , 'true');
						inp.removeAttribute('disabled');
						inp.value="This text field is readonly!";
					}
					else {
						inp.setAttribute('disabled' , 'disabled');
						inp.removeAttribute('readonly');
						inp.value="This text field is disabled!";
					}
				});
			
			
				if(!ace.vars['touch']) {
					$('.chosen-select').chosen({allow_single_deselect:true}); 
					//resize the chosen on window resize
			
					$(window)
					.off('resize.chosen')
					.on('resize.chosen', function() {
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					}).trigger('resize.chosen');
					//resize chosen on sidebar collapse/expand
					$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
						if(event_name != 'sidebar_collapsed') return;
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					});
			
			
					$('#chosen-multiple-style .btn').on('click', function(e){
						var target = $(this).find('input[type=radio]');
						var which = parseInt(target.val());
						if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
						 else $('#form-field-select-4').removeClass('tag-input-style');
					});
				}
			
			
				$('[data-rel=tooltip]').tooltip({container:'body'});
				$('[data-rel=popover]').popover({container:'body'});
				
				$('textarea[class*=autosize]').autosize({append: "\n"});
				$('textarea.limited').inputlimiter({
					remText: '%n character%s remaining...',
					limitText: 'max allowed : %n.'
				});
			
				$.mask.definitions['~']='[+-]';
				$('.input-mask-date').mask('99/99/9999');
				$('.input-mask-phone').mask('(999) 999-9999');
				$('.input-mask-eyescript').mask('~9.99 ~9.99 999');
				$(".input-mask-product").mask("a*-999-a999",{placeholder:" ",completed:function(){alert("You typed the following: "+this.val());}});
			
			
			
				$( "#input-size-slider" ).css('width','200px').slider({
					value:1,
					range: "min",
					min: 1,
					max: 8,
					step: 1,
					slide: function( event, ui ) {
						var sizing = ['', 'input-sm', 'input-lg', 'input-mini', 'input-small', 'input-medium', 'input-large', 'input-xlarge', 'input-xxlarge'];
						var val = parseInt(ui.value);
						$('#form-field-4').attr('class', sizing[val]).val('.'+sizing[val]);
					}
				});
			
				$( "#input-span-slider" ).slider({
					value:1,
					range: "min",
					min: 1,
					max: 12,
					step: 1,
					slide: function( event, ui ) {
						var val = parseInt(ui.value);
						$('#form-field-5').attr('class', 'col-xs-'+val).val('.col-xs-'+val);
					}
				});
			
			
				
				//"jQuery UI Slider"
				//range slider tooltip example
				$( "#slider-range" ).css('height','200px').slider({
					orientation: "vertical",
					range: true,
					min: 0,
					max: 100,
					values: [ 17, 67 ],
					slide: function( event, ui ) {
						var val = ui.values[$(ui.handle).index()-1] + "";
			
						if( !ui.handle.firstChild ) {
							$("<div class='tooltip right in' style='display:none;left:16px;top:-6px;'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>")
							.prependTo(ui.handle);
						}
						$(ui.handle.firstChild).show().children().eq(1).text(val);
					}
				}).find('span.ui-slider-handle').on('blur', function(){
					$(this.firstChild).hide();
				});
				
				
				$( "#slider-range-max" ).slider({
					range: "max",
					min: 1,
					max: 10,
					value: 2
				});
				
				$( "#slider-eq > span" ).css({width:'90%', 'float':'left', margin:'15px'}).each(function() {
					// read initial values from markup and remove that
					var value = parseInt( $( this ).text(), 10 );
					$( this ).empty().slider({
						value: value,
						range: "min",
						animate: true
						
					});
				});
				
				$("#slider-eq > span.ui-slider-purple").slider('disable');//disable third item
		
				$('#id-input-file-1 , #id-input-file-2').ace_file_input({
					no_file:'No File ...',
					btn_choose:'Choose',
					btn_change:'Change',
					droppable:false,
					onchange:null,
					thumbnail:false, //| true | large
					whitelist:'csv|xls|xlsx',
					blacklist:'exe|php|mp4'
					//onchange:''
					//
				});
				//pre-show a file name, for example a previously selected file
				//$('#id-input-file-1').ace_file_input('show_file_list', ['myfile.txt'])
			
			
				$('#id-input-file-3').ace_file_input({
					style:'well',
					btn_choose:'Drop files here or click to choose',
					btn_change:null,
					no_icon:'ace-icon fa fa-cloud-upload',
					droppable:true,
					thumbnail:'small'//large | fit
					//,icon_remove:null//set null, to hide remove/reset button
					/**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
					/**,before_remove : function() {
						return true;
					}*/
					,
					preview_error : function(filename, error_code) {
						//name of the file that failed
						//error_code values
						//1 = 'FILE_LOAD_FAILED',
						//2 = 'IMAGE_LOAD_FAILED',
						//3 = 'THUMBNAIL_FAILED'
						//alert(error_code);
					}
			
				}).on('change', function(){
					//console.log($(this).data('ace_input_files'));
					//console.log($(this).data('ace_input_method'));
				});
				
				
				//$('#id-input-file-3')
				//.ace_file_input('show_file_list', [
					//{type: 'image', name: 'name of image', path: 'http://path/to/image/for/preview'},
					//{type: 'file', name: 'hello.txt'}
				//]);
			
				
				
			
				//dynamically change allowed formats by changing allowExt && allowMime function
				$('#id-file-format').removeAttr('checked').on('change', function() {
					var whitelist_ext, whitelist_mime;
					var btn_choose
					var no_icon
					if(this.checked) {
						btn_choose = "Drop images here or click to choose";
						no_icon = "ace-icon fa fa-picture-o";
			
						whitelist_ext = ["jpeg", "jpg", "png", "gif" , "bmp"];
						whitelist_mime = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/bmp"];
					}
					else {
						btn_choose = "Drop files here or click to choose";
						no_icon = "ace-icon fa fa-cloud-upload";
						
						whitelist_ext = null;//all extensions are acceptable
						whitelist_mime = null;//all mimes are acceptable
					}
					var file_input = $('#id-input-file-3');
					file_input
					.ace_file_input('update_settings',
					{
						'btn_choose': btn_choose,
						'no_icon': no_icon,
						'allowExt': whitelist_ext,
						'allowMime': whitelist_mime
					})
					file_input.ace_file_input('reset_input');
					
					file_input
					.off('file.error.ace')
					.on('file.error.ace', function(e, info) {
						//console.log(info.file_count);//number of selected files
						//console.log(info.invalid_count);//number of invalid files
						//console.log(info.error_list);//a list of errors in the following format
						
						//info.error_count['ext']
						//info.error_count['mime']
						//info.error_count['size']
						
						//info.error_list['ext']  = [list of file names with invalid extension]
						//info.error_list['mime'] = [list of file names with invalid mimetype]
						//info.error_list['size'] = [list of file names with invalid size]
						
						
						/**
						if( !info.dropped ) {
							//perhapse reset file field if files have been selected, and there are invalid files among them
							//when files are dropped, only valid files will be added to our file array
							e.preventDefault();//it will rest input
						}
						*/
						
						
						//if files have been selected (not dropped), you can choose to reset input
						//because browser keeps all selected files anyway and this cannot be changed
						//we can only reset file field to become empty again
						//on any case you still should check files with your server side script
						//because any arbitrary file can be uploaded by user and it's not safe to rely on browser-side measures
					});
				
				});
			
				$('#spinner1').ace_spinner({value:0,min:0,max:200,step:10, btn_up_class:'btn-info' , btn_down_class:'btn-info'})
				.closest('.ace-spinner')
				.on('changed.fu.spinbox', function(){
					//alert($('#spinner1').val())
				}); 
				$('#spinner2').ace_spinner({value:0,min:0,max:10000,step:100, touch_spinner: true, icon_up:'ace-icon fa fa-caret-up bigger-110', icon_down:'ace-icon fa fa-caret-down bigger-110'});
				$('#spinner3').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
				$('#spinner4').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'ace-icon fa fa-plus', icon_down:'ace-icon fa fa-minus', btn_up_class:'btn-purple' , btn_down_class:'btn-purple'});
			
				//$('#spinner1').ace_spinner('disable').ace_spinner('value', 11);
				//or
				//$('#spinner1').closest('.ace-spinner').spinner('disable').spinner('enable').spinner('value', 11);//disable, enable or change value
				//$('#spinner1').closest('.ace-spinner').spinner('value', 0);//reset to 0
			
			
				//datepicker plugin
				//link
				$('.date-picker').datepicker({
					autoclose: true,
					      format:"d MM yyyy",
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			
				//or change it into a date range picker
				$('.input-daterange').datepicker({autoclose:true});
			
			
				//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
				$('input[name=date-range-picker]').daterangepicker({
					'applyClass' : 'btn-sm btn-success',
					'cancelClass' : 'btn-sm btn-default',
					locale: {
						applyLabel: 'Apply',
						cancelLabel: 'Cancel',
					}
				})
				.prev().on(ace.click_event, function(){
					$(this).next().focus();
				});
			
			
				$('#timepicker1').timepicker({
					minuteStep: 1,
					showSeconds: true,
					showMeridian: false
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
				$('#date-timepicker1').datetimepicker().next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
			
				$('#colorpicker1').colorpicker();
			
				$('#simple-colorpicker-1').ace_colorpicker();
				//$('#simple-colorpicker-1').ace_colorpicker('pick', 2);//select 2nd color
				//$('#simple-colorpicker-1').ace_colorpicker('pick', '#fbe983');//select #fbe983 color
				//var picker = $('#simple-colorpicker-1').data('ace_colorpicker')
				//picker.pick('red', true);//insert the color if it doesn't exist
			
			
				$(".knob").knob();
				
				
				var tag_input = $('#form-field-tags');
				try{
					tag_input.tag(
					  {
						placeholder:tag_input.attr('placeholder'),
						//enable typeahead by specifying the source array
						source: ace.vars['US_STATES'],//defined in ace.js >> ace.enable_search_ahead
						/**
						//or fetch data from database, fetch those that match "query"
						source: function(query, process) {
						  $.ajax({url: 'remote_source.php?q='+encodeURIComponent(query)})
						  .done(function(result_items){
							process(result_items);
						  });
						}
						*/
					  }
					)
			
					//programmatically add a new
					var $tag_obj = $('#form-field-tags').data('tag');
					$tag_obj.add('Programmatically Added');
				}
				catch(e) {
					//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
					tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
					//$('#form-field-tags').autosize({append: "\n"});
				}
				
				
				/////////
				$('#modal-form input[type=file]').ace_file_input({
					style:'well',
					btn_choose:'Drop files here or click to choose',
					btn_change:null,
					no_icon:'ace-icon fa fa-cloud-upload',
					droppable:true,
					thumbnail:'large'
				})
				
				//chosen plugin inside a modal will have a zero width because the select element is originally hidden
				//and its width cannot be determined.
				//so we set the width after modal is show
				$('#modal-form').on('shown.bs.modal', function () {
					if(!ace.vars['touch']) {
						$(this).find('.chosen-container').each(function(){
							$(this).find('a:first-child').css('width' , '210px');
							$(this).find('.chosen-drop').css('width' , '210px');
							$(this).find('.chosen-search input').css('width' , '200px');
						});
					}
				})
				/**
				//or you can activate the chosen plugin after modal is shown
				//this way select element becomes visible with dimensions and chosen works as expected
				$('#modal-form').on('shown', function () {
					$(this).find('.modal-chosen').chosen();
				})
				*/
			
				
				
				$(document).one('ajaxloadstart.page', function(e) {
					$('textarea[class*=autosize]').trigger('autosize.destroy');
					$('.limiterBox,.autosizejs').remove();
					$('.daterangepicker.dropdown-menu,.colorpicker.dropdown-menu,.bootstrap-datetimepicker-widget.dropdown-menu').remove();
				});
			
			});
		</script>
   <script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2",  "none", {validateOn:["change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"000", validateOn:["change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"000", validateOn:["change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"000", validateOn:["change"]});
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"000", validateOn:["change"]});
var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"000", validateOn:["change"]});
var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {invalidValue:"000", validateOn:["change"]});
var spryselect7 = new Spry.Widget.ValidationSelect("spryselect7", {validateOn:["change"], invalidValue:"000", isRequired:false});
//-->
</script>
</body>
</html>
<?php
mysqli_free_result($getrequest);

?>
