<?php
/* WEB-APP : webMCR/Package/SkinGenerator2D [Unit test] (С) 2013-2014 NC22 | License : GPLv3 */

if (isset($_GET['show'])) {

    require '../SkinViewer2D.class.php';
    header("Content-type: image/png");

    $show = $_GET['show'];
    $skin = isset($_GET['hd']) ? './hd.png' : './skin.png';
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
    <p>
        <img src="skinTest.php?show=head" />
        <img src="skinTest.php?show=body&side=front" />
        <img src="skinTest.php?show=body&side=back" />
        <img src="skinTest.php?show=body" />
        <img src="skinTest.php?show=body&side=front&cloak=yes" />
        <img src="skinTest.php?show=body&side=back&cloak=yes" />
        <img src="skinTest.php?show=body&cloak=yes" />
    </p>
    <p>
        <img src="skinTest.php?show=head&hd=yes" />
        <img src="skinTest.php?show=body&side=front&hd=yes" />
        <img src="skinTest.php?show=body&side=back&hd=yes" />
        <img src="skinTest.php?show=body&hd=yes" />
        <img src="skinTest.php?show=body&side=front&cloak=yes&hd=yes" />
        <img src="skinTest.php?show=body&side=back&cloak=yes&hd=yes" />
        <img src="skinTest.php?show=body&cloak=yes&hd=yes" />    
    </p>
    <p>
        <img src="skinTest.php?show=body&side=front&cloak=hd" />
        <img src="skinTest.php?show=body&side=back&cloak=hd" />
        <img src="skinTest.php?show=body&cloak=hd" />
        <img src="skinTest.php?show=body&side=front&cloak=hd&hd=yes" />
        <img src="skinTest.php?show=body&side=back&cloak=hd&hd=yes" />
        <img src="skinTest.php?show=body&cloak=hd&hd=yes" /> 
    </p>        
<?php } ?>
    