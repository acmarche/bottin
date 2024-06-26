<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('liip_imagine', ['resolvers' => ['default' => ['web_path' => null]]]);

    $containerConfigurator->extension(
        'liip_imagine',
        [
            'filter_sets' => [
                'cache' => null,
                'acbottin_thumb' => [
                    'quality' => 100,
                    'filters' => ['thumbnail' => ['size' => [250, 188], 'mode' => 'inset']],
                ],
                'circuitcourt_thumb' => [
                    'quality' => 95,
                    'filters' => ['thumbnail' => ['size' => [1200], 'mode' => 'inset']],
                ],
            ],
        ]
    );
};
