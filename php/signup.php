<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$error_username = "Пользователь с таким username уже существует";
$error_email = "Пользователь с таким email уже существует";

$email = $_POST["email"];
$username = $_POST["username"];
$pass = $_POST["pass"];

if( R::count('users','username = ?', array($username)) > 0 ){
    echo $error_username;
} elseif ( R::count('users','email = ?', array($email)) > 0 ) {
    echo $error_email;
} else {

$token = $email . "_token_" . $pass;

$user = R::dispense('users');
$user->email = $email;
$user->username = $username;
$user->password = password_hash($pass, PASSWORD_DEFAULT);

$user->token = $token;

R::store($user);
// echo $id ." ".$email;
echo $token;
}

?>