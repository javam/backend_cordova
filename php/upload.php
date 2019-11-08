<?php
header("Access-Control-Allow-Origin: *");
include 'config.php';

$maxAllowComressionRatio = 10000; // ширина*длина/размер файла, сжатие которого не требуется

$error_ratio = "Недопустимые пропорции изображения. Допустимо не более чем 1 к 2";

// TODO: исправить передачу реального токена
// $token = $_GET["token"];
$token = "token";

$user = R::findOne('users','token = ?',array($token));

if( isset( $_POST['my_file_upload'] ) ){  
	// ВАЖНО! тут должны быть все проверки безопасности передавемых файлов и вывести ошибки если нужно

	$uploaddir = '../photo'; 

	// cоздадим папку если её нет
	if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );

	$file = $_FILES[0]; // полученные файлы
    
    //генерация уникального названия для фотографии
	$file_name = uniqid("",true).".jpg";

	list($width, $height) = getimagesize($file['tmp_name']); 
	
	//проверка соотношения сторон изображения
	if($height/$width > 2 || $width/$height > 2) {  
		die( json_encode( array('file' => $error_ratio ) ) ); 
	}
	
	$compressing_ratio = $width*$height/filesize($file['tmp_name']);

	if($width > 1080 && $compressing_ratio>$maxAllowComressionRatio){ 
		if (resizeImage($file['tmp_name'], 100, $file_name)){
			$done_file = $file_name;
		}
	}
	else {
	// переместим файлы из временной директории в указанную
		if( move_uploaded_file( $file['tmp_name'], "$uploaddir/$file_name" ) ){
			// $done_file[] = realpath( "$uploaddir/$file_name" );
			$done_file = $file_name;
		}
	}

    if($done_file){
        $data = array('file' => $done_file ); 

        $photo = R::dispense('photos');
        $photo->user_id = $user->id;
        $photo->src = $file_name;
        R::store($photo);
    }else{
        $data = array('error' => 'Ошибка загрузки файлов.');
    }

	die( json_encode( $data ) );
}

function resizeImage ($filename, $quality, $new_filename){
    
 	$dir = '../photo/';
       
    $target_width = 1080; // Ширина изображения миниатюры
        
	list($width, $height) = getimagesize($filename); // Возвращает ширину и высоту

	$ratio = $target_width/$width;

	$newwidth = $target_width;
	$newheight = $height*$ratio;

        
	$resized = imagecreatetruecolor($newwidth, $newheight);
			 
    $source = imagecreatefromjpeg($filename);

    // imagecopyresized($resized, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagecopyresampled($resized, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    imagejpeg($resized, $dir . $new_filename, $quality);

        @imagedestroy($resized);         
        @imagedestroy($source);  
            
         return true;
    }

?>