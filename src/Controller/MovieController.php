<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Session;
use App\Helper\KinomaxSelectorDictionary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieController extends AbstractController
{
    public function __construct(
        public HttpClientInterface $httpClient,
        public Crawler $crawler,
        public KinomaxSelectorDictionary $kinomaxSelectorDictionary
    ) {
    }

    #[Route('/movies', name: 'app_movie', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $params = $request->getContent();
        $params = json_decode($params, true);
        $dictionary = $this->kinomaxSelectorDictionary;
        $venue = $params['venue'] ?? null;

        if (empty($venue)) {
            return $this->json([
                'success' => false,
                'message' => 'Venue is empty'
            ]);
        }

        $date = '';
        if (!empty($params['date'])) {
            $date = $params['date'];

            $dateObj = new \DateTime($date);
            $date = '/' . $dateObj->format('Y-m-d');
        }

        $client = $this->httpClient;
        $rawResponse = $client->request('GET', 'https://kinomax.ru/' . $venue . $date);
        $htmlContent = $rawResponse->getContent();

        $crawler = new Crawler();
        $crawler->addHtmlContent($htmlContent);

        $movies = $crawler->filter($dictionary::OUTER_MOVIE_DIV);
//        dump($movies->text("Empty"));
//        dump($movies->html());
//        dump($movies->filter('h4')->text("Empty html"));
//        dump(count($movies->filter($dictionary::OUTER_MOVIE_DIV . ' ' . $dictionary::IMAGE_TAG)));
//        dump($movies->filter($dictionary::OUTER_MOVIE_DIV . ' ' . $dictionary::IMAGE_TAG)->text('111'));


        $moviesList = [];

        $movies->filter($dictionary::OUTER_MOVIE_DIV)->each(function (Crawler $movieDiv) use ($dictionary, &$moviesList) {

            $movie = new Movie();
            $movieDiv->filter($dictionary::OUTER_MOVIE_DIV)->each(function (Crawler $movieInnerDiv) use ($dictionary, $movieDiv, $movie) {
                $movie
                    ->setName($movieDiv->filter('h4')->text("Empty title"))
                    ->setImage($movieDiv->filter($dictionary::IMAGE_TAG)->attr('src'))
                ;

            });

            $sessionsList = [];

            $movieDiv->filter($dictionary::SESSION_DIV)->each(function (Crawler $movieSessionDiv) use (&$sessionsList, $dictionary, $movieDiv, $movie) {

                $sessionObj = new Session();
                $movieSessionDiv->filter($dictionary::SESSION_DIV)->each(function (Crawler $session) use ($dictionary, $movieDiv, $sessionObj, $movie, &$sessionsList) {

                    $sessionObj
                        ->setMovie($movie)
                        ->setFormat($session->filter($dictionary::SESSION_FORMAT)->text("Empty format"))
                        ->setTime($session->filter($dictionary::SESSION_TIME)->text("Empty time"))
                        ->setPrice((int) trim(str_replace('от', '', $session->filter($dictionary::SESSION_PRICE)->text("Empty price"))))
                    ;

                    $sessionsList[] = $sessionObj;
//                    dump([
//                        "s_time" => $session->filter($dictionary::SESSION_TIME)->text("Empty time"),
//                        "s_price" => $session->filter($dictionary::SESSION_PRICE)->text("Empty price"),
//                        "s_format" => $session->filter($dictionary::SESSION_FORMAT)->text("Empty format"),
//                    ]);

//                    dump($sessionsList);
                });



            });

            foreach($sessionsList as $sessionObj) {
                $movie->addSession($sessionObj);
            }

//            dump(
//                $movie,
//
//            );

            $moviesList[] = $movie;


        });

        $moviesResponse = array_map(function (Movie $movie) {
            return [
                "name" => $movie->getName(),
                "image" => $movie->getImage(),
                "sessions" => $movie->getSessionsArray()
            ];
        }, $moviesList);


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MovieController.php',
            'date' => $date,
            'movie' => $moviesResponse,
        ]);
    }
}
