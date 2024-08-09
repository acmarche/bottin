<?php

use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (DoctrineConfig $doctrine) {
    $doctrine->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve());

    $em = $doctrine->orm()->entityManager('default');
    $em->connection('default');

    $em->mapping('AcMarcheBottin')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Bottin/src/Entity')
        ->prefix('AcMarche\Bottin')
        ->alias('AcMarcheBottin');
};
