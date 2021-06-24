<?php

namespace AcMarche\Bottin\Utils;

use RuntimeException;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class FileUtils
{
    private FileLocator $fileLocator;

    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    public function getFilePath(string $fileName): string
    {
        return $this->fileLocator->locate(
            '@AcMarcheBottinBundle'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'elastic'.DIRECTORY_SEPARATOR.$fileName
        );
    }

    public function readConfigFile(string $fileName): array
    {
        $filePath = $this->getFilePath($fileName);
        set_error_handler(
            function ($type, $msg) use (&$error) {
                $error = $msg;
            }
        );
        $content = Yaml::parse(file_get_contents($filePath));
        restore_error_handler();
        if (false === $content) {
            throw new RuntimeException($error);
        }

        return $content;
    }
}
