<?php

use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    $security->firewall('main')
        ->provider('bottin_sql_provider')
        ->lazy(true);

    $security
        ->accessControl(['path' => '^/admin', 'roles' => 'ROLE_ADMIN']);
};
