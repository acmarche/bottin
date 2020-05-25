<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Localite
{
    /**
     * @var int
     * @SerializedName("@id")
     */
    public $id;
    public $l_nom;
    public $postal;
    public $x;
    public $y;
    public $com_id;
    public $c_nom;
    public $reg_id;
}
