<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$tokenInput = $_POST["token"];
$token = R::findOne('users','token = ?', array($tokenInput));
if ($token) echo "ok";

// echo $token;

?>