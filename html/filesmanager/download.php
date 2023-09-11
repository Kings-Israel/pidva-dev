<?php
$file_name = $_GET['filename'];
$file_url = $file_name;
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$file_name."\""); 
readfile($file_url);
$previous = "javascript:history.go(-1)";
if(isset($_SERVER['HTTP_REFERER'])) {
  $previous = $_SERVER['HTTP_REFERER'];
}
header(sprintf("Location: %s", $previous));
?>
