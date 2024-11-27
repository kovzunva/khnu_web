<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ImageService
{
    public static function saveImg($img, $folder, $id, $pref = null){
        $img_path = $img;
        $img_id = $pref.$id;
        $u_id = Auth::id(); // Зверніть увагу на використання Auth
        $Err_img = '';

        // Встановлюємо максимальні розміри зображення
        $maxWidth = 512;
        $maxHeight = 512;

        // Перевірка, чи це картинка
        try{
            if (@get_headers($img_path)[0] !== 'HTTP/1.1 404 Not Found' && exif_imagetype($img_path)) {  
                list($width, $height, $type) = getimagesize($img_path);     
                switch ($type) {
                    case 1: $image = imagecreatefromgif($img_path);
                        break;
                    case 2: $image = imagecreatefromjpeg($img_path);
                        break;
                    case 3: $image = imagecreatefrompng($img_path);
                        break;
                    case 6: $image = imagecreatefrombmp($img_path);
                        break;
                    case 18: $image = imagecreatefromwebp($img_path);
                        break;   
                    default: $Err_img='Даний тип файлу не підтримується: '.$extension;
                }
                
                // Перевірка розміру та масштабування
                if ($width > $maxWidth || $height > $maxHeight) {
                    // Розраховуємо нові пропорційні розміри
                    $ratio = min($maxWidth / $width, $maxHeight / $height);
                    $newWidth = (int)($width * $ratio);
                    $newHeight = (int)($height * $ratio);

                    // Масштабуємо зображення до нових розмірів
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    // Заміна оригінального зображення на змінене
                    imagedestroy($image);
                    $image = $newImage;
                }

                // Збереження картинки
                $sid=str_pad($img_id, 8, "0", STR_PAD_LEFT); // формуємо маску для пошуку завантажених картинок
                $pattern='images\\'.$folder.'\\'.$sid.'*.*';
                $max='0';        
                foreach (glob($pattern) as $filename) { //визначаємо порядковий індекс нової картинки
                    $s=substr(pathinfo($filename,PATHINFO_FILENAME),9);
                    if ($s>$max) $max=$s;
                }
                $newname = str_pad($img_id, 8, "0", STR_PAD_LEFT) . '_' . ($max + 1) . '.jpg';                
                
                imagejpeg($image, 'images/' . $folder . '/' . $newname, 75);
                imagedestroy($image);
            }
            else {
                // Якщо файл не є зображенням
                $Err_img = 'Файл ['.substr($img_path,0,30).'...] не є дійсним зображенням. ';
            }
            return $Err_img;
        }
        catch (\Exception $e) {return 'Файл ['.substr($img_path,0,30).'...] не є дійсним зображенням.';}
    }

    public static function getImgs($folder,$id,$pref=null){
        $img_id = $pref.$id;
        $sid=str_pad($img_id, 8, "0", STR_PAD_LEFT); //формуємо маску для пошуку існуючих ілюстрацій для книги
        $pattern='images/'.$folder.'/'.$sid.'*.*';
        $i=0;
        $imgs = null;
        foreach (glob($pattern) as $filename){
            $imgs[$i] = $filename;
            $i=$i+1;
        }
        return $imgs;
    }

    public static function getImg($folder,$id,$num=0,$pref=null){
        $img_id = $pref.$id;
        $sid=str_pad($img_id, 8, "0", STR_PAD_LEFT); //формуємо маску для пошуку картинок
        $pattern='images/'.$folder.'/'.$sid.'*.*';
        $i=0;
        $imgs = null;
        foreach (glob($pattern) as $filename){
            $imgs[$i] = $filename;
            $i=$i+1;
        }
        if (!$num) $num = 0;
        if ($imgs) return $imgs[$num];
        else return null;
    }

    public static function delImg($del_img){
        unlink($del_img);     
    }    
}
