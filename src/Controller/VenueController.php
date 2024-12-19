<?php

namespace App\Controller;

use App\Entity\Venue;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VenueController extends AbstractController
{
    public function __construct(
        public HttpClientInterface $httpClient,
    )
    {

    }

    #[Route('/venue', name: 'app_venue')]
    public function index(): JsonResponse
    {
        $response = new JsonResponse();
        $client = $this->httpClient;
        $rawResponse = $client->request('GET', 'https://api.kinomax.ru/rest/cinemas', [
            'headers' => [
                'location-id' => 7
            ],

        ]);

        $decodedList = json_decode($rawResponse->getContent());
        $venueList = [];
        foreach ($decodedList as $placeItem) {
            $venue = new Venue();
            $venue
                ->setId($placeItem->id)
                ->setIdent($placeItem->ident)
                ->setName($placeItem->name)
                ->setAddress($placeItem->geo->address)
                ->setImage($placeItem->image)
            ;

            $venueList[] = $venue;
        }


        $new = array_map(function (Venue $v) {
            return [
                'ident' => $v->getIdent(),
                'name' => $v->getName(),
                'image' => 'https://images.kinomax.ru/1300' . $v->getImage(),
                'address' => $v->getAddress()
            ];
        }, $venueList);


        return $this->json([
            'venues' => $new,
        ]);
    }
}
