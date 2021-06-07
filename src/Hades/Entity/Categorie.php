<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Categorie
{
    /**
     * @SerializedName("@id")
     */
    public string $id;
    /**
     * @SerializedName("@tri")
     */
    public int $tri;
    /**
     * @var Titre[]
     */
    public array $lib;

}
