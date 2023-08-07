<?php require_once('../../Connections/connect.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        return "'$theValue'";


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
$colname_getmoduleid = "-1";
if (isset($_GET['moduleid'])) {
    $colname_getmoduleid = $_GET['moduleid'];
}

$errorcode = '';


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "checkindb")) {

    if (isset($_POST['student_token'])) {

        $student_token = strtoupper($_POST['student_token']);

        if (is_uploaded_file($_FILES['certificate_photo']['tmp_name'])) {

            date_default_timezone_set('Africa/Nairobi');
            $date_insert = date('dmYhis');
            $a = "ED-" . $_POST['student_token'] . "-" . $_POST['search_id'] . "-" . $date_insert;
            $rawname = $_FILES['certificate_photo']['name'];
            "Upload: " . $a . "_" . $_FILES["certificate_photo"]["name"];
            $file = "educationcertificates/" . $a . "_" . $_FILES["certificate_photo"]["name"];

            require_once "../../uploads.php";
            $prefix = "certificate_photo";
            $filenameuploaded = uploadFile($prefix, "individual-educationcertificates", $a . "_" . $_FILES[$prefix]["name"]);


        } else {

            $filenameuploaded = "";

        }

        mysqli_select_db($connect, $database_connect);

        $datetoday = date('Y-m-d');

        $query_getstudent = "SELECT * FROM pel_edu_data WHERE student_token = '$student_token' and student_status = '11'";

        $getstudent = mysqli_query_ported($query_getstudent, $connect) or die(mysqli_error($connect));
        $row_getstudent = mysqli_fetch_assoc($getstudent);
        $totalRows_getstudent = mysqli_num_rows($getstudent);

        if ($totalRows_getstudent > 0) {

            $updateSQL = sprintf("UPDATE pel_psmt_edu_data SET edu_name=%s, edu_institution=%s, status=%s, date_added=%s, added_by=%s, edu_course=%s,edu_specialization=%s,student_token=%s, data_source=%s, edu_award=%s, edu_graduation_year=%s, certificate_photo=%s, data_notes=%s WHERE edu_id=%s",
                GetSQLValueString(mysqli_real_escape_string($connect,strtoupper($row_getstudent['student_first_name']." ".$row_getstudent['student_second_name']." ".$row_getstudent['student_third_name'])), "text"),
                GetSQLValueString(strtoupper($row_getstudent['institution_name']), "text"),
                GetSQLValueString($_POST['status'], "text"),
                GetSQLValueString($_POST['date_added'], "text"),
                GetSQLValueString($_POST['added_by'], "text"),
                GetSQLValueString($row_getstudent['course_name'], "text"),
                GetSQLValueString($row_getstudent['student_specialization'], "text"),
                GetSQLValueString($student_token, "text"),
                GetSQLValueString($row_getstudent['data_source'], "text"),
                GetSQLValueString($row_getstudent['award'], "text"),
                GetSQLValueString($row_getstudent['graduation_date'], "text"),
                GetSQLValueString($filenameuploaded, "text"),
                GetSQLValueString($_POST['data_notes'], "text"),
                GetSQLValueString($_POST['edu_id'], "int"));

            error_log("GOT SQL $updateSQL ");

            mysqli_select_db($connect, $database_connect);
            mysqli_query_ported($updateSQL, $connect);

            $colname_getrequestid = $_POST['request_id'];
            $colname_getmoduleid = $_POST['moduleid'];

//echo $Result1 = mysqli_query_ported($insertSQL, $connect)or die(mysqli_error($connect));
            if (mysqli_error($connect)) {

                //error_log("GOT error here ".mysqli_error($connect));

                $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

										 Details of the Student Data havent been added.
											<br />
										</div>';

            } else {

                $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
                /* if (isset($_SERVER['QUERY_STRING'])) {
                   $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
                   $updateGoTo .= $_SERVER['QUERY_STRING'];
                 }*/
                header(sprintf("Location: %s", $updateGoTo));
            }

        } else {
            $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

										 No Details of Student found Kindly go and Upload Education Details
											<br />
										</div>';

        }
    }
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editmatchdetails")) {

    $updateSQL = sprintf("UPDATE pel_psmt_edu_data SET match_status_name=%s, status=%s, date_added=now(), added_by=%s, match_status_insititution=%s, match_status_course=%s, match_status_award=%s, match_status_year=%s,match_status_specialization=%s WHERE edu_id=%s",
        GetSQLValueString(strtoupper($_POST['match_status_name']), "text"),
        GetSQLValueString($_POST['status'], "text"),
        GetSQLValueString($_POST['added_by'], "text"),
        GetSQLValueString(strtoupper($_POST['match_status_insititution']), "text"),
        GetSQLValueString(strtoupper($_POST['match_status_course']), "text"),
        GetSQLValueString(strtoupper($_POST['match_status_award']), "text"),
        GetSQLValueString(strtoupper($_POST['match_status_year']), "text"),
        GetSQLValueString(strtoupper($_POST['match_status_specialization']), "text"),
        GetSQLValueString($_POST['edu_id'], "int"));

    mysqli_select_db($connect, $database_connect);
    mysqli_query_ported($updateSQL, $connect);

    $colname_getrequestid = $_POST['request_id'];
    $colname_getmoduleid = $_POST['moduleid'];

    if (mysqli_error($connect)) {
        $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												ERROR!!!!!
											</strong>

											 Details of the Data Provided were not updated succesfully.
											<br />
										</div>';

    } else {

        $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
        /* if (isset($_SERVER['QUERY_STRING'])) {
           $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
           $updateGoTo .= $_SERVER['QUERY_STRING'];
         }*/
        header(sprintf("Location: %s", $updateGoTo));
    }
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editdetails")) {

    $updateSQL = sprintf("UPDATE pel_psmt_edu_data SET education_level = %s, name_provided=%s, status=%s, date_added=now(), added_by=%s, institution_provided=%s, data_source_provided=%s, course_provided=%s, award_provided=%s, year_provided=%s,specialization_provided=%s,country=%s WHERE edu_id=%s",
        GetSQLValueString(strtoupper($_POST['education_level']), "text"),
        GetSQLValueString(strtoupper($_POST['name_provided']), "text"),
        GetSQLValueString($_POST['status'], "text"),
        GetSQLValueString($_POST['added_by'], "text"),
        GetSQLValueString(strtoupper($_POST['institution_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['data_source_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['course_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['award_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['year_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['specialization_provided']), "text"),
        GetSQLValueString($_POST['country'], "text"),
        GetSQLValueString($_POST['edu_id'], "int"));

    mysqli_select_db($connect, $database_connect);
    mysqli_query_ported($updateSQL, $connect);

    $colname_getrequestid = $_POST['request_id'];
    $colname_getmoduleid = $_POST['moduleid'];

    if (mysqli_error($connect)) {

        error_log("GOT error updating data ".mysqli_error($connect));

        $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												ERROR!!!!!
											</strong>

											 Details of the Data Provided were not updated succesfully.
											<br />
										</div>';

    } else {

        $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
        /* if (isset($_SERVER['QUERY_STRING'])) {
           $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
           $updateGoTo .= $_SERVER['QUERY_STRING'];
         }*/
        header(sprintf("Location: %s", $updateGoTo));
    }
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "newdetails")) {

    $insertSQL = sprintf("INSERT INTO pel_psmt_edu_data (education_level,name_provided, status, date_added, added_by, institution_provided, data_source_provided, course_provided, award_provided, year_provided,specialization_provided, country, search_id) VALUES (%s, %s, %s, now(), %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString(strtoupper($_POST['education_level']), "text"),
        GetSQLValueString(strtoupper($_POST['name_provided']), "text"),
        GetSQLValueString($_POST['status'], "text"),
        GetSQLValueString($_POST['added_by'], "text"),
        GetSQLValueString(strtoupper($_POST['institution_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['data_source_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['course_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['award_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['year_provided']), "text"),
        GetSQLValueString(strtoupper($_POST['specialization_provided']), "text"),
        GetSQLValueString($_POST['country'], "text"),
        GetSQLValueString($_POST['search_id'], "text"));

    mysqli_select_db($connect, $database_connect);
    mysqli_query_ported($insertSQL, $connect);
    $colname_getrequestid = $_POST['request_id'];
    $colname_getmoduleid = $_POST['moduleid'];

//echo $Result1 = mysqli_query_ported($insertSQL, $connect)or die(mysqli_error($connect));
    if (mysqli_error($connect)) {
        $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

										 Details of the Provided Data havent been added.
											<br />
										</div>';

    } else {
        $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
        /* if (isset($_SERVER['QUERY_STRING'])) {
           $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
           $updateGoTo .= $_SERVER['QUERY_STRING'];
         }*/
        header(sprintf("Location: %s", $updateGoTo));
    }

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addreject")) {

    $deleteSQLreject = sprintf("UPDATE pel_psmt_edu_data SET status=%s, review_status=%s, verified_by=%s, verified_date=now(), review_notes=%s WHERE search_id=%s and edu_id=%s",
        GetSQLValueString('00', "text"),
        GetSQLValueString($_POST['review_status'], "text"),
        GetSQLValueString($_POST['verified_by'], "text"),
        GetSQLValueString($_POST['review_notes'], "text"),
        GetSQLValueString($_POST['search_id'], "int"),
        GetSQLValueString($_POST['edu_id'], "int"));
    mysqli_select_db($connect, $database_connect);
    $Result1reject = mysqli_query_ported($deleteSQLreject, $connect) or die(mysqli_error($connect));


    $deleteSQL3 = sprintf("UPDATE pel_psmt_request_modules SET status=%s WHERE module_id=%s AND request_ref_number=%s",
        GetSQLValueString('00', "text"),
        GetSQLValueString($colname_getmoduleid, "text"),
        GetSQLValueString($_POST['search_id'], "int"));
    mysqli_select_db($connect, $database_connect);
    $Result3 = mysqli_query_ported($deleteSQL3, $connect) or die(mysqli_error($connect));


    $colname_getrequestid = $_POST['request_id'];
    $colname_getmoduleid = $_POST['moduleid'];

//echo $Result1 = mysqli_query_ported($insertSQL, $connect)or die(mysqli_error($connect));
    if (mysqli_error($connect)) {
        $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

										 Reject Review details not added Error!.
											<br />
										</div>';

    } else {
        $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
        /* if (isset($_SERVER['QUERY_STRING'])) {
           $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
           $updateGoTo .= $_SERVER['QUERY_STRING'];
         }*/
        header(sprintf("Location: %s", $updateGoTo));
    }
}

if ((isset($_GET['edu_id'])) && ($_GET['edu_id'] != "")) {

    if ($_GET['status'] == '00') {

        $deleteSQL = sprintf("UPDATE pel_psmt_edu_data SET status=%s, added_by=%s, date_added=now() WHERE edu_id=%s",
            GetSQLValueString($_GET['status'], "text"),
            GetSQLValueString($_GET['fullnames'], "text"),
            GetSQLValueString($_GET['edu_id'], "int"));
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

										 Details of the Status were not updated havent been added.
											<br />
										</div>';

        } else {
            $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
            /* if (isset($_SERVER['QUERY_STRING'])) {
               $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
               $updateGoTo .= $_SERVER['QUERY_STRING'];
             }*/
            header(sprintf("Location: %s", $updateGoTo));
        }
    }

    else if ($_GET['status'] == '22') {

        $deleteSQL = sprintf("UPDATE pel_psmt_edu_data SET status=%s, added_by=%s, date_added =now() WHERE edu_id=%s",
            GetSQLValueString($_GET['status'], "text"),
            GetSQLValueString($_GET['fullnames'], "text"),
            GetSQLValueString($_GET['edu_id'], "int"));
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

										 Details of the DL data havent been added.
											<br />
										</div>';

        } else {
            $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
            /* if (isset($_SERVER['QUERY_STRING'])) {
               $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
               $updateGoTo .= $_SERVER['QUERY_STRING'];
             }*/
            header(sprintf("Location: %s", $updateGoTo));
        }
    }

}

//to approve data

if ((isset($_GET['search_id_approve'])) && ($_GET['search_id_approve'] != "")) {

    if ($_GET['status'] == '11') {

        $deleteSQL2 = sprintf("UPDATE pel_psmt_edu_data SET status=%s, review_status=%s, verified_by=%s, verified_date=now() WHERE search_id=%s and edu_id=%s",
            GetSQLValueString($_GET['status'], "text"),
            GetSQLValueString("APPROVED", "text"),
            GetSQLValueString($_GET['fullnames'], "text"),
            GetSQLValueString($_GET['search_id_approve'], "int"),
            GetSQLValueString($_GET['edu_id'], "int"));
        mysqli_select_db($connect, $database_connect);

        error_log("got deleteSQL2 $deleteSQL2");

        $Result2 = mysqli_query_ported($deleteSQL2, $connect) or die(mysqli_error($connect));


        $deleteSQL3 = sprintf("UPDATE pel_psmt_request_modules SET status=%s WHERE module_id=%s AND request_ref_number=%s",
            GetSQLValueString('11', "text"),
            GetSQLValueString($colname_getmoduleid, "text"),
            GetSQLValueString($_GET['search_id_approve'], "text"));
        mysqli_select_db($connect, $database_connect);

        error_log("got deleteSQL3 $deleteSQL3");

        $Result3 = mysqli_query_ported($deleteSQL3, $connect) or die(mysqli_error($connect));

        if (mysqli_error($connect)) {
            $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

										 Details of the data havent been approved
											<br />
										</div>';

        } else {
            $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
            /* if (isset($_SERVER['QUERY_STRING'])) {
               $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
               $updateGoTo .= $_SERVER['QUERY_STRING'];
             }*/


            header(sprintf("Location: %s", $updateGoTo));
        }
    }

    else if (isset($_GET['verification_status'])) {

        $verification_status_comments = isset($_POST['verification_status_comments']) ? $_POST['verification_status_comments'] : "";

        if($_GET['verification_status'] == "1") {

            $verification_status_comments = "";
        }

        $deleteSQL2 = sprintf("UPDATE pel_psmt_edu_data SET verification_status=%s,verification_status_comments=%s WHERE search_id=%s and edu_id=%s",
            GetSQLValueString($_GET['verification_status'], "text"),
            GetSQLValueString($verification_status_comments, "text"),
            GetSQLValueString($_GET['search_id_approve'], "int"),
            GetSQLValueString($_GET['edu_id'], "int"));
        mysqli_select_db($connect, $database_connect);

        error_log("got deleteSQL2 $deleteSQL2");

        $Result2 = mysqli_query_ported($deleteSQL2, $connect) or die(mysqli_error($connect));


        $deleteSQL3 = sprintf("UPDATE pel_psmt_request_modules SET status=%s WHERE module_id=%s AND request_ref_number=%s",
            GetSQLValueString('11', "text"),
            GetSQLValueString($colname_getmoduleid, "text"),
            GetSQLValueString($_GET['search_id_approve'], "text"));
        mysqli_select_db($connect, $database_connect);

        error_log("got deleteSQL3 $deleteSQL3");

        $Result3 = mysqli_query_ported($deleteSQL3, $connect) or die(mysqli_error($connect));

        if (mysqli_error($connect)) {
            $errorcode = '<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

										 Details of the data havent been updated
											<br />
										</div>';

        } else {
            $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
            /* if (isset($_SERVER['QUERY_STRING'])) {
               $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
               $updateGoTo .= $_SERVER['QUERY_STRING'];
             }*/


            header(sprintf("Location: %s", $updateGoTo));
        }
    }

}


if ((isset($_GET['edu_id'])) && ($_GET['edu_id'] != "")) {

    if ($_GET['status'] == '00') {
        $deleteSQL = sprintf("UPDATE pel_psmt_edu_data SET status=%s, added_by=%s, date_added=now() WHERE edu_id=%s",
            GetSQLValueString($_GET['status'], "text"),
            GetSQLValueString($_GET['fullnames'], "text"),
            GetSQLValueString($_GET['edu_id'], "int"));
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

										 Details of the comments have not been updated.
											<br />
										</div>';

        } else {
            $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
            /* if (isset($_SERVER['QUERY_STRING'])) {
               $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
               $updateGoTo .= $_SERVER['QUERY_STRING'];
             }*/
            header(sprintf("Location: %s", $updateGoTo));
        }
    }

    if ($_GET['status'] == '22') {
        $deleteSQL = sprintf("UPDATE pel_psmt_edu_data SET status=%s, added_by=%s, date_added=now() WHERE edu_id=%s",
            GetSQLValueString($_GET['status'], "text"),
            GetSQLValueString($_GET['fullnames'], "text"),
            GetSQLValueString($_GET['edu_id'], "int"));
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
            $updateGoTo = "educationcheck.php?request_id=$colname_getrequestid&moduleid=$colname_getmoduleid";
            /* if (isset($_SERVER['QUERY_STRING'])) {
               $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
               $updateGoTo .= $_SERVER['QUERY_STRING'];
             }*/
            header(sprintf("Location: %s", $updateGoTo));
        }
    }

}


?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <meta charset="utf-8"/>
        <title>Individual Education Details Data Management - Peleza Admin</title>

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

        <!--[if lte IE 9]>
        <link rel="stylesheet" href="../../assets/css/ace-part2.css" class="ace-main-stylesheet"/>
        <![endif]-->

        <!--[if lte IE 9]>
        <link rel="stylesheet" href="../../assets/css/ace-ie.css"/>
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
        <div id="sidebar" class="sidebar                  responsive">
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
                   data-icon2="ace-icon fa fa-angle-double-right"></i></div>

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
                            <a href="#">Home</a></li>

                        <li>
                            <a href="#">Peleza Modules</a></li>

                        <li>
                            <a href="#">Individual</a></li>

                        <li class="active">Individual Education Details</li>
                    </ul><!-- /.breadcrumb -->

                    <!-- #section:basics/content.searchbox -->
                    <div class="nav-search" id="nav-search">
                    </div><!-- /.nav-search -->

                    <!-- /section:basics/content.searchbox -->
                </div>

                <!-- /section:basics/content.breadcrumbs -->
                <div class="page-content" id="education">

                    <div class="row">

                        <div class="col-xs-12">

                            <div class="row">

                                <div class="col-xs-12">
                                    <h3 align="left" class="header smaller lighter blue">Add NTSA Details</h3>
                                </div>

                                <?php

                                $query_getstudent = "SELECT * FROM pel_psmt_request WHERE request_id = " . $colname_getrequestid . "";
                                $getstudent = mysqli_query_ported($query_getstudent, $connect) or die(mysqli_error($connect));
                                $row_getstudent = mysqli_fetch_assoc($getstudent);
                                $totalRows_getstudent = mysqli_num_rows($getstudent);

                                ?>

                                <h3 align="left" class=" smaller lighter blue"><strong>SEARCH REF: </strong> <?php echo $row_getstudent['request_ref_number']; ?></h3>

                                <div>

                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                        <thead>

                                        <tr>

                                            <th>Dataset Name</th>
                                            <th>Client Name</th>

                                            <th>Request Package</th>
                                            <th>Request Date</th>

                                            <th class="hidden-480">Status</th>

                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>
                                                <a href="#"><?php echo $row_getstudent['bg_dataset_name']; ?></a></td>
                                            <td><?php echo $row_getstudent['client_name']; ?></td>

                                            <td><?php echo $row_getstudent['request_plan']; ?></td>
                                            <td><?php echo $row_getstudent['request_date']; ?></td>

                                            <td class="hidden-480">  <?php

                                                if ($row_getstudent['verification_status'] == '44') {
                                                    ?>
                                                    <span class="label label-sm label-warning">In Progress</span>
                                                    <?php
                                                }

                                                if ($row_getstudent['verification_status'] == '00') {
                                                    ?>
                                                    <span class="label label-sm label-purple">New Request</span>
                                                    <?php
                                                }

                                                if ($row_getstudent['verification_status'] == '11') {
                                                    ?>
                                                    <span class="label label-sm label-success">Final</span>
                                                    <?php
                                                }

                                                if ($row_getstudent['verification_status'] == '22') {
                                                    ?>
                                                    <span class="label label-sm label-warning">Not Reviewed</span>
                                                    <?php
                                                }

                                                if ($row_getstudent['verification_status'] == '33') {
                                                    ?>
                                                    <span class="label label-sm label-primary">Interim Data</span>
                                                    <?php
                                                }

                                                ?>    </td>


                                        </tr>

                                        </tbody>
                                    </table>

                                    <hr/>
                                    <div class="col-xs-12">
                                        <?php

                                        echo $errorcode;
                                        ?>
                                    </div>

                                    <?php

                                        $search_ref = $row_getstudent['request_ref_number'];
                                        $number_plate = "";

                                        $query_getdetails = "select * from pel_psmt_files where data_type = 'text' AND request_id = ".$row_getstudent['request_id']."";

                                        $getdetails = mysqli_query_ported($query_getdetails, $connect) or die(mysqli_error($connect));

                                        $row_getdetails = mysqli_fetch_assoc($getdetails);
                                        $totalRows_getdetails = mysqli_num_rows($getdetails);

                                        if ($totalRows_getdetails > 0) {

                                            $number_plate = $row_getdetails['psmtfile_name'];
                                            $number_plate = strtoupper(str_replace(" ","",$number_plate));

                                        }

                                    ?>

                                    <h3 align="left" class=" smaller lighter blue"><strong>NUMBER PLATE: </strong><span id="number_plate"><?= $number_plate ?></span></h3>

                                    <table class="table table-bordered">

                                        <thead>
                                            <tr>
                                                <th>Key</th>
                                                <th>Value</th>
                                                <th>Key</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td>
                                                Ref Number
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.ref_number"></strong>
                                            </td>

                                            <td>
                                                Date Verified
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.date"></strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Registration Date
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.registration_date"></strong>
                                            </td>

                                            <td>
                                                Chasis Number
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.chassis_number"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Customs Entry Number
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.customs_entry_number"></strong>
                                            </td>

                                            <td>
                                                Type of Vehicle
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.type_of_vehicle"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Body Type
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.body_type"></strong>
                                            </td>

                                            <td>
                                                Date of Manufacture
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.date_of_manufacture"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Body Color
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.body_colour"></strong>
                                            </td>

                                            <td>
                                                Make
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.make"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Number of Axles
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.number_of_axles"></strong>
                                            </td>

                                            <td>
                                                Vehicle Model
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.vehicle_model"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Engine Number
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.engine_number"></strong>
                                            </td>

                                            <td>
                                                Fuel type
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.fuel_type"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Rating (CC)
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.rating"></strong>
                                            </td>

                                            <td>
                                                Tare Weight (KGs)
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.tare_weight"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Load Capacity (KGs)
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.load_capacity"></strong>
                                            </td>

                                            <td>
                                                Number of Passengers
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.number_of_passengers"></strong>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>
                                                Vehicle Under Caveat
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.vehicle_under_caveat"></strong>
                                            </td>

                                            <td>
                                                Conditions
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.conditions"></strong>
                                            </td>
                                        </tr>


                                        <tr>

                                            <td>
                                                Logbook No
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.logbook_no"></strong>
                                            </td>

                                            <td>
                                                Logbook Serial
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.logbook_serial_no"></strong>
                                            </td>
                                        </tr>


                                        <tr>

                                            <td>
                                                Drive Side
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.drive_side"></strong>
                                            </td>

                                            <td>
                                                Created By
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.created_by"></strong>
                                            </td>
                                        </tr>

                                        <tr>

                                            <td>
                                                Approved By
                                            </td>
                                            <td>
                                                <strong v-text="vehicle.approved_by"></strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th colspan="4">
                                                Current Owners
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>
                                                ID
                                            </th>
                                            <th>
                                                NAME
                                            </th>
                                            <th>
                                                EMAIL
                                            </th>
                                            <th>
                                                PIN
                                            </th>
                                        </tr>
                                        <tr v-for="o in vehicle.current_owners">
                                            <td v-text="o.id_number"></td>
                                            <td v-text="o.name"></td>
                                            <td v-text="o.email"></td>
                                            <td v-text="o.pin"></td>
                                        </tr>


                                        <tr>
                                            <th colspan="4">
                                                Previous Owners
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>
                                                ID
                                            </th>
                                            <th>
                                                NAME
                                            </th>
                                            <th>
                                                EMAIL
                                            </th>
                                            <th>
                                                PIN
                                            </th>
                                        </tr>
                                        <tr v-for="o in vehicle.previous_owners">
                                            <td v-text="o.id_number"></td>
                                            <td v-text="o.name"></td>
                                            <td v-text="o.email"></td>
                                            <td v-text="o.pin"></td>
                                        </tr>

                                        </tbody>
                                    </table>

                                    <?php
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
							Admin &copy; 2018						</span>

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


    <!--    vueJS-->

    <script src="../../v1/js/axios.min.js"></script>
    <script src="../../v1/js/moment.js"></script>
    <script src="../../v1/js/vue/vue-2.6.min.js"></script>

    <!--<script src="../../v1/js/vue-table/vue-resource.min.js"></script>-->
    <script src="../../v1/js/vue-table/vuetable-2.js"></script>
    <script src="../../v1/js/notifications/pnotify.min.js"></script>
    <script src="../../v1/js/notifications/noty.min.js"></script>
    <script src="../../v1/js/vehicle.js?<?= rand(1,1000) ?>"></script>

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


        jQuery(document).ready(function ($) {
            /** ******************************
             * Simple WYSIWYG
             ****************************** **/
            $('#editControls a').click(function (e) {
                e.preventDefault();
                switch ($(this).data('role')) {
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'p':
                        document.execCommand('formatBlock', false, $(this).data('role'));
                        break;
                    default:
                        document.execCommand($(this).data('role'), false, null);
                        break;
                }

                var textval = $("#editor").html();
                $("#editorCopy").val(textval);
            });

            $("#editor").keyup(function () {
                var value = $(this).html();
                $("#editorCopy").val(value);
            }).keyup();

            $('#checkIt').click(function (e) {
                e.preventDefault();
                alert($("#editorCopy").val());
            });
        });
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