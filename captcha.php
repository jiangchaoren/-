<?php
session_start();
header('Content-Type: image/png');

$w = 130;
$h = 44;
$img = imagecreatetruecolor($w, $h);

$bg = imagecolorallocate($img, 245, 247, 250);
$text_color = imagecolorallocate($img, 99, 102, 241);
$line_color = imagecolorallocate($img, 220, 222, 255);
$noise_color = imagecolorallocate($img, 200, 203, 255);

imagefill($img, 0, 0, $bg);

$chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
$code = '';
for($i = 0; $i < 4; $i++){
    $code .= $chars[rand(0, strlen($chars)-1)];
}
$_SESSION['captcha'] = $code;

// 干扰线
for($i = 0; $i < 5; $i++){
    imageline($img, rand(0, $w), rand(0, $h), rand(0, $w), rand(0, $h), $line_color);
}

// 干扰点
for($i = 0; $i < 80; $i++){
    imagesetpixel($img, rand(0, $w), rand(0, $h), $noise_color);
}

// 文字
$font_size = 5;
$spacing = 26;
$start_x = ($w - 4 * $spacing) / 2 + 5;
for($i = 0; $i < 4; $i++){
    $angle = rand(-20, 20);
    $y = rand(30, 36);
    imagestring($img, $font_size, $start_x + $i * $spacing, $y - 20, $code[$i], $text_color);
}

// 边框
$border = imagecolorallocate($img, 220, 222, 230);
imagerectangle($img, 0, 0, $w-1, $h-1, $border);

imagepng($img);
imagedestroy($img);
