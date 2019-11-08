<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$tokenInput = $_GET["token"];
$token = R::findOne('users','token = ?', array($tokenInput));
if ($token) echo "ok";

// echo $token;

?>