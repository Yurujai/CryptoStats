<?php

declare(strict_types=1);

namespace App\Command;

use App\Document\Exchange;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DownloadExchangeDataCommand extends Command
{
    protected static $defaultName = 'crypto:download:exchange';
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
            ->setDescription('Download exchange data')
            ->setHelp('Download exchange data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->removePreviousMarkets();

        $page = 1;
        $params = [
            'per_page' => 250,
            'page' => $page,
        ];

        $apiURL = 'https://api.coingecko.com/api/v3/exchanges?';
        do {
            sleep(1);
            $params['page'] = $page;
            $url = $apiURL.http_build_query($params);
            $response = $this->client->request(
                'GET',
                $url
            );

            foreach ($response->toArray() as $element) {
                $exchange = new Exchange($element['id'], $element['name'], $element['image'], $element['url'], true);
                $this->documentManager->persist($exchange);
            }

            $this->documentManager->flush();
            ++$page;
        } while (count($response->toArray()) > 0);

        $this->importCustomExchange();

        return 0;
    }

    private function removePreviousMarkets()
    {
        $this->documentManager->createQueryBuilder(Exchange::class)
            ->field('imported')->equals(true)
            ->remove()
            ->getQuery()
            ->execute();
    }

    private function importCustomExchange()
    {
        $customExchange1 = 'bitvavo';
        $customExchange1Exists = $this->documentManager->getRepository(Exchange::class)->findOneBy(['alternativeId' => $customExchange1]);
        if (!$customExchange1Exists) {
            $exchange = new Exchange(
                $customExchange1,
                'Bitvavo',
                'https://bitvavo.com',
                'https://bitvavo.com/assets/img/fav/apple-touch-icon.png',
                false
            );
            $this->documentManager->persist($exchange);
            $this->documentManager->flush();
        }
    }
}
