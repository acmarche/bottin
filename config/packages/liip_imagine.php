<?php

use Symfony\Config\LiipImagineConfig;

return static function (LiipImagineConfig $liipImagineConfig): void {
    $filterSet = $liipImagineConfig->filterSet('acbottin_thumb');

    $filterSet->quality(100);

    // Define the thumbnail filter
    $thumbnailFilter = $filterSet->filter('thumbnail');
    $thumbnailFilter->set('size', [250, 250]); // Ensure both width & height are specified
    $thumbnailFilter->set('mode', 'outbound');

    // Define the rotate filter
    $rotateFilter = $filterSet->filter('rotate');
    $rotateFilter->set('angle', 90);

    $liipImagineConfig
        ->filterSet('circuitcourt_thumb')
        ->quality(95)
        ->filter('thumbnail')
        ->set('size', [1200])
        ->set('mode', 'inset');
};
