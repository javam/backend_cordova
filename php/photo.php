<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$limit_photos = 5;

$type = $_POST["type"];
$page_num = $_POST['page_num'];

$token = $_POST["token"];
$photo_id = $_POST["photo_id"];

$user = R::findOne('users','token = ?',array($token));
//TODO: вынести бин likes сюда, но возможно нужна проверка на наличие photo_id

switch ($type) {
    case 'subscribes':
    $followers = R::findAll('followers','user_id = ?',array($user->id));
    foreach($followers as $follower){
        $followers_ids[] = $follower->follow_id;
    }
    if($followers_ids){
    $photos = R::findLike('photos',array('user_id' => $followers_ids),' LIMIT ?,?',array(($page_num*$limit_photos),$limit_photos));
    getPhoto($photos);
    } 
    // echo json_encode($followers);
        break; 
    
    case 'flow':
    $photos=R::findAll('photos','ORDER BY id DESC LIMIT ?,?',array(($page_num*$limit_photos),$limit_photos));
    getPhoto($photos);
        break;

    case 'add_open':
    $photo=R::load('photos',$photo_id);
    $photo->open++;
    R::store($photo);
    break;    

    case 'view_photo':
    $photo=R::load('photos',$photo_id);
    $user=R::load('users',$photo->user_id);
    $send[] = array($photo, $user);
        echo json_encode($send);
    break;    

    case 'trend':
        # code...
    break;    
        
    case 'add_like':
        $likes = R::findOne('likes','user_id = ? AND photo_id = ?',array($user->id, $photo_id));
        $photo = R::load('photos',$photo_id);
        if(!$likes){
            $likes = R::dispense('likes');
            $likes->user_id = $user->id;
            $likes->photo_id = $photo_id;
            R::store($likes);
            $photo->likes++;
        }
        else{
            R::trash($likes);
            $photo->likes--;
        }            
        R::store($photo);        
        echo $photo->likes;
        break;
    
    default:
        # code...
        break;
}

function getPhoto($photos){   
    global $user;           
        foreach($photos as $photo){
            $photo = R::load('photos',$photo->id);
            $photo->views++;
            R::store($photo);
            $is_liked_by_user = R::findOne('likes','user_id = ? AND photo_id = ?',array($user->id, $photo->id));
            $name = R::findOne('users','id = ?', array($photo->user_id))['name'];
            $send[] = array($photo->src, $name, $photo->likes, $photo->id, $is_liked_by_user->id);
        }   
    echo json_encode($send);
}

?>