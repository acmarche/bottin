<?php

use AcMarche\Bottin\Cbe\Import\LowerNameConverter;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->serializer()->nameConverter(LowerNameConverter::class);
};
