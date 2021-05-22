<?php

declare(strict_types=1);

namespace App\Command;

use App\Document\Market;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DownloadMarketDataCommand extends Command
{
    protected static $defaultName = 'crypto:download:market';
    private $client;
    private $documentManager;

    public function __construct(HttpClientInterface $client, DocumentManager $documentManager, $name = null)
    {
        parent::__construct($name);
        $this->client = $client;
        $this->documentManager = $documentManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Download market data')
            ->setHelp('Download market data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->removePreviousMarkets();

        $page = 1;
        $params = [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 250,
            'page' => $page,
            'sparkline' => false,
        ];

        $apiURL = 'https://api.coingecko.com/api/v3/coins/markets?';
        do {
            sleep(1);
            $params['page'] = $page;
            $url = $apiURL.http_build_query($params);
            $response = $this->client->request(
                'GET',
                $url
            );

            foreach ($response->toArray() as $element) {
                $market = new Market($element['id'], $element['name'], $element['symbol'], $element['image'], true);
                $this->documentManager->persist($market);
            }

            $this->documentManager->flush();
            ++$page;
        } while (count($response->toArray()) > 0);

        return 0;
    }

    private function removePreviousMarkets()
    {
        $this->documentManager->createQueryBuilder(Market::class)
            ->field('imported')->equals(true)
            ->remove()
            ->getQuery()
            ->execute();
    }
}
