<?php
require_once('../../Connections/connect.php'); 
require_once '../../v1/Notifier.php';

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$colname_getrequestid = "-1";
if (isset($_GET['request_id'])) {
    $colname_getrequestid = $_GET['request_id'];
}

$errorcode = '';

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editdetails")) {

    $updateSQL = sprintf("UPDATE pel_company_data SET company_name=%s, registration_number=%s, status=%s, registration_date=%s, country=%s, data_source=%s, data_notes=%s, added_by=%s, address=%s, office=%s, operation_status=%s, industry=%s WHERE company_id=%s",
        GetSQLValueString(strtoupper($_POST['company_name']), "text"),
        GetSQLValueString(strtoupper($_POST['registration_number']), "text"),
        GetSQLValueString($_POST['status'], "text"),
        GetSQLValueString($_POST['registration_date'], "text"),
        GetSQLValueString($_POST['country'], "text"),
        GetSQLValueString($_POST['data_source'], "text"),
        GetSQLValueString($_POST['data_notes'], "text"),
        GetSQLValueString($_POST['added_by'], "text"),
        GetSQLValueString(strtoupper($_POST['address']), "text"),
        GetSQLValueString($_POST['office'], "text"),
        GetSQLValueString($_POST['operation_status'], "text"),
        GetSQLValueString($_POST['industry'], "text"),
        GetSQLValueString($_POST['company_id'], "int"));

    mysqli_select_db($connect, $database_connect);
    mysqli_query_ported($updateSQL, $connect);

    $colname_getrequestid = $_POST['request_id'];

    if (mysqli_error($connect)) {
        $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												ERROR!!!!!
											</strong>

											 Details of the company were not updated succesfully.
											<br />
										</div>';

    } else {

        $updateGoTo = "companyregistrationview.php?request_id=$colname_getrequestid";
        /* if (isset($_SERVER['QUERY_STRING'])) {
           $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
           $updateGoTo .= $_SERVER['QUERY_STRING'];
         }*/
        header(sprintf("Location: %s", $updateGoTo));
    }
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "newdetails")) {
    $insertSQL = sprintf("INSERT INTO pel_company_data (company_name, registration_number, status, registration_date, country, added_by, data_source, data_notes, address, office, operation_status, industry, search_id ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString(strtoupper($_POST['company_name']), "text"),
        GetSQLValueString(strtoupper($_POST['registration_number']), "text"),
        GetSQLValueString($_POST['status'], "text"),
        GetSQLValueString($_POST['registration_date'], "text"),
        GetSQLValueString($_POST['country'], "text"),
        GetSQLValueString($_POST['added_by'], "text"),
        GetSQLValueString(strtoupper($_POST['data_source']), "text"),
        GetSQLValueString($_POST['data_notes'], "text"),
        GetSQLValueString($_POST['address'], "text"),
        GetSQLValueString($_POST['office'], "text"),
        GetSQLValueString($_POST['operation_status'], "text"),
        GetSQLValueString($_POST['industry'], "text"),
        GetSQLValueString($_POST['search_id'], "text"));

    mysqli_select_db($connect, $database_connect);
    mysqli_query_ported($insertSQL, $connect);
    $colname_getrequestid = $_POST['request_id'];

    if (mysqli_error($connect)) {
        $errorcode = '<div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">
                            <i class="ace-icon fa fa-times"></i>
                        </button>

                        <strong>
                            <i class="ace-icon fa fa-times"></i>
                            Oh snap!
                        </strong>

                        Details of the company ahvent been added.
                        <br />
                    </div>';
    } else {
        $updateGoTo = "companyregistrationview.php?request_id=$colname_getrequestid";
        header(sprintf("Location: %s", $updateGoTo));
    }
}

?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <meta charset="utf-8"/>
        <title>Individual Data Management - Peleza Admin</title>

        <meta name="description" content="Static &amp; Dynamic Tables"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

        <!-- bootstrap & fontawesome -->
        <link rel="stylesheet" href="../../assets/css/bootstrap.css"/>
        <link rel="stylesheet" href="../../assets/css/font-awesome.css"/>

        <!-- page specific plugin styles -->
        <link rel="stylesheet" href="../../assets/css/jquery-ui.custom.css"/>
        <link rel="stylesheet" href="../../assets/css/chosen.css"/>
        <link rel="stylesheet" href="../../assets/css/datepicker.css"/>
        <link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.css"/>
        <link rel="stylesheet" href="../../assets/css/daterangepicker.css"/>
        <link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.css"/>
        <link rel="stylesheet" href="../../assets/css/colorpicker.css"/>

        <!-- text fonts -->
        <link rel="stylesheet" href="../../assets/css/ace-fonts.css"/>

        <!-- ace styles -->
        <link rel="stylesheet" href="../../assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style"/>

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
            try {
                ace.settings.check('navbar', 'fixed')
            } catch (e) {
            }
        </script>
        <?php include('../header2.php'); ?>
    </div>

    <!-- /section:basics/navbar.layout -->
    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {
            }
        </script>

        <!-- #section:basics/sidebar -->
        <div id="sidebar" class="sidebar responsive">
            <script type="text/javascript">
                try {
                    ace.settings.check('sidebar', 'fixed')
                } catch (e) {
                }
            </script>
            <?php include('../sidebarmenu2.php'); ?>
            <!-- #section:basics/sidebar.layout.minimize -->
            <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left"
                   data-icon2="ace-icon fa fa-angle-double-right">
                </i>
            </div>

            <!-- /section:basics/sidebar.layout.minimize -->
            <script type="text/javascript">
                try {
                    ace.settings.check('sidebar', 'collapsed')
                } catch (e) {
                }
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
                        } catch (e) {
                        }
                    </script>

                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="#">Home</a>
                        </li>
                        <li>
                            <a href="#">Peleza Modules</a>
                        </li>
                        <li>
                            <a href="#">Individual</a>
                        </li>
                        <li class="active">Individual Data Entry</li>
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
                                    <h3 align="left" class="header smaller lighter blue">Add Individual Data Details</h3>
                                </div>
                                <?php
                                $query_getstudent = "SELECT * FROM pel_psmt_request Inner Join pel_client ON pel_client.client_id = pel_psmt_request.client_id WHERE request_id = " . $colname_getrequestid . "";
                                $getstudent = mysqli_query_ported($query_getstudent, $connect) or die(mysqli_error($connect));
                                $row_getstudent = mysqli_fetch_assoc($getstudent);
                                $totalRows_getstudent = mysqli_num_rows($getstudent);

                                $refnumber2 = $row_getstudent['request_ref_number'];
                                $request_plan2 = $row_getstudent['request_plan'];
                                $bg_dataset_name2 = $row_getstudent['bg_dataset_name'];
                                $client_login_username2 = $row_getstudent['client_login_username'];
                                $client_first_name2 = $row_getstudent['client_first_name'];
                                $final_notify2 = $row_getstudent['final_notify'];

                                mysqli_select_db($connect, $database_connect);
                                $query_getgetprogress_update = sprintf("SELECT
                                                                        pel_psmt_request_modules.`status`
                                                                        FROM
                                                                        pel_module
                                                                        Inner Join pel_psmt_request_modules ON pel_psmt_request_modules.module_id = pel_module.module_id
                                                                        WHERE pel_psmt_request_modules.request_ref_number = %s AND pel_psmt_request_modules.status = '11'", GetSQLValueString($refnumber2, "text"));
                                $getgetprogress_update = mysqli_query_ported($query_getgetprogress_update, $connect) or die(mysqli_error($connect));
                                $row_getgetprogress_update = mysqli_fetch_assoc($getgetprogress_update);
                                $totalRows_getgetprogress_update = mysqli_num_rows($getgetprogress_update);

                                if ($totalRows_getgetprogress_update > 0) {
                                    $deleteSQL2 = sprintf("UPDATE pel_psmt_request SET status='33', verification_status='33' WHERE request_ref_number=%s",
                                        GetSQLValueString($refnumber2, "text"));
                                    mysqli_select_db($connect, $database_connect);
                                    $Result2 = mysqli_query_ported($deleteSQL2, $connect) or die(mysqli_error($connect));
                                }

                                $query_getgetprogress_update2 = sprintf("SELECT
                                                                        pel_psmt_request_modules.`status`
                                                                        FROM
                                                                        pel_module
                                                                        Inner Join pel_psmt_request_modules ON pel_psmt_request_modules.module_id = pel_module.module_id
                                                                        WHERE pel_psmt_request_modules.request_ref_number = %s AND pel_psmt_request_modules.status = '00'", GetSQLValueString($refnumber2, "text"));
                                $getgetprogress_update2 = mysqli_query_ported($query_getgetprogress_update2, $connect) or die(mysqli_error($connect));
                                $row_getgetprogress_update2 = mysqli_fetch_assoc($getgetprogress_update2);
                                $totalRows_getgetprogress_update2 = mysqli_num_rows($getgetprogress_update2);
                                
                                if ($totalRows_getgetprogress_update2 == 0) {
                                    $deleteSQL2 = sprintf("UPDATE pel_psmt_request SET status='11', verification_status='11', final_notify='11' WHERE request_ref_number=%s",
                                        GetSQLValueString($refnumber2, "text"));
                                    mysqli_select_db($connect, $database_connect);
                                    $Result2 = mysqli_query_ported($deleteSQL2, $connect) or die(mysqli_error($connect));

                                    if ($final_notify2 == '11') {
                                        $refnumber2 = $row_getstudent['request_ref_number'];
                                        $request_plan2 = $row_getstudent['request_plan'];
                                        $bg_dataset_name2 = $row_getstudent['bg_dataset_name'];
                                        $client_email_address2 = $row_getstudent['client_email_address'];
                                        $client_first_name2 = $row_getstudent['client_first_name'];
                                        $final_notify2 = $row_getstudent['final_notify'];

                                        require("../../PHPMailer/PHPMailer.php");
                                        require("../../PHPMailer/SMTP.php");
                                        require("../../PHPMailer/Exception.php");

                                        //$mail = new PHPMailer;
                                        $mail = new PHPMailer\PHPMailer\PHPMailer();

                                        $mail->isSMTP();

                                        $mail->Host = 'eleven.deepafrica.com';             // Specify main and backup SMTP servers
                                        $mail->SMTPAuth = true;                     // Enable SMTP authentication
                                        $mail->Username = 'admin@pidva.africa';          // SMTP username
                                        $mail->Password = 'Gu!Z@JMu*ann'; // SMTP password
                                        $mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
                                        $mail->Port = 587;                          // TCP port to connect to

                                        $mail->setFrom('admin@pidva.africa', 'PIDVA Administrator');
                                        $mail->addAddress($client_email_address2);
                                        //$mail->addAddress('omintolbert@gmail.com');   // Add a recipient
                                        //$mail->addCC('mwendemarita@gmail.com');
                                        //$mail->addBCC('omintolbert@gmail.com');

                                        $mail->isHTML(true);  // Set email format to HTML
                                        //echo "<br/>";
                                        $bodyContent = '<table width="530" cellspacing="0" cellpadding="0" border="0" align="center">
                                                            <tbody>
                                                                <tr valign="top">
                                                                <td>Hi ' . $client_first_name2 . ', <br />
                                                                    <br />
                                                                Your BG request has been completed for the below candidate and results are currently in the portal. Please login to view full report.</td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                            <table width="530" cellspacing="0" cellpadding="0" border="0" align="center">
                                                            <tbody>
                                                                <tr valign="top">
                                                                <td><br />
                                                                <br />NAME: <strong>' . $bg_dataset_name2 . '</strong> <br /><br /> REF NUMBER: <strong>' . $refnumber2 . '</strong> 
                                                                    <br />
                                                                </td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                            <table width="530" cellspacing="0" cellpadding="0" border="0" align="center">
                                                            <tbody>
                                                                <tr valign="top">
                                                                <td></td>
                                                                </tr>
                                                                <tr>
                                                                <td width="137"></td>
                                                                <td width="216" align="center"><table align="center">
                                                                <tr>
                                                                    <td style="background-color: #c2d247;border-color: #c2d247;border: 2px solid #c2d247;padding: 10px;text-align: center;">
                                                                        <a style="display: block;color: #094156;font-size: 12px;text-decoration: none;text-transform: uppercase;" href="https://psmt.pidva.africa/" target="_blank">CHECK RESULTS</a>
                                                                    </td>
                                                                </tr>
                                                            </table></td>
                                                                <td width="137"></td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                            <table width="530" cellspacing="0" cellpadding="0" border="0" align="center">
                                                            <tbody>
                                                                <tr valign="top">
                                                                <td></td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                            <table width="530" cellspacing="0" cellpadding="0" border="0" align="center">
                                                            <tbody>
                                                                <tr valign="top">
                                                                <td><br />
                                                                
                                                                    <br />  You have received this mandatory email service  announcement to update you about important changes to your PSMT request.<br /><br />
                                                                </td>
                                                                </tr>
                                                                <tr>
                                                                <td width="100%">Sincerely,<br />
                                                                    <strong>PSMT Administrator</strong> <br />
                                                                    verify@peleza.com<br />
                                                                
                                                                    copyright©2020.All rights reserved.PSMT.<br />
                                                                    <br />
                                                                </td>
                                                                </tr>
                                                            </tbody>
                                                            </table>';


                                        $mail->Subject = 'Do Not Reply: REQUEST FINAL NOTIFICATION';
                                        $mail->Body = $bodyContent;

                                        if (!$mail->send()) {
                                            echo 'Message could not be sent.';
                                            echo 'Mailer Error: ' . $mail->ErrorInfo;
                                        } else {
                                            //  echo 'Message has been sent';
                                        }
                                    }
                                } else {
                                    if ($row_getstudent['verification_status'] == '00') {
                                        $deleteSQL2 = sprintf(
                                            "UPDATE pel_psmt_request SET status='44', verification_status='44' WHERE request_ref_number=%s",
                                            GetSQLValueString($refnumber2, 'text')
                                        );
                                        mysqli_select_db($connect, $database_connect);
                                        ($Result2 = mysqli_query_ported($deleteSQL2, $connect)) or die(mysqli_error($connect));
                                        $mysqli_affected_rows = mysqli_affected_rows($connect);
    
                                        $reference_number = $refnumber2;
                                        $progressStatus = 'In Progress';
                                        $progressPercentage = 44;
                                        $description = 'status updates at ' . date('Y-m-d H:i:s');
    
                                        if ($mysqli_affected_rows > 0) {
                                            $notify = new Notifier();
                                            $notify->notify(
                                                $reference_number,
                                                $progressStatus,
                                                $progressPercentage,
                                                $description
                                            );
                                        }
                                    }
                                }
                                ?>

                                <h3 align="left" class=" smaller lighter blue"><strong>SEARCH REF: </strong> <?php echo $row_getstudent['request_ref_number']; ?></h3>

                                <div>
                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Request Package</th>
                                                <th>Request Date</th>
                                                <th>Client Name</th>
                                                <th>Company Name</th>
                                                <th class="hidden-480">Data Set Type</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td><?php echo $row_getstudent['request_plan']; ?></td>
                                                <td><?php echo date('Y m d H:i', strtotime($row_getstudent['request_date'])); ?></td>
                                                <td><?php echo $row_getstudent['client_name']; ?></td>
                                                <td><?php echo $row_getstudent['company_name']; ?></td>
                                                <td><?php echo $row_getstudent['dataset_name']; ?></td>
                                            </tr>
                                        </tbody>

                                        <thead>
                                        <tr>
                                            <th>Dataset Name</th>
                                            <th>Country</th>
                                            <th>Email Address</th>
                                            <th>Identity Number</th>
                                            <th class="hidden-480">Status</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>
                                                <a href="#"><?php echo $row_getstudent['bg_dataset_name']; ?></a>
                                            </td>
                                            <td><?php echo $row_getstudent['dataset_citizenship']; ?></td>
                                            <td><?php echo $row_getstudent['bg_dataset_email']; ?></td>
                                            <td><?php echo $row_getstudent['bg_dataset_idnumber']; ?></td>

                                            <td class="hidden-480"> 
                                                <?php
                                                    if($row_getstudent['verification_status'] === '44'){
                                                        ?>
                                                            <span class="label label-sm label-warning">In Progress</span>	
                                                        <?php
                                                    }
                                                    if($row_getstudent['verification_status'] === '00') {
                                                        ?>
                                                            <span class="label label-sm label-purple">New Request</span>	
                                                        <?php
                                                    }	
                                                    if($row_getstudent['verification_status'] === '11') {
                                                        ?>
                                                            <span class="label label-sm label-success">Final</span>	
                                                        <?php
                                                    }
                                                    if($row_getstudent['verification_status'] === '22') {
                                                        ?>
                                                            <span class="label label-sm label-warning">UnReviewed</span>	
                                                        <?php
                                                    }	
                                                    if($row_getstudent['verification_status'] === '33') {
                                                        ?>
                                                            <span class="label label-sm label-primary">Interim Data</span>	
                                                        <?php
                                                    }
                                                    if ($row_getstudent['verification_status'] == '55') {
                                                        ?>
                                                            <span class="label label-sm label-danger">Invalidated</span>
                                                        <?php
                                                    }
                                                    if ($row_getstudent['verification_status'] == '66') { ?>
                                                        <span class="label label-sm label-warning">Manual</span>
                                                    <?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>

                                        <thead>
                                        <tr>
                                            <th>Locked By</th>
                                            <th>Date Locked</th>
                                            <th>Last Date Update</th>
                                            <th>Report Adverse Status</th>
                                            <th>Set Adverse Status</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>
                                                <a href="#"><?php echo $row_getstudent['user_name']; ?></a>
                                            </td>
                                            <td><?php echo $row_getstudent['user_lock_date']; ?></td>
                                            <td><?php echo $row_getstudent['status_date']; ?></td>
                                            <td><?php
                                                if ($row_getstudent['adverse_status'] == 'NO ADVERSE MENTION') {
                                                    ?>
                                                    <span class="label label-sm label-success">NO ADVERSE MENTION</span>
                                                    <?php
                                                }
                                                if ($row_getstudent['adverse_status'] == 'ADVERSE MENTION') {
                                                    ?>
                                                    <span class="label label-sm label-danger">ADVERSE MENTION</span>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <form name="formc" action="<?php echo $editFormAction; ?>"
                                                      method="POST">
                                                    <input type="hidden" name="requestref" id="requestref"
                                                           value="<?php echo $row_getstudent['request_ref_number']; ?>">
                                                    <select style="background-color:#cdd53a;" name="adverse_status_name"
                                                            id="search-text-inputcountry"
                                                            class="form-control inputcountry chosen"
                                                            onchange="this.form.submit()" required>
                                                        <option value="">Select Adverse Status</option>

                                                        <option value="ADVERSE MENTION">ADVERSE MENTION</option>
                                                        <option value="NO ADVERSE MENTION">NO ADVERSE MENTION</option>
                                                    </select>

                                                    <input type="hidden" name="MM_insert" value="formc">
                                                </form>
                                                <?php

                                                if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formc")) {
                                                    $adverse_status_name = $_POST["adverse_status_name"];
                                                    $requestref = $_POST["requestref"];
                                                    $deleteSQL = sprintf("UPDATE pel_psmt_request SET adverse_status=%s WHERE request_ref_number=%s",
                                                        GetSQLValueString($adverse_status_name, "text"),
                                                        GetSQLValueString($requestref, "text"));
                                                    mysqli_select_db($connect, $database_connect);
                                                    $Result1 = mysqli_query_ported($deleteSQL, $connect) or die(mysqli_error($connect));
                                                    if (mysqli_error($connect)) {
                                                        $errorcode = '<div class="alert alert-danger">
                                                                        <button type="button" class="close" data-dismiss="alert">
                                                                            <i class="ace-icon fa fa-times"></i>
                                                                        </button>
                                                                        <strong>
                                                                            <i class="ace-icon fa fa-times"></i>
                                                                            Oh snap!
                                                                        </strong>
                                                                            Details of the data notes havenot been blocked
                                                                        <br />
                                                                    </div>';

                                                    } else {
                                                        $updateGoTo = "individualdataentry.php?request_id=$colname_getrequestid";
                                                        ?>
                                                            <meta http-equiv="Refresh" content="0; url=<?php echo $updateGoTo; ?>">
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <hr/>
                                    <table id="simple-table" class="table  table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>BG SCREENING MODULES</th>
                                            <th>Files Uploaded</th>
                                            <th>Data Provisions</th>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td width="50%">
                                                <table id="simple-table" class="table  table-bordered table-hover">
                                                    <?php
                                                    $refnumber = $row_getstudent['request_ref_number'];
                                                    mysqli_select_db($connect, $database_connect);
                                                    $query_getprogress2 = sprintf("SELECT
                                                                                    pel_module.module_role,
                                                                                    pel_psmt_request_modules.`status`,
                                                                                    pel_psmt_request_modules.request_ref_number,
                                                                                    pel_psmt_request_modules.module_name,
                                                                                    pel_psmt_request_modules.module_id
                                                                                    FROM
                                                                                    pel_module
                                                                                    Inner Join pel_psmt_request_modules ON pel_psmt_request_modules.module_id = pel_module.module_id WHERE pel_psmt_request_modules.request_ref_number = %s ORDER BY pel_psmt_request_modules.`status` DESC", GetSQLValueString($refnumber, "text"));
                                                    $getprogress2 = mysqli_query_ported($query_getprogress2, $connect) or die(mysqli_error($connect));
                                                    $row_getprogress2 = mysqli_fetch_assoc($getprogress2);
                                                    $totalRows_getprogress2 = mysqli_num_rows($getprogress2);
                                                    $complete = 0;
                                                    $all = 0;
                                                    if ($totalRows_getprogress2 > 0) {
                                                        $i = 1;
                                                        do {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $i++; ?>.</td>
                                                                <td>
                                                                    <strong><?php echo $row_getprogress2['module_name']; ?></strong>
                                                                </td>
                                                                <td class="hidden-480"> 
                                                                    <?php
                                                                    if ($row_getprogress2['status'] == '44') {
                                                                        ?>

                                                                        <span class="label label-sm label-warning">In Progress</span>
                                                                        <?php
                                                                    }
                                                                    if ($row_getprogress2['status'] == '00') {
                                                                        ?>
                                                                        <span class="label label-sm label-purple">No Data</span>
                                                                        <?php
                                                                    }
                                                                    if ($row_getprogress2['status'] == '11') {
                                                                        ?>
                                                                        <span class="label label-sm label-success">Valid Data</span>
                                                                        <?php
                                                                    }
                                                                    if ($row_getprogress2['status'] == '22') {
                                                                        ?>
                                                                            <span class="label label-sm label-warning">Not Reviewed</span>
                                                                        <?php
                                                                    }
                                                                    if ($row_getprogress2['status'] == '33') {
                                                                        ?>
                                                                            <span class="label label-sm label-primary">Interim Data</span>
                                                                        <?php
                                                                    }
                                                                    if ($row_getprogress2['status'] == '66') {
                                                                        ?>
                                                                            <span class="label label-sm label-warning">Manual</span>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <?php $pagename = $row_getprogress2['module_name'] == "EDUCATION VERIFICATION" ? "educationcheck" : $row_getprogress2['module_role']; ?>

                                                                <td>
                                                                    <a href="<?php echo $pagename; ?>.php?request_id=<?php echo $row_getstudent['request_id']; ?>&moduleid=<?php echo $row_getprogress2['module_id']; ?>"
                                                                       class="orange">
                                                                        <button class="btn btn-xs btn-info">
                                                                            <i class="ace-icon fa fa-pencil smaller-80"></i>
                                                                        </button>
                                                                    </a>
                                                                </td>
                                                                <?php
                                                                    if ($row_getstudent['verification_status'] !== '66') {
                                                                        ?>
                                                                            <td>
                                                                                    <form action="" method="post">
                                                                                        <input type="hidden" name="status_manual" value="manual">
                                                                                        <input type="hidden" name="status" value="66">
                                                                                        <input type="hidden" name="request_id" value="<?php echo $row_getstudent['request_id'] ?>">
                                                                                        <button type="submit" class="btn btn-warning btn-xs">Set to Manual</button>
                                                                                    </form>
                                                                            </td>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </tr>

                                                            <?php
                                                        } while ($row_getprogress2 = mysqli_fetch_assoc($getprogress2));
                                                    } ?>
                                                </table>
                                            </td>
                                            <td width="">
                                                <table id="simple-table" class="table table-bordered table-hover">
                                                    <?php
                                                    $filetracker = $row_getstudent['file_tracker'];
                                                    mysqli_select_db($connect, $database_connect);
                                                    $query_getfiles = "SELECT pel_psmt_request.request_id, pel_psmt_files.psmtfile_filetoken,pel_psmt_files.psmtfile_id,pel_psmt_files.psmtfile_name,pel_psmt_files.psmtfile_type,pel_psmt_files.`status`,
                                                                                pel_psmt_files.request_id,pel_psmt_files.client_id FROM pel_psmt_request
                                                                                Inner Join pel_psmt_files ON pel_psmt_request.file_tracker = pel_psmt_files.psmtfile_filetoken WHERE pel_psmt_request.file_tracker = '$filetracker' and pel_psmt_files.data_type='file'";
                                                    $getfiles = mysqli_query_ported($query_getfiles, $connect) or die(mysqli_error($connect));
                                                    $row_getfiles = mysqli_fetch_assoc($getfiles);
                                                    $totalRows_getfiles = mysqli_num_rows($getfiles);

                                                    $i = 1;
                                                    do {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $i++; ?>.</td>
                                                            <td>
                                                                <a href="<?php echo $row_getfiles['psmtfile_name']; ?>"
                                                                   target="_blank">
                                                                    <strong><?php echo $row_getfiles['psmtfile_type']; ?></strong>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    } while ($row_getfiles = mysqli_fetch_assoc($getfiles)); ?>
                                                </table>
                                            </td>
                                            <td>
                                                <?php
                                                mysqli_select_db($connect, $database_connect);

                                                $query_getfiles2 = "SELECT pel_psmt_request.request_id, pel_psmt_files.psmtfile_filetoken,pel_psmt_files.psmtfile_id,pel_psmt_files.psmtfile_name,pel_psmt_files.psmtfile_type,pel_psmt_files.`status`,
                                                                    pel_psmt_files.request_id,pel_psmt_files.client_id FROM pel_psmt_request
                                                                    Inner Join pel_psmt_files ON pel_psmt_request.file_tracker = pel_psmt_files.psmtfile_filetoken WHERE pel_psmt_request.file_tracker = '$filetracker' and pel_psmt_files.data_type='text'";

                                                $getfiles2 = mysqli_query_ported($query_getfiles2, $connect) or die(mysqli_error($connect));

                                                $row_getfiles2 = mysqli_fetch_assoc($getfiles2);

                                                $totalRows_getfiles2 = mysqli_num_rows($getfiles2);

                                                if ($totalRows_getfiles2 > 0) {
                                                    ?>
                                                    <table id="simple-table" class="table table-bordered table-hover">
                                                        <?php
                                                        do {
                                                            ?>
                                                            <tr>
                                                                <td width="35%">
                                                                    <b><?php echo $row_getfiles2['psmtfile_type']; ?>
                                                                        :
                                                                    </b>
                                                                </td>
                                                                <td><?php echo $row_getfiles2['psmtfile_name']; ?></td>
                                                                <td>
                                                                    <a href="../filesmanager/download.php?filename=<?php echo $row_getfiles2['psmtfile_name'] ?>" class="btn btn-sm btn-primary">Download</a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        } while ($row_getfiles2 = mysqli_fetch_assoc($getfiles2)); ?>
                                                    </table>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="col-xs-12">
                                        <?php echo $errorcode; ?>
                                    </div>
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
            <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i></a></div><!-- /.main-container -->
    <!-- basic scripts -->

    <!--[if !IE]> -->
    <script type="text/javascript">
        window.jQuery || document.write("<script src='../../assets/js/jquery.js'>" + "<" + "/script>");
    </script>

    <!-- <![endif]-->

    <!--[if IE]>
    <script type="text/javascript">
        window.jQuery || document.write("<script src='../../assets/js/jquery1x.js'>" + "<" + "/script>");
    </script>
    <![endif]-->
    <script type="text/javascript">
        if ('ontouchstart' in document.documentElement) document.write("<script src='../../assets/js/jquery.mobile.custom.js'>" + "<" + "/script>");
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


    <script src="../../assets/js/markdown/markdown.js"></script>
    <script src="../../assets/js/markdown/bootstrap-markdown.js"></script>
    <script src="../../assets/js/jquery.hotkeys.js"></script>
    <script src="../../assets/js/bootstrap-wysiwyg.js"></script>
    <script src="../../assets/js/bootbox.js"></script>

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
        jQuery(function ($) {
            //initiate dataTables plugin
            var oTable1 =
                $('#dynamic-table')
                //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                    .dataTable({
                        bAutoWidth: false,
                        "aoColumns": [
                            null, null, null, null, null, null,
                            {"bSortable": false}
                        ],
                        "aaSorting": [],

                        //,
                        //"sScrollY": "200px",
                        //"bPaginate": false,

                        //"sScrollX": "100%",
                        //"sScrollXInner": "120%",
                        //"bScrollCollapse": true,
                        //Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
                        //you may want to wrap the table inside a "div.dataTables_borderWrap" element

                        //"iDisplayLength": 50
                    });
            //oTable1.fnAdjustColumnSizing();


            //TableTools settings
            TableTools.classes.container = "btn-group btn-overlap";
            TableTools.classes.print = {
                "body": "DTTT_Print",
                "info": "tableTools-alert gritter-item-wrapper gritter-info gritter-center white",
                "message": "tableTools-print-navbar"
            }

            //initiate TableTools extension
            var tableTools_obj = new $.fn.dataTable.TableTools(oTable1, {
                "sSwfPath": "../../assets/js/dataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf", //in Ace demo ../assets will be replaced by correct assets path

                "sRowSelector": "td:not(:last-child)",
                "sRowSelect": "multi",
                "fnRowSelected": function (row) {
                    //check checkbox when row is selected
                    try {
                        $(row).find('input[type=checkbox]').get(0).checked = true
                    } catch (e) {
                    }
                },
                "fnRowDeselected": function (row) {
                    //uncheck checkbox
                    try {
                        $(row).find('input[type=checkbox]').get(0).checked = false
                    } catch (e) {
                    }
                },

                "sSelectedClass": "success",
                "aButtons": [
                    {
                        "sExtends": "copy",
                        "sToolTip": "Copy to clipboard",
                        "sButtonClass": "btn btn-white btn-primary btn-bold",
                        "sButtonText": "<i class='fa fa-copy bigger-110 pink'></i>",
                        "fnComplete": function () {
                            this.fnInfo('<h3 class="no-margin-top smaller">Table copied</h3>\
									<p>Copied ' + (oTable1.fnSettings().fnRecordsTotal()) + ' row(s) to the clipboard.</p>',
                                1500
                            );
                        }
                    },

                    {
                        "sExtends": "csv",
                        "sToolTip": "Export to CSV",
                        "sButtonClass": "btn btn-white btn-primary  btn-bold",
                        "sButtonText": "<i class='fa fa-file-excel-o bigger-110 green'></i>"
                    },

                    {
                        "sExtends": "pdf",
                        "sToolTip": "Export to PDF",
                        "sButtonClass": "btn btn-white btn-primary  btn-bold",
                        "sButtonText": "<i class='fa fa-file-pdf-o bigger-110 red'></i>"
                    },

                    {
                        "sExtends": "print",
                        "sToolTip": "Print view",
                        "sButtonClass": "btn btn-white btn-primary  btn-bold",
                        "sButtonText": "<i class='fa fa-print bigger-110 grey'></i>",

                        "sMessage": "<div class='navbar navbar-default'><div class='navbar-header pull-left'><a class='navbar-brand' href='#'><small>Optional Navbar &amp; Text</small></a></div></div>",

                        "sInfo": "<h3 class='no-margin-top'>Print view</h3>\
									  <p>Please use your browser's print function to\
									  print this table.\
									  <br />Press <b>escape</b> when finished.</p>",
                    }
                ]
            });
            //we put a container before our table and append TableTools element to it
            $(tableTools_obj.fnContainer()).appendTo($('.tableTools-container'));

            //also add tooltips to table tools buttons
            //addding tooltips directly to "A" buttons results in buttons disappearing (weired! don't know why!)
            //so we add tooltips to the "DIV" child after it becomes inserted
            //flash objects inside table tools buttons are inserted with some delay (100ms) (for some reason)
            setTimeout(function () {
                $(tableTools_obj.fnContainer()).find('a.DTTT_button').each(function () {
                    var div = $(this).find('> div');
                    if (div.length > 0) div.tooltip({container: 'body'});
                    else $(this).tooltip({container: 'body'});
                });
            }, 200);


            //ColVis extension
            var colvis = new $.fn.dataTable.ColVis(oTable1, {
                "buttonText": "<i class='fa fa-search'></i>",
                "aiExclude": [0, 6],
                "bShowAll": true,
                //"bRestore": true,
                "sAlign": "right",
                "fnLabel": function (i, title, th) {
                    return $(th).text();//remove icons, etc
                }

            });

            //style it
            $(colvis.button()).addClass('btn-group').find('button').addClass('btn btn-white btn-info btn-bold')

            //and append it to our table tools btn-group, also add tooltip
            $(colvis.button())
                .prependTo('.tableTools-container .btn-group')
                .attr('title', 'Show/hide columns').tooltip({container: 'body'});

            //and make the list, buttons and checkboxed Ace-like
            $(colvis.dom.collection)
                .addClass('dropdown-menu dropdown-light dropdown-caret dropdown-caret-right')
                .find('li').wrapInner('<a href="javascript:void(0)" />') //'A' tag is required for better styling
                .find('input[type=checkbox]').addClass('ace').next().addClass('lbl padding-8');


            /////////////////////////////////
            //table checkboxes
            $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

            //select/deselect all rows according to table header checkbox
            $('#dynamic-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $(this).closest('table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) tableTools_obj.fnSelect(row);
                    else tableTools_obj.fnDeselect(row);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
                var row = $(this).closest('tr').get(0);
                if (!this.checked) tableTools_obj.fnSelect(row);
                else tableTools_obj.fnDeselect($(this).closest('tr').get(0));
            });


            $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
                e.stopImmediatePropagation();

                e.stopPropagation();
                e.preventDefault();
            });


            //And for the first simple table, which doesn't have TableTools or dataTables
            //select/deselect all rows according to table header checkbox
            var active_class = 'active';
            $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
                var th_checked = this.checked;//checkbox inside "TH" table header

                $(this).closest('table').find('tbody > tr').each(function () {
                    var row = this;
                    if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                    else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
                });
            });

            //select/deselect a row when the checkbox is checked/unchecked
            $('#simple-table').on('click', 'td input[type=checkbox]', function () {
                var $row = $(this).closest('tr');
                if (this.checked) $row.addClass(active_class);
                else $row.removeClass(active_class);
            });


            if (!ace.vars['touch']) {
                $('.chosen-select').chosen({allow_single_deselect: true});
                //resize the chosen on window resize

                $(window)
                    .off('resize.chosen')
                    .on('resize.chosen', function () {
                        $('.chosen-select').each(function () {
                            var $this = $(this);
                            $this.next().css({'width': $this.parent().width()});
                        })
                    }).trigger('resize.chosen');
                //resize chosen on sidebar collapse/expand
                $(document).on('settings.ace.chosen', function (e, event_name, event_val) {
                    if (event_name != 'sidebar_collapsed') return;
                    $('.chosen-select').each(function () {
                        var $this = $(this);
                        $this.next().css({'width': $this.parent().width()});
                    })
                });


                $('#chosen-multiple-style .btn').on('click', function (e) {
                    var target = $(this).find('input[type=radio]');
                    var which = parseInt(target.val());
                    if (which == 2) $('#form-field-select-4').addClass('tag-input-style');
                    else $('#form-field-select-4').removeClass('tag-input-style');
                });
            }


            /********************************/
            //add tooltip for small view action buttons in dropdown menu
            $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

            //tooltip placement on right or left
            function tooltip_placement(context, source) {
                var $source = $(source);
                var $parent = $source.closest('table')
                var off1 = $parent.offset();
                var w1 = $parent.width();

                var off2 = $source.offset();
                //var w2 = $source.width();

                if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
                return 'left';
            }

        })
    </script>


    <script type="text/javascript">
        <!--
        var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn: ["change"]});
        //-->
    </script>
    </body>
    </html>
<?php

?>