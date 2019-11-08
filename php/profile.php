<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$id_edit = $_POST['id'];
$action = $_POST['action'];
$username = $_POST['username'];
$firstName = $_POST['firstName'];
$secondName = $_POST['secondName'];
$instagram = $_POST['instagram'];
$country = $_POST['country'];
$city = $_POST['city'];

$token = $_GET["token"];

if ($action == "edit") {
// $query = "UPDATE users SET nickname = '$nickName', 
// 							firstName = '$firstName',  
// 							secondName = '$secondName',
// 							instagram = '$instagram',
// 							country = '$country',
// 							city = '$city'
// 							WHERE id='$id_edit'";
// $update = mysqli_query($connection, $query);

} else {
	$user = R::findOne('users', 'token = ?', array($token));

	echo json_encode($user);

	// $query = "SELECT * FROM users WHERE id='$id'";
	// $profile = mysqli_query($connection, $query);

	// while ($temp = mysqli_fetch_assoc($profile)) {
	// 	echo json_encode($temp);
	// }
} 

?>

