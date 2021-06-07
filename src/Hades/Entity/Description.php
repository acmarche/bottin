<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Description
{
    /**
     * @SerializedName("@data")
     */
    public string $dat;
    /**
     * @SerializedName("@lot")
     */
    public string $lot;
    /**
     * @SerializedName("@tri")
     */
    public int $tri;
    /**
     * @SerializedName("@typ")
     */
    public string $typ;
    /**
     * @var Titre[]
     *
     */
    public array $lib;

    /**
     * @var Titre[]
     */
    public array $texte;
}
