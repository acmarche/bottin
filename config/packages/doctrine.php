<?php

use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (DoctrineConfig $doctrine): void {
    $doctrine->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve());
    $doctrine->dbal()
        ->connection('cap')
        ->url(env('DATABASE_URL_CAP')->resolve());

    $doctrine->dbal()->defaultConnection('default');

    // Entity Managers:
    $doctrine->orm()->defaultEntityManager('default');
    $defaultEntityManager = $doctrine->orm()->entityManager('default');
    $defaultEntityManager->connection('default');
    $defaultEntityManager->mapping('Main')
        ->isBundle(false)
        ->dir('%kernel.project_dir%/src/AcMarche/Bottin/src/Entity')
        ->prefix('AcMarche\Bottin')
        ->alias('Main');
    $customerEntityManager = $doctrine->orm()->entityManager('cap');
    $customerEntityManager->connection('cap');
    $customerEntityManager->mapping('Cap')
        ->isBundle(false)
        ->dir('%kernel.project_dir%/src/AcMarche/Cap/src/Entity')
        ->prefix('AcMarche\Cap')
        ->alias('Cap')
    ;
};