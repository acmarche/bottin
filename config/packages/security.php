<?php

use Symfony\Component\Ldap\Ldap;
use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Security\BottinAuthenticator;
use AcMarche\Bottin\Security\BottinLdapAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\LdapInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'encoders' => [
            User::class => ['algorithm' => 'auto'],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'bottin_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [BottinAuthenticator::class];

    $main = [
        'provider' => 'bottin_user_provider',
        'logout' => ['path' => 'app_logout'],
        'form_login' => [],
        'entry_point' => BottinAuthenticator::class,
    ];

    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = BottinLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );
};
