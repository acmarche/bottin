<?php

use AcMarche\Bottin\Entity\User;
use AcMarche\Bottin\Security\BottinAuthenticator;
use AcMarche\Bottin\Security\BottinLdapAuthenticator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {

    $security->provider('bottin_user_provider')
        ->entity()
        ->class(User::class)
        ->property('username');

    // @see Symfony\Config\Security\FirewallConfig
    $main = [
        'provider' => 'bottin_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => BottinAuthenticator::class,
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
        'remember_me' => [
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ],
    ];

    $authenticators = [BottinAuthenticator::class];
    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = BottinLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    $main['custom_authenticators'] = $authenticators;
    $security->firewall('main', $main);
};
