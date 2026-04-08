<?php

declare(strict_types=1);

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

final class BottinPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return 'bottin/fiches/'.$media->model_id.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return 'bottin/fiches/'.$media->model_id.'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return 'bottin/fiches/'.$media->model_id.'/responsive-images/';
    }
}
