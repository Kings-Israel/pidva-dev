<?php require_once('../../Connections/connect.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($connect, $theValue) : mysqli_escape_string($connect, $theValue);

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

$maxRows_get5topinstitutions = 5;
$pageNum_get5topinstitutions = 0;
if (isset($_GET['pageNum_get5topinstitutions'])) {
  $pageNum_get5topinstitutions = $_GET['pageNum_get5topinstitutions'];
}
$startRow_get5topinstitutions = $pageNum_get5topinstitutions * $maxRows_get5topinstitutions;

mysqli_select_db($connect,$database_connect);
$query_get5topinstitutions = "SELECT COUNT(pel_searches.institution_id) as COUNT_INSTITUTION, SUM(pel_searches.search_credit_charged) as TOTAL_PER, pel_searches.institution_name FROM pel_searches  GROUP BY pel_searches.institution_id ORDER BY COUNT_INSTITUTION DESC";
$query_limit_get5topinstitutions = sprintf("%s LIMIT %d, %d", $query_get5topinstitutions, $startRow_get5topinstitutions, $maxRows_get5topinstitutions);
$get5topinstitutions = mysqli_query_ported($query_limit_get5topinstitutions, $connect) or die(mysqli_error($connect));
$row_get5topinstitutions = mysqli_fetch_assoc($get5topinstitutions);

if (isset($_GET['totalRows_get5topinstitutions'])) {
  $totalRows_get5topinstitutions = $_GET['totalRows_get5topinstitutions'];
} else {
  $all_get5topinstitutions = mysqli_query_ported($query_get5topinstitutions);
  $totalRows_get5topinstitutions = mysqli_num_rows($all_get5topinstitutions);
}
$totalPages_get5topinstitutions = ceil($totalRows_get5topinstitutions/$maxRows_get5topinstitutions)-1;

$maxRows_get5topclients = 5;
$pageNum_get5topclients = 0;
if (isset($_GET['pageNum_get5topclients'])) {
  $pageNum_get5topclients = $_GET['pageNum_get5topclients'];
}
$startRow_get5topclients = $pageNum_get5topclients * $maxRows_get5topclients;

mysqli_select_db($connect,$database_connect);
$query_get5topclients = "SELECT COUNT(pel_searches.client_id) AS COUNT_CLIENT, SUM(pel_searches.search_credit_charged) AS TOTAL_PER, pel_searches.client_name FROM pel_searches  GROUP BY pel_searches.client_id ORDER BY TOTAL_PER DESC";
$query_limit_get5topclients = sprintf("%s LIMIT %d, %d", $query_get5topclients, $startRow_get5topclients, $maxRows_get5topclients);
$get5topclients = mysqli_query_ported($query_limit_get5topclients, $connect) or die(mysqli_error($connect));
$row_get5topclients = mysqli_fetch_assoc($get5topclients);

if (isset($_GET['totalRows_get5topclients'])) {
  $totalRows_get5topclients = $_GET['totalRows_get5topclients'];
} else {
  $all_get5topclients = mysqli_query_ported($query_get5topclients);
  $totalRows_get5topclients = mysqli_num_rows($all_get5topclients);
}
$totalPages_get5topclients = ceil($totalRows_get5topclients/$maxRows_get5topclients)-1;

mysqli_select_db($connect,$database_connect);
$query_countclients = "SELECT COUNT(pel_client.client_id) COUNT_CLIENTS FROM pel_client ";
$countclients = mysqli_query_ported($query_countclients, $connect) or die(mysqli_error($connect));
$row_countclients = mysqli_fetch_assoc($countclients);
$totalRows_countclients = mysqli_num_rows($countclients);

mysqli_select_db($connect,$database_connect);
$query_getinstitutions_count = "SELECT COUNT(pel_edu_institution.inst_id) AS COUNT_INSTITUTIONS FROM pel_edu_institution";
$getinstitutions_count = mysqli_query_ported($query_getinstitutions_count, $connect) or die(mysqli_error($connect));
$row_getinstitutions_count = mysqli_fetch_assoc($getinstitutions_count);
$totalRows_getinstitutions_count = mysqli_num_rows($getinstitutions_count);

mysqli_select_db($connect,$database_connect);
$query_getinstitutions_count2 = "SELECT
COUNT(pel_searches.institution_name) AS COUNT_INSTITUTIONS2,
pel_edu_institution.inst_name,
pel_searches.institution_name
FROM
pel_searches
Inner Join pel_edu_institution ON pel_edu_institution.inst_name = pel_searches.institution_name
GROUP BY pel_searches.institution_name";
$getinstitutions_count2 = mysqli_query_ported($query_getinstitutions_count2, $connect) or die(mysqli_error($connect));
$row_getinstitutions_count2 = mysqli_fetch_assoc($getinstitutions_count2);
$totalRows_getinstitutions_count2 = mysqli_num_rows($getinstitutions_count2);

mysqli_select_db($connect,$database_connect);
$query_countclients2 = "SELECT COUNT(pel_searches.client_id) AS COUNT_CLIENTS2 FROM pel_searches  GROUP BY pel_searches.client_id ";
$countclients2 = mysqli_query_ported($query_countclients2, $connect) or die(mysqli_error($connect));
$row_countclients2 = mysqli_fetch_assoc($countclients2);
$totalRows_countclients2 = mysqli_num_rows($countclients2);?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Dashboard - Peleza Admin</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.css" />
		<link rel="stylesheet" href="../../assets/css/font-awesome.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/css/ace-fonts.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="../../assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="../assets/css/ace-part2.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="../assets/css/ace-ie.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="../../assets/js/ace-extra.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="../assets/js/html5shiv.js"></script>
		<script src="../assets/js/respond.js"></script>
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
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>				</div>

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
								<a href="#">Home</a>							</li>
							<li class="active">Dashboard</li>
						</ul><!-- /.breadcrumb -->

						<!-- #section:basics/content.searchbox -->
						<div class="nav-search" id="nav-search">
							<!-- <form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="ace-icon fa fa-search nav-search-icon"></i></span>
							</form> -->
						</div><!-- /.nav-search -->

						<!-- /section:basics/content.searchbox -->
					</div>

					<!-- /section:basics/content.breadcrumbs -->
					<div class="page-content">
						<!-- /section:settings.box -->
						<div class="page-header">
							<h1>
								Dashboard
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Overview & Statistics
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="alert alert-block alert-success">
									<button type="button" class="close" data-dismiss="alert">
										<i class="ace-icon fa fa-times"></i>
									</button>

									<i class="ace-icon fa fa-check green"></i>

									Welcome to
									<strong class="green">
										Peleza Admin
										<small>(v1.0.0)</small>
									</strong>,
									your access and activity is strictly monitored <a href="#">read terms and conditions</a> of access.
								</div>

								<div class="row">
									<div class="space-6"></div>

									<div class="col-sm-12 infobox-container">
										<!-- #section:pages/dashboard.infobox -->
                    <?php
										if (in_array('VIEW_DASHBOARD_REPORTS_MODULES', $roledata)) {
										?>
										<div class="infobox infobox-green">
											<div class="infobox-progress">
												<!-- #section:pages/dashboard.infobox.easypiechart -->
												<div class="easy-pie-chart percentage" data-percent="<?php 
												if($row_getinstitutions_count['COUNT_INSTITUTIONS']=='0')
												{
												echo "0";
												}
												else
												{
												
												echo round($totalRows_getinstitutions_count2/$row_getinstitutions_count['COUNT_INSTITUTIONS']*100, 0);
												} ?>" data-size="46">
													<span class="percent"><?php if($row_getinstitutions_count['COUNT_INSTITUTIONS']=='0')
												{
												echo "0";
												}
												else
												{
												
												echo round($totalRows_getinstitutions_count2/$row_getinstitutions_count['COUNT_INSTITUTIONS']*100, 0);
												} ?></span>%												</div>

												<!-- /section:pages/dashboard.infobox.easypiechart -->
											</div>

											<div class="infobox-data">
												<span class="infobox-text">Searched</span>

												<div class="infobox-content">
													<span class="bigger-110">~</span>
													Institutions
												</div>
											</div>
										</div>
										<?php
										}
										if (in_array('VIEW_DASHBOARD_REPORTS_CLIENTS', $roledata)) {
											?>
											<div class="infobox infobox-blue">
												<div class="infobox-progress">
													<!-- #section:pages/dashboard.infobox.easypiechart -->
													<div class="easy-pie-chart percentage" data-percent="<?php 
														if($row_countclients['COUNT_CLIENTS']=='0') {
															echo "0";
														} else {
															echo round($totalRows_countclients2/$row_countclients['COUNT_CLIENTS']*100, 0); 
														}?>" data-size="46">
														<span class="percent">
															<?php
															if($row_countclients['COUNT_CLIENTS']=='0') {
																echo "0";
															} else {
																echo round($totalRows_countclients2/$row_countclients['COUNT_CLIENTS']*100, 0); 
															}
															?>
														</span>%												
													</div>
													<!-- /section:pages/dashboard.infobox.easypiechart -->
												</div>
												<div class="infobox-data">
													<span class="infobox-text">Clients</span>
													<div class="infobox-content">
														<span class="bigger-110">~</span>
														With Searches
													</div>
												</div>
											</div>
											<?php
										}
										?>
									<div class="vspace-12-sm"></div>
								</div><!-- /.row -->

								<!-- #section:custom/extra.hr -->
								<div class="hr hr32 hr-dotted"></div>

								<!-- /section:custom/extra.hr -->
								<div class="row">
									<?php
								  if (in_array('VIEW_DASHBOARD_REPORTS_SEARCHES', $roledata)) {
										?>
										<div class="col-sm-6">
											<div class="widget-box transparent">
												<div class="widget-header widget-header-flat">
													<h4 class="widget-title lighter">
														<i class="ace-icon fa fa-star orange"></i>
														Popular Clients With Searches	
													</h4>

													<div class="widget-toolbar">
														<a href="#" data-action="collapse">
															<i class="ace-icon fa fa-chevron-up"></i>
														</a>											
													</div>
												</div>

												<div class="widget-body">
													<div class="widget-main no-padding">
														<table class="table table-bordered table-striped">
															<thead class="thin-border-bottom">
																<tr>
																	<th>
																		<i class="ace-icon fa fa-caret-right blue"></i>Name
																	</th>

																	<th>
																		<i class="ace-icon fa fa-caret-right blue"></i>Searches
																	</th>
																</tr>
															</thead>

															<tbody>
																<?php do { ?>
																	<tr>
																		<td><?php echo $row_get5topclients['client_name']; ?></td>
																		<td>
																			<small>
																				<b class="blue"><?php echo $row_get5topclients['COUNT_CLIENT']; ?></s>
																			</small>
																		</td>
																		</tr>
																	<?php } while ($row_get5topclients = mysqli_fetch_assoc($get5topclients)); ?>
															</tbody>
														</table>
													</div><!-- /.widget-main -->
												</div><!-- /.widget-body -->
											</div><!-- /.widget-box -->
										</div><!-- /.col -->

										<div class="col-sm-6">
											<div class="widget-box transparent">
												<div class="widget-header widget-header-flat">
													<h4 class="widget-title lighter">
														<i class="ace-icon fa fa-star orange"></i>
														Popular Searched Institutions
													</h4>

													<div class="widget-toolbar">
														<a href="#" data-action="collapse">
															<i class="ace-icon fa fa-chevron-up"></i>
														</a>
													</div>
												</div>

												<div class="widget-body">
													<div class="widget-main no-padding">
														<table class="table table-bordered table-striped">
															<thead class="thin-border-bottom">
																<tr>
																	<th>
																		<i class="ace-icon fa fa-caret-right blue"></i>Name
																	</th>

																	<th>
																		<i class="ace-icon fa fa-caret-right blue"></i>Searches
																	</th>
																</tr>
															</thead>

															<tbody>
																<?php do { ?>
																	<tr>
																		<td><?php echo $row_get5topinstitutions['institution_name']; ?></td>
																		<td>
																			<small>
																				<b class="red"><?php echo $row_get5topinstitutions['COUNT_INSTITUTION']; ?></s>
																			</small>															      
																		</td>
																	</tr>
																<?php } while ($row_get5topinstitutions = mysqli_fetch_assoc($get5topinstitutions)); ?>
															</tbody>
														</table>
													</div><!-- /.widget-main -->
												</div><!-- /.widget-body -->
											</div><!-- /.widget-box -->
										</div><!-- /.col -->
										<?php
									}
									?>
								</div><!-- /.row -->
								<div class="hr hr32 hr-dotted"></div>
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
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>			</a>		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='../../assets/js/jquery.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
		window.jQuery || document.write("<script src='../assets/js/jquery1x.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='../../assets/js/jquery.mobile.custom.js'>"+"<"+"/script>");
		</script>
		<script src="../../assets/js/bootstrap.js"></script>

		<!-- page specific plugin scripts -->

		<!--[if lte IE 8]>
		  <script src="../assets/js/excanvas.js"></script>
		<![endif]-->
		<script src="../../assets/js/jquery-ui.custom.js"></script>
		<script src="../../assets/js/jquery.ui.touch-punch.js"></script>
		<script src="../../assets/js/jquery.easypiechart.js"></script>
		<script src="../../assets/js/jquery.sparkline.js"></script>
		<script src="../../assets/js/flot/jquery.flot.js"></script>
		<script src="../../assets/js/flot/jquery.flot.pie.js"></script>
		<script src="../../assets/js/flot/jquery.flot.resize.js"></script>

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
				$('.easy-pie-chart.percentage').each(function(){
					var $box = $(this).closest('.infobox');
					var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
					var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
					var size = parseInt($(this).data('size')) || 50;
					$(this).easyPieChart({
						barColor: barColor,
						trackColor: trackColor,
						scaleColor: false,
						lineCap: 'butt',
						lineWidth: parseInt(size/10),
						animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
						size: size
					});
				})
			
				$('.sparkline').each(function(){
					var $box = $(this).closest('.infobox');
					var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
					$(this).sparkline('html',
									 {
										tagValuesAttribute:'data-values',
										type: 'bar',
										barColor: barColor ,
										chartRangeMin:$(this).data('min') || 0
									 });
				});
			
			
			  //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
			  //but sometimes it brings up errors with normal resize event handlers
			  $.resize.throttleWindow = false;
			
			  var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
			  var data = [
				{ label: "social networks",  data: 38.7, color: "#68BC31"},
				{ label: "search engines",  data: 24.5, color: "#2091CF"},
				{ label: "ad campaigns",  data: 8.2, color: "#AF4E96"},
				{ label: "direct traffic",  data: 18.6, color: "#DA5430"},
				{ label: "other",  data: 10, color: "#FEE074"}
			  ]
			  function drawPieChart(placeholder, data, position) {
			 	  $.plot(placeholder, data, {
					series: {
						pie: {
							show: true,
							tilt:0.8,
							highlight: {
								opacity: 0.25
							},
							stroke: {
								color: '#fff',
								width: 2
							},
							startAngle: 2
						}
					},
					legend: {
						show: true,
						position: position || "ne", 
						labelBoxBorderColor: null,
						margin:[-30,15]
					}
					,
					grid: {
						hoverable: true,
						clickable: true
					}
				 })
			 }
			 drawPieChart(placeholder, data);
			
			 /**
			 we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
			 so that's not needed actually.
			 */
			 placeholder.data('chart', data);
			 placeholder.data('draw', drawPieChart);
			
			
			  //pie chart tooltip example
			  var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
			  var previousPoint = null;
			
			  placeholder.on('plothover', function (event, pos, item) {
				if(item) {
					if (previousPoint != item.seriesIndex) {
						previousPoint = item.seriesIndex;
						var tip = item.series['label'] + " : " + item.series['percent']+'%';
						$tooltip.show().children(0).text(tip);
					}
					$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
				} else {
					$tooltip.hide();
					previousPoint = null;
				}
				
			 });
			
				/////////////////////////////////////
				$(document).one('ajaxloadstart.page', function(e) {
					$tooltip.remove();
				});
			
			
			
			
				var d1 = [];
				for (var i = 0; i < Math.PI * 2; i += 0.5) {
					d1.push([i, Math.sin(i)]);
				}
			
				var d2 = [];
				for (var i = 0; i < Math.PI * 2; i += 0.5) {
					d2.push([i, Math.cos(i)]);
				}
			
				var d3 = [];
				for (var i = 0; i < Math.PI * 2; i += 0.2) {
					d3.push([i, Math.tan(i)]);
				}
				
			
				var sales_charts = $('#sales-charts').css({'width':'100%' , 'height':'220px'});
				$.plot("#sales-charts", [
					{ label: "Domains", data: d1 },
					{ label: "Hosting", data: d2 },
					{ label: "Services", data: d3 }
				], {
					hoverable: true,
					shadowSize: 0,
					series: {
						lines: { show: true },
						points: { show: true }
					},
					xaxis: {
						tickLength: 0
					},
					yaxis: {
						ticks: 10,
						min: -2,
						max: 2,
						tickDecimals: 3
					},
					grid: {
						backgroundColor: { colors: [ "#fff", "#fff" ] },
						borderWidth: 1,
						borderColor:'#555'
					}
				});
			
			
				$('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('.tab-content')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
			
			
				$('.dialogs,.comments').ace_scroll({
					size: 300
			    });
				
				
				//Android's default browser somehow is confused when tapping on label which will lead to dragging the task
				//so disable dragging when clicking on label
				var agent = navigator.userAgent.toLowerCase();
				if("ontouchstart" in document && /applewebkit/.test(agent) && /android/.test(agent))
				  $('#tasks').on('touchstart', function(e){
					var li = $(e.target).closest('#tasks li');
					if(li.length == 0)return;
					var label = li.find('label.inline').get(0);
					if(label == e.target || $.contains(label, e.target)) e.stopImmediatePropagation() ;
				});
			
				$('#tasks').sortable({
					opacity:0.8,
					revert:true,
					forceHelperSize:true,
					placeholder: 'draggable-placeholder',
					forcePlaceholderSize:true,
					tolerance:'pointer',
					stop: function( event, ui ) {
						//just for Chrome!!!! so that dropdowns on items don't appear below other items after being moved
						$(ui.item).css('z-index', 'auto');
					}
					}
				);
				$('#tasks').disableSelection();
				$('#tasks input:checkbox').removeAttr('checked').on('click', function(){
					if(this.checked) $(this).closest('li').addClass('selected');
					else $(this).closest('li').removeClass('selected');
				});
			
			
				//show the dropdowns on top or bottom depending on window height and menu position
				$('#task-tab .dropdown-hover').on('mouseenter', function(e) {
					var offset = $(this).offset();
			
					var $w = $(window)
					if (offset.top > $w.scrollTop() + $w.innerHeight() - 100) 
						$(this).addClass('dropup');
					else $(this).removeClass('dropup');
				});
			
			})
		</script>

		<!-- the following scripts are used in demo only for onpage help and you don't need them -->
		<link rel="stylesheet" href="../../assets/css/ace.onpage-help.css" />
		<link rel="stylesheet" href="../../docs/assets/js/themes/sunburst.css" />

		<script type="text/javascript"> ace.vars['base'] = '..'; </script>
		<script src="../../assets/js/ace/elements.onpage-help.js"></script>
		<script src="../../assets/js/ace/ace.onpage-help.js"></script>
		<script src="../../docs/assets/js/rainbow.js"></script>
		<script src="../../docs/assets/js/language/generic.js"></script>
		<script src="../../docs/assets/js/language/html.js"></script>
		<script src="../../docs/assets/js/language/css.js"></script>
		<script src="../../docs/assets/js/language/javascript.js"></script>

            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script src="../../assets/js/socket.io.js"></script>
            <script src="../../assets/js/websockets.js"></script>
	</body>
</html>
<?php
mysqli_free_result($get5topinstitutions);

mysqli_free_result($get5topclients);

mysqli_free_result($countclients);

mysqli_free_result($getinstitutions_count);


?>
