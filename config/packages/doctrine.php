<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\Bottin' => [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/src/AcMarche/Bottin/src/Entity',
                        'prefix' => 'AcMarche\Bottin',
                        'alias' => 'AcMarche\Bottin',
                    ],
                    'AcMarche\Bottin\Cbe' => [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/src/AcMarche/Bottin/src/Cbe/Entity',
                        'prefix' => 'AcMarche\Bottin\Cbe',
                        'alias' => 'AcMarche\Bottin\Cbe',
                    ],
                ],
            ],
        ]
    );
};
