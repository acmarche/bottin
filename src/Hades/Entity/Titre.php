<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Titre
{
    /**
     *
     * @SerializedName("@lg")
     */
    public string $lg;

    /**
     *
     * @SerializedName("#")
     */
    public string $value;
}
