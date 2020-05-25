<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Categorie
{
    /**
     * @var string
     * @SerializedName("@id")
     */
    public $id;
    /**
     * @var int
     * @SerializedName("@tri")
     */
    public $tri;
    /**
     * @var Titre[]
     */
    public $lib;

}
