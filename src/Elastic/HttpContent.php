<?php


namespace AcMarche\Bottin\Elastic;


use Symfony\Component\HttpClient\HttpClient;

class HttpContent
{
    public function getCategories()
    {

    }

    function getFicheById($id)
    {
        $client = HttpClient::create();
        return $client->request(
            'GET',
            'https://bottin.marche.be/api/fiches/' . $id . '.json',
            [
                'auth_basic' => ['**', '**'],
            ]
        );
    }

    function getFichesByCategory(int $categoryId)
    {
        $client = HttpClient::create();

        $url = 'https://bottin.marche.be/api/classements.json?category.id=' . $categoryId . '.json';

        $request = $client->request(
            'GET',
            $url,
            [
                'auth_basic' => ['**', '**'],
            ]
        );
        //echo $res->getStatusCode();
        return $request->getContent();
        //    return $this->convertfiche($this->getContent($url));
    }

}
