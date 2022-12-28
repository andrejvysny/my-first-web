<?php 
$mailfrom = $_POST['name'];
$message = $_POST['message'];
$from_mail = "none";//$_POST['email'];
$today = date("d-m-Y_H:i");
//$today = date("Y-m-d H:i:s");
 

if($mailfrom!=""and$message!=""and$from_mail!=""){

$filename= date("m-d_H-i-s__").$mailfrom;
$msg = 'Date: '.$today."</br>\nFrom: ".$mailfrom."</br>\n\nMessage: ".$message;
$myfile = fopen("pages/mail/".$filename.".txt", "w") ;
fwrite($myfile, $msg);
fclose($myfile);
require 'pages/contact.php';echo '<script language="javascript">alert("Message successfully sent!")</script>';	}else {
	require 'pages/contact.php';
}