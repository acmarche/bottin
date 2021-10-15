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
        self::TOURISME,
    ];

    public const ADMINISTRATION = 664;
    public const CITOYEN = 483;
    public const CULTURE = 663;
    public const ECONOMIE = 511;
    public const ENFANCE = 671;
    public const SANTE = 488;
    public const SOCIAL = 487;
    public const SPORT = 486;
    public const TOURISME = 485;

    public const EMAILS = [
        self::ADMINISTRATION => 'cst@marche.be',
        self::CITOYEN => 'cst@marche.be',
        self::CULTURE => 'animation@marche.be',
        self::ECONOMIE => 'adl@marche.be',
        self::ENFANCE => 'epe@marche.be',
        self::SANTE => 'sante@marche.be',
        self::SOCIAL => 'pssp@marche.be',
        self::SPORT => 'csl@marche.be',
        self::TOURISME => 'animation@marche.be',
    ];
}
