<?php

	require "../lib/rb-mysql.php"; 

R::setup( 'mysql:host=127.0.0.1;dbname=trender', 'root', '' ); 


	if(!R::testConnection()){
		exit('Нет подключения к базе данных');
	}

	R::freeze(true);


// $connection = mysqli_connect(
// 	$config['db']['server'],
// 	$config['db']['login'],
// 	$config['db']['password'],
// 	$config['db']['name']
// );

// if ( $connection == false ){
// 	echo "Connection error!<br>";
// 	echo mysqli_connect_error();
// 	exit();
// }

?>