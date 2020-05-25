<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Titre
{
    /**
     * @var string
     *
     * @SerializedName("@lg")
     */
    public $lg;

    /**
     * @var string
     *
     * @SerializedName("#")
     */
    public $value;
}
