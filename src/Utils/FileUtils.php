<?php

namespace AcMarche\Bottin\Utils;

use AcMarche\Bottin\Entity\FicheImage;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class FileUtils
{
    public function __construct(
        private readonly FileLocator $fileLocator,
    ) {}

    public function getFilePath(string $fileName): array|string
    {
        return $this->fileLocator->locate(
            '@AcMarcheBottinBundle'.\DIRECTORY_SEPARATOR.'config'.\DIRECTORY_SEPARATOR.'elastic'.\DIRECTORY_SEPARATOR.$fileName,
        );
    }

    public function readConfigFile(string $fileName): array
    {
        $filePath = $this->getFilePath($fileName);
        set_error_handler(
            static function ($type, $msg) use (&$error) {
                $error = $msg;
            },
        );
        $content = Yaml::parse(file_get_contents($filePath));
        restore_error_handler();
        if (false === $content) {
            throw new \RuntimeException($error);
        }

        return $content;
    }

    public function processImage(FicheImage $ficheImage)
    {
        ;
        $path = '/var/'.$ficheImage->imageName;
        $filter = 'acbottin_thumb';

return;
        $filteredImage = $this->filterService->getUrlOfFilteredImage($path, $filter);

        // Rotate image manually if needed using the GD library
        $image = imagecreatefromjpeg($filteredImage);
        if (\exif_imagetype($filteredImage)) {
            $exif = \exif_read_data($filteredImage);
            dd($exif);
            if (isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, 270, 0);
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                }
            }
        }
        // Save the corrected image if necessary
    }

}
