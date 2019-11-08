<?php 
header("Access-Control-Allow-Origin: *");
include 'config.php';

$limit_photos = 5;

$type = $_GET["type"];
$page_num = $_GET['page_num'];

$token = $_GET["token"];
$photo_id = $_GET["photo_id"];

$user = R::findOne('users','token = ?',array($token));
//TODO: вынести бин likes сюда, но возможно нужна проверка на наличие photo_id

switch ($type) {
    case 'subscribes':
    $followers = R::findAll('followers','user_id = ?',array($user->id));
    foreach($followers as $follower){
        $followers_ids[] = $follower->follow_id;
    }

    $photos = R::findLike('photos',array('user_id' => $followers_ids),'LIMIT ?,?',array(($page_num*$limit_photos),$limit_photos));
    getPhoto($photos);
        break; 
    
    case 'flow':
    $photos=R::findAll('photos','ORDER BY id DESC LIMIT ?,?',array(($page_num*$limit_photos),$limit_photos));
    getPhoto($photos);
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
        foreach($photos as $row){
            $is_liked = R::findOne('likes','user_id = ? AND photo_id = ?',array($user->id, $row->id));
            $name = R::findOne('users','id = ?', array($row->user_id))['name'];
            $send[] = array($row->src, $name, $row->likes, $row->id, $is_liked->id);
        }   
    echo json_encode($send);
}

?>