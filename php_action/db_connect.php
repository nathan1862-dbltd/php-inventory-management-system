<?php 	

$localhost = "localhost";
$username = "thebertg_admin";
$password = "thebertg_admin";
$dbname = "thebertg_pos";
$store_url = "http://op.thebizportwebs.online/";
// db 
$connect = new mysqli($localhost, $username, $password, $dbname);
// check connection
if($connect->connect_error) {
  die("Connection Failed : " . $connect->connect_error);
} else {
  // echo "Successfully connected";
}

?>