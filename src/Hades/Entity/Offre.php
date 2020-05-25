<?php


namespace AcMarche\Bottin\Hades\Entity;


use Symfony\Component\Serializer\Annotation\SerializedName;

class Offre
{
    /**
     * @var string
     * @SerializedName("@id")
     */
    public $id;
    public $modif_date;
    public $publiable;
    public $oldkey;
    public $oldtab;
    public $off_id_ref;
}
