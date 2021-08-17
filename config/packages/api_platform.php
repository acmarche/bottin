<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension(
        'api_platform',
        [
            'mapping' => [
                'paths' => ['%kernel.project_dir%/src/AcMarche/Bottin/src/Entity'],
            ],
        ]
    );
};
