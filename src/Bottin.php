<?php

namespace AcMarche\Bottin;

/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 26/05/16
 * Time: 10:38.
 */
class Bottin
{
    public const ROLES = ['ROLE_BOTTIN_ADMIN', 'ROLE_BOTTIN'];
    public const url = 'https://www.marche.be/logo/adl/categories/';

    public const ROOTS = [
        self::ADMINISTRATION,
        self::CITOYEN,
        self::CULTURE,
        self::ECONOMIE,
        self::ENFANCE,
        self::SANTE,
        self::SOCIAL,
        self::SPORT,
    ];

    public const ADMINISTRATION = 664;
    public const CITOYEN = 483;
    public const CULTURE = 663;
    public const ECONOMIE = 511;
    public const ENFANCE = 671;
    public const SANTE = 488;
    public const SOCIAL = 487;
    public const SPORT = 486;

}
