<?php

use AcMarche\Bottin\Namer\DirectoryNamer;
use AcMarche\Bottin\Security\Ldap\LdapBottin;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('bottin.cp_default', '6900');
    $parameters->set('bottin.url_update_category', '%env(BOTTIN_URL_UPDATE_CATEGORY)%');
    $parameters->set('bottin.email_from', '%env(EMAIL_FROM)%');
    $parameters->set('bootcdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\Bottin\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests}']);

    $services->set(DirectoryNamer::class)
        ->public();

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Ldap::class)
            ->args(['@Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'])
            ->tag('ldap');
        $services->set(Adapter::class)->args(
            [
                [
                    'host' => '%env(ACLDAP_URL)%',
                    'port' => 636,
                    'encryption' => 'ssl',
                    'options' => [
                        'protocol_version' => 3,
                        'referrals' => false,
                    ],
                ],
            ]
        );

        $services->set(LdapBottin::class)
            ->arg('$adapter', service(Adapter::class))
            ->tag('ldap'); // necessary for new LdapBadge(LdapMercredi::class)
    }
};
