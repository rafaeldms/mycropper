<?php

namespace RafaelDms\Cropper;

use Exception;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\WebPConvert;

/**
 * Class RafaelDms Cropper
 * @package RafaelDms\Cropper
 * @author Rafael Damasceno Ferreira <https://www.github.com/rafaeldms>
 */
class Cropper
{
    /** @var string $cachePath */
    private string $cachePath;

    /** @var string $imagePath */
    private string $imagePath;

    /** @var string $imageMime */
    private string $imageMime;

    /** @var string $imageName */
    private string $imageName;

    /** @var int $quality */
    private int $quality;

    /** @var int $compressor */
    private int $compressor;

    /** @var bool $webP */
    private bool $webP;

    /**
     * Allow jpg, png and webp to thumb and cache generate
     * @var array allowed media types
     */
    private static array $allowedExt = ['image/jpeg', 'image/png', 'image/webp'];

    /** @var ConversionFailedException $exception*/
    public ConversionFailedException $exception;

    /**
     * @param string $cachePath
     * @param int $quality
     * @param int $compressor
     * @param bool $webP
     * @throws Exception
     */
    public function __construct(string $cachePath, int $quality = 75, int $compressor = 5, bool $webP = false)
    {
        $this->cachePath = $cachePath;
        $this->quality = $quality;
        $this->compressor = $compressor;
        $this->webP = $webP;

        if (!file_exists($this->cachePath) || !is_dir($this->cachePath)) {
            if (!mkdir($this->cachePath, 0755, true)) {
                throw new Exception("Could not create cache folder");
            }
        }
    }

    /**
     * @param string $imagePath
     * @param int $width
     * @param int|null $height
     * @return string|null
     */
    public function make(string $imagePath, int $width, int $height = null): ?string
    {
         if (!file_exists($imagePath) || !is_file($imagePath)) {
            return "Image file does not exist: $imagePath";
        }
        $this->imagePath = $imagePath;
        $this->imageName = $this->name($this->imagePath, $width, $height);
        $this->imageMime = mime_content_type($imagePath);
        if (!in_array($this->imageMime, self::$allowedExt)) {
            return "Invalid image type: $this->imageMime";
        }
        return $this->image($width, $height);
    }

    /**
     * @param int $width
     * @param int|null $height
     * @return string|null
     */
    private function image(int $width, int $height = null): ?string
    {
        $imageWebP = "$this->cachePath/$this->imageName.webp";
        $imageExt = "$this->cachePath/$this->imageName." . pathinfo($this->imagePath)["extension"];
        if ($this->webP && file_exists($imageWebP) && is_file($imageWebP)) {
            return $imageWebP;
        }
        if (file_exists($imageExt) && is_file($imageExt)) {
            return $imageExt;
        }
        return $this->imageCache($width, $height);
    }

    /**
     * @param string $name
     * @param int $width
     * @param int|null $height
     * @return string
     */
    private function name(string $name, int $width, int $height = null): string
    {
        $filterName = mb_convert_encoding(htmlspecialchars(mb_strtolower(pathinfo($name)["filename"])), 'ISO-8859-1',
            'UTF-8');
        $formats = mb_convert_encoding('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª',
            'ISO-8859-1', 'UTF-8');
        $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyrr                                 ';
        $trimName = trim(strtr($filterName, $formats, $replace));
        $name = str_replace(["-----", "----", "---", "--"], "-", str_replace(" ", "-", $trimName));

        $hash = $this->hash($this->imagePath);
        $widthName = ($width ? "-{$width}" : "");
        $heightName = ($height ? "x{$height}" : "");

        return "{$name}{$widthName}{$heightName}-{$hash}-" . time();
    }

    /**
     * @param string $path
     * @return string
     */
    private function hash(string $path): string
    {
        return hash('crc32', pathinfo($path)['basename']);
    }

    /**
     * @param string $imagePath
     * @return bool
     */
    public function flush(string $imagePath): bool
    {
        foreach (scandir($this->cachePath) as $file) {
            $file = "{$this->cachePath}/{$file}";
            if ($imagePath && strpos($file, $this->hash($imagePath))) {
                $this->imageDestroy($file);
            } elseif (!$imagePath) {
                $this->imageDestroy($file);
            }
        }
    }

    /**
     * @param int $width
     * @param int|null $height
     * @return string|null
     */
    private function imageCache(int $width, int $height = null): ?string
    {
        list($src_w, $src_h) = getimagesize($this->imagePath);
        $height = ($height ?? ($width * $src_h) / $src_w);
        $src_x = 0;
        $src_y = 0;
        $cmp_x = $src_w / $width;
        $cmp_y = $src_h / $height;
        if ($cmp_x > $cmp_y) {
            $src_x = round(($src_w - ($src_w / $cmp_x * $cmp_y)) / 2);
            $src_w = round($src_w / $cmp_x * $cmp_y);
        } elseif ($cmp_y > $cmp_x) {
            $src_y = round(($src_h - ($src_h / $cmp_y * $cmp_x)) / 2);
            $src_h = round($src_h / $cmp_y * $cmp_x);
        }
        $height = (int)$height;
        $src_x = (int)$src_x;
        $src_y = (int)$src_y;
        $src_w = (int)$src_w;
        $src_h = (int)$src_h;
        if ($this->imageMime == "image/jpeg") {
            return $this->fromJpg($width, $height, $src_x, $src_y, $src_w, $src_h);
        }
        if ($this->imageMime == "image/png") {
            return $this->fromPng($width, $height, $src_x, $src_y, $src_w, $src_h);
        }
        return null;
    }

    /**
     * @param string $imagePath
     * @return void
     */
    private function imageDestroy(string $imagePath): void
    {
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $src_x
     * @param int $src_y
     * @param int $src_w
     * @param int $src_h
     * @return string|null
     */
    private function fromJpg(int $width, int $height, int $src_x, int $src_y, int $src_w, int $src_h): ?string
    {
        $image = imagecreatetruecolor($width, $height);
        $src = imagecreatefromjpeg($this->imagePath);
        imagecopyresampled($image, $src, 0, 0, $src_x, $src_y, $width, $height, $src_w, $src_h);
        imagejpeg($image, "$this->cachePath/$this->imageName.jpg", $this->quality);
        imagedestroy($image);
        imagedestroy($src);
        if ($this->webP) {
            try {
                $this->toWebP("$this->cachePath/$this->imageName.jpg");
            } catch (ConversionFailedException $exception) {
                $this->exception = $exception;
            }
        }
        return "$this->cachePath/$this->imageName.jpg";
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $src_x
     * @param int $src_y
     * @param int $src_w
     * @param int $src_h
     * @return string|null
     */
    private function fromPng(int $width, int $height, int $src_x, int $src_y, int $src_w, int $src_h): ?string
    {
        $image = imagecreatetruecolor($width, $height);
        $src = imagecreatefrompng($this->imagePath);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagecopyresampled($image, $src, 0, 0, $src_x, $src_y, $width, $height, $src_w, $src_h);
        imagepng($image, "$this->cachePath/$this->imageName.png", $this->compressor);
        imagedestroy($image);
        imagedestroy($src);

        if ($this->webP) {
            try {
                $this->toWebP("$this->cachePath/$this->imageName.png");
            } catch (ConversionFailedException $exception) {
                $this->exception = $exception;
            }
        }
        return "$this->cachePath/$this->imageName.png";
    }

    /**
     * @param string $image
     * @param $unlinkImage
     * @return string
     */
    private function toWebP(string $image, $unlinkImage = true): string
    {
        try {
            $webPConverted = pathinfo($image)["dirname"] . "/" . pathinfo($imagePath)["filename"] . ".webp";
            WebPConvert::convert($image, $webPConverted, ['default-quality' => $this->quality]);
            if($unlinkImage){
                unlink($image);
            }
            return $webPConverted;
        }catch (ConversionFailedException $exception){
            $this->exception = $exception;
            return $image;
        }
    }
}