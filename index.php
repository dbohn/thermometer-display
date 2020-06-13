<?php

require_once "vendor/autoload.php";

$width = 176;
$height = 264;

/*$image = imagecreatetruecolor($width, $height);

$black = imagecolorallocate($image, 0, 0, 0);
$white = imagecolorallocate($image, 255, 255, 255);

imagefill($image, 0, 0, $white);
imagestring($image, 5, 5, 5, 'Test String', $black);

// Convert image to grayscale
imagetruecolortopalette($image, false, 2);

ob_start();
imagebmp($image, null, false);
$stringdata = ob_get_contents();
ob_end_clean();

imagedestroy($image);*/

/*$im = new Imagick();
$im->setColorSpace(Imagick::COLORSPACE_GRAY);
$im->newImage($width, $height, new ImagickPixel('black'));
$im->setImageFormat('MONO');
$im->setImageType(Imagick::IMGTYPE_BILEVEL);

$text = "Hello World";

$draw = new ImagickDraw();
//$draw->setFont('Arial');
$draw->setFontSize(20);
$draw->setFillColor('white');

$im->annotateImage($draw, 10, 20, 0, $text);

$im->posterizeImage(2, false);
$im->setImageDepth(1);
//$im->writeimage('test-imagick.bmp');
//file_put_contents('mono-image', $im->getImageBlob());
$imageData = $im->getImageBlob();*/

$imageData = require_once 'image_blob.php';

$imageData = implode(array_map("chr", $imageData));

$ffi = FFI::load(__DIR__ . '/epaper/src/libepaper.h');
echo "Initializing" . PHP_EOL;
$ffi->DEV_Module_Init();
$ffi->EPD_2IN7_Init();

echo "Clearing" . PHP_EOL;
$ffi->EPD_2IN7_Clear();

$bufferType = FFI::arrayType(FFI::type("uint8_t"), [strlen($imageData)]);

$buffer = FFI::new($bufferType);

FFI::memcpy($buffer, $imageData, strlen($imageData));

$ffi->EPD_2IN7_Display($buffer);

echo "Sleeping" . PHP_EOL;
$ffi->EPD_2IN7_Sleep();

echo "Now we are done!" . PHP_EOL;