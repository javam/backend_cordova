<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$token = $_GET['token'];

$liked = $_GET['liked'];
$vote = $_GET['vote'];

$user = R::findOne('users', 'token = ?',  array($token));

// TODO: Переделать на сравнение с последним id в таблице swipe
if( $user['last_id'] == R::count('swipe') ) { echo json_encode("end.jpg"); }
	
else{ 
	$swipe = R::findOne('swipe', 'status = 1 AND user_id != ? AND id > ?', array($user->id, $user['last_id']));
	$photoSRC = R::findOne('photos', 'id = ?', array($swipe['photo_id']))['src'];

	$userData = R::findOne('users','id = ?', array($swipe['user_id']));
	$instagramLink = $userData['instagram'];
	$profileLink = $userData['username'];


	if( $swipe['views_all'] > $min_view_points_for_raiting ) 
	{
		$rating = $swipe['likes'] / $swipe['views_all'];
	}
	else $rating = 0;

	// заглушка для повторения фото
	if($swipe['id']==6) {$user->last_id = 0;}
	else {$user->last_id = $swipe['id'];}

	if($vote > 0) {$user->view_points = ++$user->view_points;}
	
	R::store($user);

	$swipe->views_left = --$swipe->views_left;
	$swipe->views_all = ++$swipe->views_all;

	if($liked == "like") $swipe->likes = ++$swipe->likes;

	if($swipe['views_left'] == 0) $swipe->status = 0;

	R::store($swipe);

	echo json_encode( array($photoSRC, $instagramLink, $profileLink, $rating) );
	// echo json_encode($photoSRC['src']);
}

?>