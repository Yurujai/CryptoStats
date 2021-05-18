<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrendingController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function trendingTopics(): Response
    {
        $response = $this->client->request(
            'GET',
            'https://api.coingecko.com/api/v3/search/trending'
        );

        $content = $response->toArray();

        return $this->render('/block/trends.html.twig', [
            'title' => 'TOP 7 Coingecko trends',
            'trends' => $content['coins'],
        ]);
    }
}
