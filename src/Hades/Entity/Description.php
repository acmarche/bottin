<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Description
{
    /**
     * @var string
     * @SerializedName("@data")
     */
    public $dat;
    /**
     * @var string
     * @SerializedName("@lot")
     */
    public $lot;
    /**
     * @var int
     * @SerializedName("@tri")
     */
    public $tri;
    /**
     * @var string
     * @SerializedName("@typ")
     */
    public $typ;
    /**
     * @var Titre[]
     *
     */
    public $lib;

    /**
     * @var Titre[]
     */
    public $texte;
}
