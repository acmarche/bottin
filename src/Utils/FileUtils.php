<?php


namespace AcMarche\Bottin\Utils;

use Symfony\Component\HttpKernel\Config\FileLocator;

class FileUtils
{
    private \Symfony\Component\HttpKernel\Config\FileLocator $fileLocator;

    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    public function getFilePath(string $fileName): string
    {
        return $this->fileLocator->locate(
            '@AcMarcheBottinBundle' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'elastic' . DIRECTORY_SEPARATOR . $fileName
        );
    }

    public function readConfigFile(string $fileName): string
    {
        $filePath = $this->getFilePath($fileName);
        set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });
        $content = file_get_contents($filePath);
        restore_error_handler();
        if (false === $content) {
            throw new \RuntimeException($error);
        }

        return $content;
    }

    public function jsonDecode(): void
    {

    }
}
