<?php
if (!isset($_SESSION)) session_start();
$random_string = mt_rand(1000,9999);
$_SESSION['captcha'] = $random_string;
$font = imageloadfont('almosnow.gdf'); 
if (!$font) $random_string = "FONT NOT FOUND";
$fontWidth = imagefontwidth($font);
$fontHeight = imagefontheight($font);
$width  = strlen($random_string) * $fontWidth;
$height = $fontHeight;
$img = @ImageCreate ($width, $height) or die ("Cannot Initialize new GD image stream");
$background_color = @imagecolorallocate($img, 255, 255, 255);
$text_color = @imagecolorallocate($img,   0,   0, 0);
@imagestring($img, $font, 0, 0, $random_string, $text_color);
$img2 = @ImageCreate ($width, $height) or die ("Cannot Initialize new GD image stream");
/*
// ���������� ��� ��������:
$x=1;
$i=0;
while ($x<$width) { // ���� �� X-�� � �������� �������
$xx = mt_rand(1,2);   // c ���� ����������� ����� ��������
$yy = mt_rand(5,10); // c ���� ����������� ����� ��������
$xx = 1;
$yy = 1;
$i=$i+($xx/10);         // ��� ��� Sin-���
$y = ceil(sin($i)*$yy);// �������� �� Y-��
@imagecopy ($img2, $img, $x, $y, $x, 0, 1, $height); // ����������� �������
$x++;
}*/

// ���������� ��������� ��� �������������� �����������
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header ("Content-type: image/png");
ImagePng ($img);
@imagedestroy($img);
@imagedestroy($img);
?>
