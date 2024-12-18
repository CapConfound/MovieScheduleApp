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

        $decodedLsit = json_decode($rawResponse->getContent());

        $venueList = [];
        foreach ($decodedLsit as $placeItem) {
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
        $new = array_map(function ($v) {
            return $v->getName();
        }, $venueList);


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VenueController.php',
            'names' => $new,
            'array' => $venueList
        ]);
    }
}
