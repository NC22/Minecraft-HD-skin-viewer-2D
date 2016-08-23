<?php
/* WEB-APP : webMCR/Package/SkinGenerator2D [Unit test] (С) 2013-2014 NC22 | License : GPLv3 */

if (isset($_GET['show'])) {

    require '../SkinViewer2D.class.php';
    header("Content-type: image/png");

    $show = $_GET['show'];
    $mode =  empty($_GET['mode']) ? 'skin' : $_GET['mode'];
    $skin = './' . $mode . '.png';
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
        <img src="skinTest.php?show=head&mode=hd" />
        <img src="skinTest.php?show=body&side=front&mode=hd" />
        <img src="skinTest.php?show=body&side=back&mode=hd" />
        <img src="skinTest.php?show=body&mode=hd" />
        <img src="skinTest.php?show=body&side=front&cloak=yes&mode=hd" />
        <img src="skinTest.php?show=body&side=back&cloak=yes&mode=hd" />
        <img src="skinTest.php?show=body&cloak=yes&mode=hd" />    
    </p>
    <p>
        <img src="skinTest.php?show=body&side=front&cloak=hd" />
        <img src="skinTest.php?show=body&side=back&cloak=hd" />
        <img src="skinTest.php?show=body&cloak=hd" />
        <img src="skinTest.php?show=body&side=front&cloak=hd&mode=hd" />
        <img src="skinTest.php?show=body&side=back&cloak=hd&mode=hd" />
        <img src="skinTest.php?show=body&cloak=hd&mode=hd" /> 
    </p> 
    <p>
        <img src="skinTest.php?show=body&side=front&cloak=hd&mode=skin64" />
        <img src="skinTest.php?show=body&side=back&cloak=hd&mode=skin64" />
        <img src="skinTest.php?show=body&cloak=hd&mode=skin64" />
        <img src="skinTest.php?show=body&mode=skin64" />
    </p>    
<?php } ?>
    