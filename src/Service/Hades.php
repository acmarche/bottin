<?php

namespace AcMarche\Bottin\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Hades
{
    protected $username;
    protected $password ;
    protected $urlBase;
    protected $curl;
    protected $commune = 263;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * Hades constructor.
     */
    public function __construct()
    {
        $this->httpClient = HttpClient::create(
            [
                'auth_basic' => [$this->username, $this->password],
            ]
        );
    }

    public function getOffres($criteres)
    {
        $params = "tbl=xmlcomplet&com_id=$this->commune&";
        $params = $params.$criteres;

        try {
            $response = $this->httpClient->request(
                'GET',
                $this->urlBase.$params,
                [
                ]
            );
        } catch (TransportExceptionInterface $e) {
            return ['error' => $e->getMessage()];
        }

        try {
            return $this->loadXml($response->getContent());
        } catch (ClientExceptionInterface $e) {
            return ['error' => $e->getMessage()];
        } catch (RedirectionExceptionInterface $e) {
            return ['error' => $e->getMessage()];
        } catch (ServerExceptionInterface $e) {
            return ['error' => $e->getMessage()];
        } catch (TransportExceptionInterface $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function loadXml($data)
    {
        $xml = simplexml_load_string($data);
        if (false === $xml) {
            return $this->returnError();
        }

        return $xml->offres->offre;
    }

    public function getIdOffre(\SimpleXMLElement $element)
    {
        if ($element->attributes()) {
            if (isset($element->attributes()->id)) {
                return (int) $element->attributes()->id;
            }
        }

        return 0;
    }

    /**
     * @param \SimpleXMLElement $element
     *
     * @return array("principal","contact","administratif")
     */
    public function getContacts(\SimpleXMLElement $element)
    {
        $contacts = [];

        $elements = (array) $element->contacts;

        if (!isset($elements['contact'])) {
            return [];
        }

        $i = 0;
        foreach ($elements['contact'] as $contact) {
            $libelles = (array) $contact->lib; //0=>clef,1=>fr,2=>nl...
            $type = $libelles[0]; //ap, contact, proprio
            $contacts[$type]['tri'] = (string) $contact->attributes()->tri;

            $libelle_fr = $libelles[1];
            $contacts[$type]['libelle'] = $libelle_fr;
            $contacts[$type]['noms'] = (string) $contact->noms;
            $contacts[$type]['prenoms'] = (string) $contact->prenoms;
            $contacts[$type]['civilite'] = (string) $contact->civilite;
            $contacts[$type]['societe'] = (string) $contact->societe;
            $contacts[$type]['adresse'] = (string) $contact->adresse;
            $contacts[$type]['numero'] = (string) $contact->numero;
            $contacts[$type]['postal'] = (string) $contact->postal;
            $contacts[$type]['pays'] = (string) $contact->pays;
            $contacts[$type]['localite_nom'] = (string) $contact->l_nom;

            $contacts[$type]['communications'] = $this->getCommunications((array) $contact->communications);
        }

        return $contacts;
    }

    public function getCommunications(array $data)
    {
        $communications = [];
        $i = 0;
        foreach ($data as $rows) {
            foreach ($rows as $communication) {
                $communications[$i]['tri'] = (string) $communication->attributes()->tri;
                $communications[$i]['type'] = (string) $communication->attributes()->typ; //tel,telmob,mail,url

                $libelles = (array) $communication->lib;
                $communications[$i]['libelle_clef'] = $libelles[0];
                $communications[$i]['libelle_fr'] = $libelles[1];
                $communications[$i]['value'] = (string) $communication->val;
                ++$i;
            }
        }

        return $communications;
    }

    public function getMedias(\SimpleXMLElement $element)
    {
        $medias = [];
        $i = 0;
        $elements = (array) $element->medias;

        if (!isset($elements['media'])) {
            return [];
        }

        $datas = ($elements['media'] instanceof \SimpleXMLElement) ? [$elements['media']] : $elements['media'];

        foreach ($datas as $element) {
            $medias[$i]['ext'] = (string) $element->attributes()->ext;
            $medias[$i]['ord'] = (string) $element->attributes()->ord;
            $medias[$i]['url'] = (string) $element->url;
            $libelles = $element->titre;
            $medias[$i]['titre_fr'] = (string) $libelles[0];
            $medias[$i]['titre_nls'] = (string) $libelles[1];
            $medias[$i]['titre_eng'] = (string) $libelles[2];
            $medias[$i]['titre_deu'] = (string) $libelles[3];
            ++$i;
        }

        return $medias;
    }

    public function getCategories(\SimpleXMLElement $element)
    {
        $categories = [];
        $i = 0;
        $elements = (array) $element->categories;

        foreach ($elements as $element) {
            $categories[$i]['clef_offre'] = (string) $element->attributes()->id;
            $categories[$i]['tri'] = (string) $element->attributes()->tri;
            $libelles = $element->lib;
            $categories[$i]['titre_fr'] = (string) $libelles[0];
            $categories[$i]['titre_fr_single'] = (string) $libelles[1];
            $categories[$i]['titre_nls'] = (string) $libelles[2];
            $categories[$i]['titre_eng'] = (string) $libelles[3];
            $categories[$i]['titre_deu'] = (string) $libelles[4];
            ++$i;
        }

        return $categories;
    }

    public function getLocalisations(\SimpleXMLElement $element)
    {
        $data = [];
        $localisation = (array) $element->localisation;
        $object = $localisation['localite'];
        $localite_id = (int) $object->attributes()->id;

        $data['localite_id'] = $localite_id;
        $data['localite_nom'] = (string) $object->l_nom;
        $data['code_postal'] = (string) $object->postal;
        //$data['longitude'] = (string)$object->x; pas precis
        //$data['latitude'] = (string)$object->y;
        $data['commune_id'] = (string) $object->com_id;
        $data['commune_nom'] = (string) $object->c_nom;
        $data['region_id'] = (string) $object->reg_id;

        $geocodes = (array) $element->geocodes;
        $geocode = $geocodes['geocode'];
        $data['longitude'] = (string) $geocode->x;
        $data['latitude'] = (string) $geocode->y;

        return $data;
    }

    public function getDescriptions(\SimpleXMLElement $element)
    {
        $descriptions = [];
        $elements = (array) $element->descriptions;

        $datas = isset($elements['description']) ? $elements['description'] : null;

        if (!$datas) {
            return [];
        }

        //si qu' une description
        if ($datas instanceof \SimpleXMLElement) {
            $datas = [0 => $datas];
        }

        $i = 0;
        foreach ($datas as $description) {
            $descriptions[$i]['temporisation'] = (string) $description->attributes()->dat; //tjr, ann, dad
            $descriptions[$i]['type_texte'] = (string) $description->attributes()->typ; //html ou non, multiligne ou pas
            $descriptions[$i]['lot'] = (string) $description->attributes()->lot;
            $descriptions[$i]['tri'] = (string) $description->attributes()->tri;

            $libelles = (array) $description->lib;
            $descriptions[$i]['lib_identifiant'] = $libelles[0];
            $lib_fr = $libelles[1]; //Remarque
            $lib_nls = $libelles[2]; //Opmerking
            $lib_eng = $libelles[3]; //Notice
            $lib_deu = $libelles[4]; //Anmerkung

            $textes = $description->texte;
            $descriptions[$i]['texte_fr'] = (string) $textes[0];
            $descriptions[$i]['texte_nls'] = (string) $textes[1];
            $descriptions[$i]['texte_eng'] = (string) $textes[2];
            $descriptions[$i]['texte_deu'] = (string) $textes[3];

            $descriptions[$i]['date_debut'] = (string) $description->date_deb;
            $descriptions[$i]['date_fin'] = (string) $description->date_fin;

            ++$i;
        }

        return $descriptions;
    }

    public function getHoraires(\SimpleXMLElement $element)
    {
        $horaires = [];
        $i = 0;
        $elements = (array) $element->horaires;
        if (0 == count($horaires)) {
            return [];
        }

        foreach ($elements as $element) {
            $horaires[$i]['clef_offre'] = (string) $element->attributes()->id;
            $horaires[$i]['tri'] = (string) $element->attributes()->tri;
            $libelles = $element->lib;
            $horaires[$i]['titre_fr'] = (string) $libelles[0];
            $horaires[$i]['titre_fr_single'] = (string) $libelles[1];
            $horaires[$i]['titre_nls'] = (string) $libelles[2];
            $horaires[$i]['titre_eng'] = (string) $libelles[3];
            $horaires[$i]['titre_deu'] = (string) $libelles[4];
            ++$i;
        }

        return $horaires;
    }

    public function returnError()
    {
        return libxml_get_errors();
    }
}
