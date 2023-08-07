<?php require_once('../../Connections/connect.php');

$help_id = $_GET['id'];

mysqli_select_db($connect, $database_connect);

$get_subject_query = 'SELECT * FROM main_helpsubject WHERE id =' . $help_id;
$get_help_subject = mysqli_query_ported($get_subject_query, $connect) or die(mysqli_error($connect));
$subject = mysqli_fetch_assoc($get_help_subject);

$help_subject = '';

do {
  $help_subject = $subject['subject'];
} while ($subject = mysqli_fetch_assoc($get_help_subject));

$get_messages_query = 'SELECT * FROM main_helpmessage WHERE subject_id = ' . $help_id;
$get_help_messages = mysqli_query_ported($get_messages_query, $connect) or die(mysqli_error($connect));
$messages = mysqli_fetch_assoc($get_help_messages);

$get_responses_query = 'SELECT * FROM main_helpresponse WHERE subject_id = ' . $help_id;
$get_help_responses = mysqli_query_ported($get_responses_query, $connect) or die(mysqli_error($connect));
$responses = mysqli_fetch_assoc($get_help_responses);
$responses_count = mysqli_num_rows($get_help_responses);

$texts = array();

function date_compare($a, $b)
{
  $t1 = strtotime($a['created_at']);
  $t2 = strtotime($b['created_at']);
  return $t1 - $t2;
}

function format_time($time) {
  $t2 = strtotime($time);
  return date('d M Y', $t2);
}

do {
  $message = [ "text" => $messages['message'], "created_at" => $messages['created_at'], "type" => "message" ];
  array_push($texts, $message);
} while ($messages = mysqli_fetch_assoc($get_help_messages));

if ($responses_count > 0) {
  do {
    $response = [ "text" => $responses['response'], "created_at" => $responses['created_at'], "type" => "response" ];
    array_push($texts, $response);
  } while ($responses = mysqli_fetch_assoc($get_help_responses));
}

usort($texts, 'date_compare');

if (isset($_POST['message'])) {
  $timestamp = time(); 
  $date = new DateTime("@".$timestamp);

  $date->setTimezone(new DateTimeZone('Africa/Nairobi'));   
  // echo $date->format('Y-m-d H:i:sP') . "<br>";

  $insert_sql = sprintf(
    "INSERT INTO main_helpresponse (response, subject_id, created_at) VALUES (%s, %s, %s)", 
    GetSQLValueString($_POST['message'], "text"),
    GetSQLValueString($help_id, "int"),
    GetSQLValueString($date->format('Y-m-d H:i:sP'), "date"),
  );
  mysqli_select_db($connect, $database_connect);
	mysqli_query_ported($insert_sql, $connect);
  
  $update_sql = sprintf("UPDATE main_helpmessage SET read_at = %s WHERE subject_id = %s", GetSQLValueString($date->format('Y-m-d H:i:sP'), "date"), $help_id);
  mysqli_select_db($connect,$database_connect);
  mysqli_query_ported($update_sql, $connect) or die(mysqli_error($connect));
  header(sprintf("Location: %s", './help.php'));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title>Help - Peleza Admin</title>

	<meta name="description" content="Static &amp; Dynamic Tables" />
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
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.css" />
	<link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.css" />
	<link rel="stylesheet" href="../../assets/css/daterangepicker.css" />
	<link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.css" />
	<link rel="stylesheet" href="../../assets/css/colorpicker.css" />
	<!-- ace settings handler -->
	<script src="../../assets/js/ace-extra.js"></script>

	<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

	<!--[if lte IE 8]>
		<script src="../assets/js/html5shiv.js"></script>
		<script src="../assets/js/respond.js"></script>
		<![endif]-->
</head>
<style>
  .page-content {
    display: flex;
    justify-content: center;
    margin-right: auto;
  }

  .messages-box {
    background-color: lightgray;
    border: 1px solid gray;
    border-radius: 3px;
    margin-bottom: 4px;
    min-height: 450px;
    max-height: 500px;
    overflow-y: scroll;
  }

  .message-box {
    background-color: gray;
    max-width: 590px;
    min-width: 590px;
    margin: 10px;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
  }

  .sent {
    border-left: 6px solid #153F56;
  }

  .received {
    border-left: 6px solid #DC3545;
  }
</style>
<body class="no-skin">
  <!-- #section:basics/navbar.layout -->
	<div id="navbar" class="navbar navbar-default">
		<script type="text/javascript">
			try {
				ace.settings.check('navbar', 'fixed')
			} catch (e) {}
		</script>
		<?php include('../header2.php'); ?>
	</div>

  <!-- /section:basics/navbar.layout -->
	<div class="main-container" id="main-container">
    <script type="text/javascript">
			try {
				ace.settings.check('main-container', 'fixed')
			} catch (e) {}
		</script>

    <!-- #section:basics/sidebar -->
		<div id="sidebar" class="sidebar responsive">
			<script type="text/javascript">
				try {
					ace.settings.check('sidebar', 'fixed')
				} catch (e) {}
			</script>
			<?php include('../sidebarmenu2.php'); ?>


			<!-- #section:basics/sidebar.layout.minimize -->
			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>

			<!-- /section:basics/sidebar.layout.minimize -->
			<script type="text/javascript">
				try {
					ace.settings.check('sidebar', 'collapsed')
				} catch (e) {}
			</script>
		</div>

    <!-- /section:basics/sidebar -->
		<div class="main-content">
      <div class="main-content-inner">
        <!-- #section:basics/content.breadcrumbs -->
        <div class="breadcrumbs" id="breadcrumbs">
          <script type="text/javascript">
            try {
              ace.settings.check('breadcrumbs', 'fixed')
            } catch (e) {}
          </script>
  
          <ul class="breadcrumb">
            <ul class="breadcrumb">
              <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#">Home</a>
              </li>
  
              <li>
                <a href="./help.php">Help Section</a>
              </li>
  
              <li>
                <a href="#">Details</a>
              </li>
            </ul><!-- /.breadcrumb -->
          </ul>
        </div>
  
        <div class="page-content">
          <div class="">
            <div class="position-relative">
              <div id="login-box" class="login-box visible widget-box no-border">
                <div class="widget-body">
                  <div class="widget-main">
                    <h4 class="header blue lighter bigger">
                      <i class="ace-icon fa fa-coffee green"></i>
                      <?php echo $help_subject ?>
                    </h4>

                    <div class="messages-box" id="messages-box">
                      <?php foreach ($texts as $key => $message) {
                        ?>
                          <div class="message-box <?php echo $message['type'] === 'message' ? 'sent' : 'received' ?>">
                            <span><?php echo $message['text'] ?></span>
                            <span><?php echo format_time($message['created_at']) ?></span>
                          </div>
                        <?php
                      } ?>
                    </div>
  
                    <div class="space-6"></div>
  
                    <form name="loginuser" action="" method="POST">
                      <fieldset>
                        <label class="block clearfix">
                          <span class="block input-icon input-icon-right"><span id="sprytextfield2">
                          <input id="username" name="message" type="text" class="form-control" placeholder="Message" autocomplete="off" />
                        </label>

                        <div class="clearfix">
                          <button type="submit" value="submit" class="width-35 pull-right btn btn-sm btn-primary">
                          <span class="bigger-110">Send</span></button>
                        </div>
                      </fieldset>
                    </form>
                  </div><!-- /.widget-main -->
                </div><!-- /.login-box -->
              </div><!-- /.position-relative -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    var objDiv = document.getElementById("messages-box");
    objDiv.scrollTop = objDiv.scrollHeight;
  </script>
</body>