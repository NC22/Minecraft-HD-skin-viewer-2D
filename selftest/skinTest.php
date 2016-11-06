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
 * Render based on templates from 
 * http://minecraft.gamepedia.com/Skin
 *
 *
 * Script for testing functional of class "SkinGenerator2D"
 *
 */

if (isset($_GET['show'])) {

    require '../SkinViewer2D.class.php';
    header("Content-type: image/png");
      
    $show = $_GET['show'];
    
    $baseDir = dirname(__FILE__) . '/';
    $skin =  empty($_GET['file_name']) ? 'default' : $_GET['file_name'];
    $skin =  $baseDir . $skin . '.png';

    if (!skinViewer2D::isValidSkin($skin)) {
        $skin = $baseDir . 'default.png';
    }
    
    if ($show !== 'head') {
        $cloak = isset($_GET['cloak']) ? (($_GET['cloak'] === 'hd') ? './hd_cloak.png' : './cloak.png') : false;
        $side = isset($_GET['side']) ? $_GET['side'] : false;

        $img = skinViewer2D::createPreview($skin, $cloak, $side);
    } else {
        $img = skinViewer2D::createHead($skin, 64);
    }

    imagepng($img);
    
} else {
    ?>

    <style> img { margin-left: 10px; } </style>
    
    <!-- default skin render -->    
    <p>
        <img src="skinTest.php?show=head" />
        <img src="skinTest.php?show=body&side=front" />
        <img src="skinTest.php?show=body&side=back" />
        <!-- both sides without cloak -->
        <img src="skinTest.php?show=body" />
        <!-- separate with cloak -->
        <img src="skinTest.php?show=body&side=front&cloak=yes" />
        <img src="skinTest.php?show=body&side=back&cloak=yes" />
        <!-- both sides with cloak -->
        <img src="skinTest.php?show=body&cloak=yes" /> 
    </p>
    
    <!-- hd skin 64x32 render example -->
    <p>
        <img src="skinTest.php?show=head&file_name=hd" />
        <img src="skinTest.php?show=body&side=front&file_name=hd" />
        <img src="skinTest.php?show=body&side=back&file_name=hd" />
        <img src="skinTest.php?show=body&file_name=hd" />
        <img src="skinTest.php?show=body&side=front&cloak=yes&file_name=hd" />
        <img src="skinTest.php?show=body&side=back&cloak=yes&file_name=hd" />
        <img src="skinTest.php?show=body&cloak=yes&file_name=hd" />    
    </p>
    
    <!-- version 1.8 skin 64x64 -->
    <p>
        <img src="skinTest.php?show=head&file_name=skin64" />
        <img src="skinTest.php?show=body&side=front&file_name=skin64" />
        <img src="skinTest.php?show=body&side=back&file_name=skin64" />
        <img src="skinTest.php?show=body&file_name=skin64" />
        <img src="skinTest.php?show=body&side=front&cloak=yes&file_name=skin64" />
        <img src="skinTest.php?show=body&side=back&cloak=yes&file_name=skin64" />
        <img src="skinTest.php?show=body&cloak=yes&file_name=skin64" />    
    </p>
    
    <!-- version 1.8 skin 64x64 tiny arms, cloak HD -->
    <p>
        <img src="skinTest.php?show=head&file_name=tiny_arms" />
        <img src="skinTest.php?show=body&side=front&cloak=hd&file_name=tiny_arms" />
        <img src="skinTest.php?show=body&side=back&cloak=hd&file_name=tiny_arms" />
        <img src="skinTest.php?show=body&cloak=hd&file_name=tiny_arms" />
        <img src="skinTest.php?show=body&side=front&cloak=hd&file_name=tiny_arms" />
        <img src="skinTest.php?show=body&side=back&cloak=hd&file_name=tiny_arms" />
        <img src="skinTest.php?show=body&cloak=hd&mode=hd&file_name=tiny_arms" /> 
    </p> 
    
<?php } ?>
    