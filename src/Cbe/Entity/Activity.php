<?php

namespace AcMarche\Bottin\Cbe\Entity;

class Activity
{
    public string $activityGroup;
    public string $naceVersion;
    public string $naceCode;
    public string $classification;
    /**
     * @var array ['FR'=>'','NL'=>'']
     */
    public array $activityGroupDescription = [];
    public array $classificationDescription=[];
    public array $naceCodeDescription=[];

}
