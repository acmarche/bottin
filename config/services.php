<?php

use AcMarche\Bottin\Elasticsearch\ElasticServer;
use AcMarche\Bottin\Namer\DirectoryNamer;
use AcMarche\Bottin\Parameter\Option;
use AcMarche\Bottin\Search\SearchElastic;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Security\LdapBottin;
use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::LDAP_DN, '%env(ACLDAP_DN)%');
    $parameters->set(Option::LDAP_USER, '%env(ACLDAP_USER)%');
    $parameters->set(Option::LDAP_PASSWORD, '%env(ACLDAP_PASSWORD)%');
    $parameters->set('bottin.cp_default', '6900');
    $parameters->set('bottin.url_update_category', '%env(BOTTIN_URL_UPDATE_CATEGORY)%');
    $parameters->set('bottin.email_from', '%env(EMAIL_FROM)%');
    $parameters->set('es_config', ['hosts' => 'http://localhost:9200']);
    $parameters->set('bootcdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private()
        ->bind('$elasticIndexName', ElasticServer::INDEX_NAME);

    $services->load('AcMarche\Bottin\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests}']);

    $services->set(DirectoryNamer::class)
        ->public();

    $services->set(Client::class)
        ->factory('@Elasticsearch\ClientBuilder::fromConfig')
        ->args(['%es_config%']);

    $services->alias(SearchEngineInterface::class, SearchElastic::class);

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
            ->tag('ldap'); //necessary for new LdapBadge(LdapMercredi::class)
    }
};
