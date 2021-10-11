<?php

use AcMarche\Bottin\Bce\Import\LowerNameConverter;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->serializer()->nameConverter(LowerNameConverter::class);
};
