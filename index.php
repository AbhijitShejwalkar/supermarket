<?php
session_start();
require_once("dbcontroller.php");
require_once("checkout.php");
$db_handle = new DBController();
$checkout = new Checkout($db_handle);
include_once('supermakret_layout.php'); 
?>


