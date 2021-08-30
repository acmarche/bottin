<?php

use AcMarche\Bottin\Hades\HadesRepository;
use AcMarche\Bottin\Namer\DirectoryNamer;
use AcMarche\Bottin\Parameter\Option;
use AcMarche\Bottin\Search\SearchElastic;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Security\LdapBottin;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\LdapInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::LDAP_DN, '%env(ACLDAP_DN)%');
    $parameters->set(Option::LDAP_USER, '%env(ACLDAP_USER)%');
    $parameters->set(Option::LDAP_PASSWORD, '%env(ACLDAP_PASSWORD)%');
    $parameters->set('bottin.cp_default', '6900');
    $parameters->set('router.request_context.scheme', 'bottin.local');
    $parameters->set('router.request_context.host', 'http');
    $parameters->set('bottin.url_update_category', '%env(BOTTIN_URL_UPDATE_CATEGORY)%');
    $parameters->set('bottin.email_from', '%env(EMAIL_FROM)%');
    $parameters->set('es_config', ['hosts' => 'http://localhost:9200']);

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private()
        ->bind('$elasticIndexName', AcMarche\Bottin\Elasticsearch\ElasticServer::INDEX_NAME);

    $services->load('AcMarche\Bottin\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests}']);

    $services->set(DirectoryNamer::class)
        ->public();

    $services->set(HadesRepository::class)
        ->args([
            '$url' => '%env(HADES_URL)%',
            '$user' => '%env(HADES_USER)%',
            '$password' => '%env(HADES_PASSWORD)%',
        ]);

    $services->alias(LoaderInterface::class, 'fidry_alice_data_fixtures.loader.doctrine');
    $services->set(Elasticsearch\ClientBuilder::class);

    $services->set(Elasticsearch\Client::class)
        ->factory('@Elasticsearch\ClientBuilder::fromConfig')
        ->args(['%es_config%']);

    $services->alias(SearchEngineInterface::class, SearchElastic::class);

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Symfony\Component\Ldap\Ldap::class)
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
            ->arg('$adapter', service('Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'))
            ->tag('ldap'); //necessary for new LdapBadge(LdapMercredi::class)
    }
};
