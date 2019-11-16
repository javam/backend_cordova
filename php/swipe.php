<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$token = $_POST['token'];
$liked = $_POST['liked'];
$prev_id = $_POST['prev_id'];

$user = R::findOne('users', 'token = ?', array($token));

// TODO: Переделать на сравнение с последним id в таблице swipe
if( $user['last_id'] == R::count('swipe') ) { echo json_encode("end.jpg"); }
	
else{ 
	// проверка на первое фото
	if($liked == 'first') {
		$swipe = R::findOne('swipe', 'status = 1 AND user_id != ? AND id >= ?', array($user->id, $user->last_id));
	}
	else {
		$swipe = R::findOne('swipe', 'status = 1 AND user_id != ? AND id > ?', array($user->id, $user->last_id));
	}

	// получаем адрес фото
	$photoSRC = R::findOne('photos', 'id = ?', array($swipe['photo_id']))['src'];

	// полуаем информацию о хозяине фото
	$userData = R::findOne('users','id = ?', array($swipe['user_id']));
	$instagramLink = $userData['instagram'];
	$profileLink = $userData['username'];

	// заглушка для повторения фото
	$user->last_id = $swipe->id == 6 ? 0 : $swipe->id;

	// если фото не первое, то к предыдущему применяется добавление данных в базу
	if($liked !== 'first'){
		$swipe_prev = R::findOne('swipe','id= ?',array($prev_id));

		// проверка, обрывает ли ошибка в оценке фото цепь угадываний. Сейчас минимум 20 показов.
		// и если да, то вычисляется рейтинг фото
		// если нет, то рейтинг = 0 
		$rating = $swipe_prev['views_all'] > $min_view_points_for_raiting ? $swipe_prev['likes'] / $swipe_prev['views_all'] : 0;

		// если угадал, получает + очко
		if ( ($liked == "like" && $rating >= 0.6) || ($liked == "dislike" && $rating < 0.6) || $rating == 0 ) {
			$user->view_points = ++$user->view_points;
			$guess = 'yes';
		  } else {
			$guess = "no";
		  }

		// $t1 = $swipe_prev['views_left']--;
		// $t2 = $swipe_prev['views_all']++;

		// $swipe_prev->views_left = $t1;
		// $swipe_prev->views_all = $t2;

		$swipe_prev->views_left = --$swipe_prev->views_left;
		$swipe_prev->views_all = ++$swipe_prev->views_all;

		if($liked == "like") $swipe_prev->likes = ++$swipe_prev->likes;

		if($swipe_prev['views_left'] == 0) $swipe_prev->status = 0;
		
		R::store($user);
		R::store($swipe_prev);
	}

	echo json_encode( array($photoSRC, $instagramLink, $profileLink, $guess, $swipe['id']) );
	// echo json_encode( array($photoSRC, $instagramLink, $profileLink, $guess, $swipe['id'],"last_id = ".$user['last_id'],$liked,"prev_id = ".$prev_id) );
	// echo json_encode($photoSRC['src']);
}

?>