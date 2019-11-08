<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$error_password = "Пароль неверный";
$error_user_not_found = "Пользователь с таким email не найден";

$email = $_GET["email"];
$pass = $_GET["pass"];

$user = R::findOne('users', 'email = ?',  array($email));

// $user->password = password_hash($pass, PASSWORD_DEFAULT);
// R::store($user);
// echo '$user';

if( $user ){
    if(password_verify($pass, $user->password)){
        echo $user->token;
    } else {
        echo $error_password;
    }
}
else{
    echo $error_user_not_found;
}

?>