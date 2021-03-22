<?php
 /** 
 * @category  MineCraft tools
 * @package   webMCR
 * @author    Rubchuk Vladimir <torrenttvi@gmail.com>
 * @copyright 2013-2016 Rubchuk Vladimir
 * @version   1.21b
 * @license   GPLv3
 * 
 * Special thanks to Official Minecraft Wiki
 * Based on templates from 
 * http://minecraft.gamepedia.com/Skin
 */

class SkinViewer2D
{
    private static $slimDetectPixel = array(42, 51); // x,y 

    /* Допустимые пропорции образа */

    private static $skinProps = array(
        0 => array('base' => 64, 'ratio' => 2),
        1 => array('base' => 64, 'ratio' => 1),
    );
    
    /*
     * Массив допустимых пропорций плаща (для плаща в MC нет четкой привязки к размеру) 
     * Некоторые плащи используют соотношение 22x17, тогда как обычно используется 
     * соотношение 64x32 с незаполненным пространством
     */

    private static $cloakProps = array(
        0 => array('base' => 64, 'ratio' => 2),
        1 => array('base' => 22, 'ratio' => 1.29),
    );

    /**
     * Создает изображение головы; вид спереди
     * @param string $way_skin полный путь до файла изображения скина
     * @param int $size размер возвращаемого изображения в пикселях
     * @return resource Возвращает идентификатор GD image при успешном результате и <b>false</b> при ошибке 
     */
    
    public static function createHead($way_skin, $size = 151)
    {
        if (!$info = self::isValidSkin($way_skin))
            return false;
            
        $img = @imagecreatefrompng($way_skin);
        if (!$img)
            return false;
            
        $p = array('face' => array(8, 8), 'hat' => array(40, 8));  
        
        
        $av = imagecreatetruecolor($size, $size);
        $mp = $info['scale'];

        imagecopyresized($av, $img, 0, 0, $p['face'][0] * $mp, $p['face'][1] * $mp, $size, $size, 8 * $mp, 8 * $mp);
        imagecopyresized($av, $img, 0, 0, $p['hat'][0] * $mp, $p['hat'][1] * $mp, $size, $size, 8 * $mp, 8 * $mp);
        imagedestroy($img);   
        
        return $av;
    }

    /**
     * Создать видовое изображение из скина; фронтальный \ задний вид  
     * @param string $way_skin полный путь до файла изображения скина
     * @param string $way_cloak полный путь до файла изображения плаща ( при необходимости )
     * @param string $side вид спереди - front \ вид сзади - back \ по умолчанию оба вида на одном изображении последовательно
     * @param int $size высота возвращаемого изображения в пикселях (ширина пропорцианальна задаваемой высоте и завист так же от параметра $side)
     * @return resource Возвращает идентификатор GD image при успешном результате и <b>false</b> при ошибке
     */
    
    public static function createPreview($way_skin, $way_cloak = false, $side = false, $size = 224)
    {
        if (!$info = self::isValidSkin($way_skin))
            return false;

        $skin = @imagecreatefrompng($way_skin);
        if (!$skin)
            return false;

        $mp = $info['scale'];
        $size_x = (($side) ? 16 : 32);
        $preview = imagecreatetruecolor($size_x * $mp, 32 * $mp);
        
        $transparent = imagecolorallocatealpha($preview, 255, 255, 255, 127);
        imagefill($preview, 0, 0, $transparent);
        
        $armWidth = 4; // for slim \ fat arms on version 1.8 or higher
        $slim = false;
        
        if ($info['ratio'] == 1) {
        
            // is slim verion
            
            $color = imagecolorat($skin, self::$slimDetectPixel[0], self::$slimDetectPixel[1]); 
            $colors = imagecolorsforindex($skin, $color); // returns rgba array
            
            if ((int) $colors['alpha'] == 127) {
                $slim = true;
                $armWidth = 3;
            }
        }

        if (!$side or $side === 'front') {

            // head
            imagecopy($preview, $skin, 4 * $mp, 0 * $mp, 8 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
            imagecopy($preview, $skin, 4 * $mp, 0 * $mp, 40 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
            
            // front arms
            
            imagecopy($preview, $skin, (4 - $armWidth) * $mp, 8 * $mp, 44 * $mp, 20 * $mp, $armWidth * $mp, 12 * $mp);
            
            // right side
            if ($info['ratio'] == 2) {
                self::imageflip($preview, $skin, 12 * $mp, 8 * $mp, 44 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            } else {
                // body (8) + arm(4)
                imagecopy($preview, $skin, 12 * $mp, 8 * $mp, 36 * $mp, 52 * $mp, $armWidth * $mp, 12 * $mp);
            }
            
            // body
            imagecopy($preview, $skin, 4 * $mp, 8 * $mp, 20 * $mp, 20 * $mp, 8 * $mp, 12 * $mp);
            
            // front legs
            imagecopy($preview, $skin, 4 * $mp, 20 * $mp, 4 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            
            if ($info['ratio'] == 2) {
                self::imageflip($preview, $skin, 8 * $mp, 20 * $mp, 4 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            } else {
                imagecopy($preview, $skin, 8 * $mp, 20 * $mp, 20 * $mp, 52 * $mp, 4 * $mp, 12 * $mp);
            }
            
            if ($info['ratio'] == 1) {
                // front arms layer 2 right
                imagecopy($preview, $skin, (4 - $armWidth) * $mp, 8 * $mp, 44 * $mp, 36 * $mp, $armWidth * $mp, 12 * $mp);
                // left
                imagecopy($preview, $skin, 12 * $mp, 8 * $mp, 52 * $mp, 52 * $mp, $armWidth * $mp, 12 * $mp);
                
                // jacket
                imagecopy($preview, $skin, 4 * $mp, 8 * $mp, 20 * $mp, 36 * $mp, 8 * $mp, 12 * $mp);
                
                // front legs right leg layer 2 
                imagecopy($preview, $skin, 4 * $mp, 20 * $mp, 4 * $mp, 36 * $mp, 4 * $mp, 12 * $mp);    
                // front legs  left leg layer 2 
                imagecopy($preview, $skin, 8 * $mp, 20 * $mp, 4 * $mp, 52 * $mp, 4 * $mp, 12 * $mp);    
            }
            
        }
        if (!$side or $side === 'back') {
        
            $mp_x_h = ($side) ? 0 : imagesx($preview) / 2; // base padding left on output canvas, if render both sides on the same image
            $backArmPos = ($armWidth * 2);
            
            // front side of arm have width 3, but back still able have width 4, so skip pixels at begining
            
            if ($armWidth < 4) {
                $backArmPos += 4 - $armWidth; 
            }
            
            // head
            imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 0 * $mp, 24 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
            imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 0 * $mp, 56 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
            
            // body back
            imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 8 * $mp, 32 * $mp, 20 * $mp, 8 * $mp, 12 * $mp);
            
            // back arm, calc from start right arm zone base on arm width
                      
            imagecopy($preview, $skin, $mp_x_h + 12 * $mp, 8 * $mp, (44 + $backArmPos) * $mp, 20 * $mp, $armWidth * $mp, 12 * $mp);
            
            // flip left arm for old skins
            
            if ($info['ratio'] == 2) {
                self::imageflip($preview, $skin, $mp_x_h + 0 * $mp, 8 * $mp, 52 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            } else {
                imagecopy($preview, $skin, $mp_x_h + (4 - $armWidth) * $mp, 8 * $mp, (36 + $backArmPos) * $mp, 52 * $mp, $armWidth * $mp, 12 * $mp);
            }    
                        
            // back leg
            
            // left
            if ($info['ratio'] == 2) {
                self::imageflip($preview, $skin, $mp_x_h + 4 * $mp, 20 * $mp, 12 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            } else {
                imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 20 * $mp, 28 * $mp, 52 * $mp, 4 * $mp, 12 * $mp);
            }
            
            // right
            imagecopy($preview, $skin, $mp_x_h + 8 * $mp, 20 * $mp, 12 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
              
            // addition attributes for new skins (v 1.8 >) 
            if ($info['ratio'] == 1) {
                // jaket
                imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 8 * $mp, 32 * $mp, 36 * $mp, 8 * $mp, 12 * $mp);
                
                // back arm decals right arm
                imagecopy($preview, $skin, $mp_x_h + 12 * $mp, 8 * $mp, (44 + $backArmPos) * $mp, 36 * $mp, $armWidth * $mp, 12 * $mp);
                // back arm decals left arm
                imagecopy($preview, $skin, $mp_x_h + (4 - $armWidth) * $mp, 8 * $mp, (52 + $backArmPos)  * $mp, 52 * $mp, $armWidth * $mp, 12 * $mp);
                
                // back leg decals 2 right leg
                imagecopy($preview, $skin, $mp_x_h + 8 * $mp, 20 * $mp, 12 * $mp, 36 * $mp, 4 * $mp, 12 * $mp); 
                // back leg decals 2 left leg
                imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 20 * $mp, 12 * $mp, 52 * $mp, 4 * $mp, 12 * $mp);      
            }           
        }

        if ($way_cloak and !$info = self::isValidCloak($way_cloak)) {
            $way_cloak = null;
        } else {
            $mp_cloak = $info['scale'];
        }

        $cloak = @imagecreatefrompng($way_cloak);
        if (!$cloak)
            $way_cloak = null;

        if ($way_cloak) {

            if ($mp_cloak > $mp) { // cloak bigger              
                $mp_x_h = ($side) ? 0 : ($size_x * $mp_cloak) / 2;
                $mp_result = $mp_cloak;
            } else {
                $mp_x_h = ($side) ? 0 : ($size_x * $mp) / 2;
                $mp_result = $mp;
            }

            $preview_cloak = imagecreatetruecolor($size_x * $mp_result, 32 * $mp_result);
            $transparent = imagecolorallocatealpha($preview_cloak, 255, 255, 255, 127);
            imagefill($preview_cloak, 0, 0, $transparent);

            // ex. copy front side of cloak to new image

            if (!$side or $side === 'front')
                imagecopyresized(
                    $preview_cloak, // result image
                    $cloak, // source image
                    round(3 * $mp_result), // start x point of result
                    round(8 * $mp_result), // start y point of result
                    round(12 * $mp_cloak), // start x point of source img
                    round(1 * $mp_cloak), // start y point of source img
                    round(10 * $mp_result), // result <- width ->
                    round(16 * $mp_result), // result /|\ height \|/
                    round(10 * $mp_cloak), // width of cloak img (from start x \ y) 
                    round(16 * $mp_cloak) // height of cloak img (from start x \ y) 
                );

            imagecopyresized($preview_cloak, $preview, 0, 0, 0, 0, imagesx($preview_cloak), imagesy($preview_cloak), imagesx($preview), imagesy($preview));

            if (!$side or $side === 'back')
                imagecopyresized(
                    $preview_cloak, 
                    $cloak, 
                    $mp_x_h + 3 * $mp_result, 
                    round(8 * $mp_result), 
                    round(1 * $mp_cloak), 
                    round(1 * $mp_cloak), 
                    round(10 * $mp_result), 
                    round(16 * $mp_result), 
                    round(10 * $mp_cloak), 
                    round(16 * $mp_cloak)
                );

            $preview = $preview_cloak;
        }

        $size_x = ($side) ? $size / 2 : $size;
        $fullsize = imagecreatetruecolor($size_x, $size);

        imagesavealpha($fullsize, true);
        $transparent = imagecolorallocatealpha($fullsize, 255, 255, 255, 127);
        imagefill($fullsize, 0, 0, $transparent);

        imagecopyresized($fullsize, $preview, 0, 0, 0, 0, imagesx($fullsize), imagesy($fullsize), imagesx($preview), imagesy($preview));

        imagedestroy($preview);
        imagedestroy($skin);
        if ($way_cloak)
            imagedestroy($cloak);

        return $fullsize;
    }

    /**
     * Сохранить изображение в формате png; отрисованое по правилам createPreview
     * @param string $way_save путь до сохраняемого файла
     * @param string $way_skin полный путь до файла изображения скина
     * @param string $way_cloak полный путь до файла изображения плаща ( при необходимости )
     * @param string $side вид спереди - front \ вид сзади - back \ по умолчанию обе стороны
     * @param int $size высота возвращаемого изображения в пикселях (ширина пропорцианальна задаваемой высоте и завист так же от параметра $side)
     * @return resource Возвращает идентификатор GD image при успешном результате и <b>false</b> при ошибке
     */
    
    public static function savePreview($way_save, $way_skin, $way_cloak = false, $side = false, $size = 224)
    {
        if (file_exists($way_save))
            unlink($way_save);

        $new_skin = self::createPreview($way_skin, $way_cloak, $side, $size);
        if (!$new_skin)
            return false;

        imagepng($new_skin, $way_save);
        return $new_skin;
    }

    /**
     * Сохранить изображение в формате png; отрисованое по правилам createHead
     * @param int $size размер возвращаемого изображения в пикселях для одной стороны
     * @param string $way_save путь до сохраняемого файла
     * @param string $way_skin полный путь до файла изображения скина
     * @return resource Возвращает идентификатор GD image при успешном результате и <b>false</b> при ошибке
     */
    
    public static function saveHead($way_save, $way_skin, $size = 151)
    {
        if (file_exists($way_save))
            unlink($way_save);

        $new_head = self::createHead($way_skin, $size);
        if (!$new_head)
            return false;

        imagepng($new_head, $way_save);
        return $new_head;
    }

    /**
     * Проверить, является ли файл изображением, с соответствующими для скина пропорциями
     * @param string $way_skin полный путь до файла изображения скина
     * @return array Если файл не проходит проверку возвращает <b>false</b>, иначе возвращает массив пропорций изображения 
     */
    
    public static function isValidSkin($way_skin)
    {
        if (!file_exists($way_skin))
            return false;

        if (!$imageSize = self::getImageSize($way_skin))
            return false;
            
            
        for ($i = 0; $i < sizeof(self::$skinProps); $i++) {
            if (round(self::$skinProps[$i]['ratio'], 2) != self::getRatio($imageSize))
                continue;

            return array(
                'ratio' => self::getRatio($imageSize),
                'scale' => self::getScale($imageSize, self::$skinProps[$i]['base']),
            );
        }

        return false;
    }

    /**
     * Проверить, является ли файл изображением, с соответствующими для плащя пропорциями
     * @param string $way_cloak полный путь до файла изображения плаща
     * @return array Если файл не проходит проверку возвращает <b>false</b>, иначе возвращает массив пропорций изображения
     */
    
    public static function isValidCloak($way_cloak)
    {
        if (!file_exists($way_cloak))
            return false;
        if (!$imageSize = self::getImageSize($way_cloak))
            return false;

        for ($i = 0; $i < sizeof(self::$cloakProps); $i++) {
            if (round(self::$cloakProps[$i]['ratio'], 2) != self::getRatio($imageSize))
                continue;

            return array(
                'ratio' => self::$cloakProps[$i]['ratio'],
                'scale' => self::getScale($imageSize, self::$cloakProps[$i]['base']),
            );
        }
        
        return false;
    }

    /* Коэфициэнт масштабирования относительно базового разрешения */
    
    private static function getScale($inputImg, $size)
    {
        if (!is_array($inputImg) and !$inputImg = self::getImageSize($inputImg))
            return false;
        return $inputImg[0] / $size;
    }

    /* Коэфициэнт соотношения сторон */
    private static function getRatio($inputImg)
    {
        if (!is_array($inputImg) and !$inputImg = self::getImageSize($inputImg))
            return false;
        return round($inputImg[0] / $inputImg[1], 2);
    }

    private static function getImageSize($file)
    {
        $imageSize = @getimagesize($file);

        if (empty($imageSize))
            return false;
        return $imageSize;
    }

    private static function imageflip(&$result, &$img, $rx = 0, $ry = 0, $x = 0, $y = 0, $size_x = null, $size_y = null)
    {
        if ($size_x < 1)
            $size_x = imagesx($img);
        if ($size_y < 1)
            $size_y = imagesy($img);

        imagecopyresampled($result, $img, $rx, $ry, ($x + $size_x - 1), $y, $size_x, $size_y, 0 - $size_x, $size_y);
    }
}
