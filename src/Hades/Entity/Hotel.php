<?php


namespace AcMarche\Bottin\Hades\Entity;

class Hotel implements OffreInterface
{
    public function getId(): int
    {
        return (int)$this->hot_id[0];
    }

    public function getTitre(): string
    {
        return $this->hot_titre;
    }

    public function getDescriptions(): array
    {
        $comments1 = $this->hot_desc_com_fr.' '.$this->hot_remarque_fr;

        return [$comments1, $this->getHoraire(), $this->hot_desc_fr];
    }

    public function getHoraire(): ?string
    {
        return $this->hot_ferm_fr;
    }

    public function getLatitude(): ?float
    {
        return $this->hot_gpsx;
    }

    public function getLongitude(): ?float
    {
        return $this->hot_gpsy;
    }

    public function getRue(): ?string
    {
        return $this->hot_adresse;
    }

    public function getLocalite(): ?string
    {
        return $this->loc_nom;
    }

    public function getCommune(): ?string
    {
        return $this->com_localite;
    }

    public function getCodePostal(): ?string
    {
        return $this->loc_cp;
    }

    public function getNbEtoile(): ?int
    {
        return $this->hot_etoiles;
    }

    public function getCivilite(): ?string
    {
        return $this->hot_contact_sex;
    }

    public function getContactNom(): ?string
    {
        return $this->hot_contact_nom;
    }

    public function getTelephone(): ?string
    {
        return $this->hot_telephone;
    }

    public function getFax(): ?string
    {
        return $this->hot_fax;
    }

    public function getEmail(): ?string
    {
        return $this->hot_email;
    }

    public function getWebsite(): ?string
    {
        return $this->hot_url;
    }

    public $hot_id;
    public $hot_codecgt;
    public $hot_inscrit;
    public string $hot_titre;
    public ?int $hot_etoiles;
    public ?string $hot_adresse;
    public $hot_cp;
    public $loc_id;
    public ?string $hot_contact_nom;
    public ?string $hot_contact_sex;
    public ?string $hot_telephone;
    public ?string $hot_fax;
    public ?string $hot_email;
    public ?string $hot_url;
    public $hot_accueil_fr;
    public $hot_accueil_nl;
    public $hot_accueil_en;
    public $hot_accueil_de;
    public $hot_tva;
    public $hot_capacite;
    public $hot_chbr_tot;
    public $hot_chbr_douche;
    public $hot_chbr_bain;
    public $hot_chbr_douche_wc;
    public $hot_chbr_bain_wc;
    public $hot_chbr_fam_tot;
    public $hot_chbr_fam_max;
    public $hot_cap_resto;
    public $hot_cap_conf;
    public ?string $hot_ferm_fr;
    public $hot_desc_fr;
    public $hot_desc_nl;
    public $hot_desc_en;
    public $hot_desc_de;
    public $hot_desc_com_fr;
    public $hot_desc_com_nl;
    public $hot_desc_com_en;
    public $hot_desc_com_de;
    public ?float $hot_gpsx;
    public ?float $hot_gpsy;
    public $hot_lab_vert;
    public $hot_logis;
    public $hot_lab_relsilence;
    public $hot_lab_relchateau;
    public $hot_lab_chatetdem;
    public $hot_lab_hotdefrance;
    public $hot_c_anichambre;
    public $hot_c_anirest;
    public $hot_c_bar;
    public $hot_c_chbrhand;
    public $hot_c_fumchbr;
    public $hot_c_fumoir;
    public $hot_c_internet;
    public $hot_c_minibar;
    public $hot_c_ptdejchbr;
    public $hot_c_radio;
    public $hot_c_resthand;
    public $hot_c_schcheveux;
    public $hot_c_servrev;
    public $hot_c_teldirect;
    public $hot_c_terbalcon;
    public $hot_c_tvchbr;
    public $hot_c_wifi;
    public $hot_c_ascen;
    public $hot_c_coffre;
    public $hot_c_creditcard;
    public $hot_c_equitation;
    public $hot_c_fitness;
    public $hot_c_garage;
    public $hot_c_jacuzzi;
    public $hot_c_jeuxext;
    public $hot_c_kayak;
    public $hot_c_parcjardin;
    public $hot_c_parking;
    public $hot_c_peche;
    public $hot_c_piscinein;
    public $hot_c_piscineout;
    public $hot_c_salletv;
    public $hot_c_sauna;
    public $hot_c_ski;
    public $hot_c_solarium;
    public $hot_c_tennis;
    public $hot_formvalid;
    public $hot_mod_dat;
    public $h2o_import;
    public $h2o_id;
    public $hot_prix_pers;
    public $hot_annee;
    public $hot_tf_mod_dat;
    public $lastmod;
    public ?string $loc_cp;
    public ?string $loc_nom;
    public ?string $com_localite;
    public $pay_id;
    public $com_id;
    public $photo;
    public $hot_suite;
    public $lien_reservation_ext_fr;
    public $lien_reservation_ext_nl;
    public $lien_reservation_ext_en;
    public $hot_remarque_fr;
    public $hot_pxdejmin;
    public $hot_pxdejmax;
    public $hot_pxdemmin;
    public $hot_pxdemmax;
    public $hot_single_fr;
    public $hot_single_nl;
    public $hot_single_en;
    public $hot_single_de;
}
