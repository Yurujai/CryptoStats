<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Wallet;
use Doctrine\ODM\MongoDB\DocumentManager;

class WalletService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function create(string $exchangeName, string $symbol, string $available, string $inOrder, float $eurPrice, float $usdPrice): Wallet
    {
        $wallet = $this->walletExists($exchangeName, $symbol);

        if (!$wallet instanceof Wallet) {
            $wallet = $this->new($exchangeName, $symbol, $available, $inOrder, $eurPrice, $usdPrice);
            $this->saveOnMemory($wallet);
        } else {
            $wallet->setAmount((float) $available);
            $wallet->setInOrder((float) $inOrder);
            $wallet->setEURPRice($eurPrice);
            $wallet->setUSDPrice($usdPrice);
        }

        return $wallet;
    }

    public function new(string $exchangeName, string $symbol, string $available, string $inOrder, float $eurPrice, float $usdPrice): Wallet
    {
        return new Wallet(
            $exchangeName,
            $symbol,
            (float) $available,
            (float) $inOrder,
            $eurPrice,
            $usdPrice
        );
    }

    public function countWallets(?string $exchangeName = null): int
    {
        return count($this->aggregateWallets(['exchange' => $exchangeName]));
    }

    public function getAllWallets(string $exchangeName)
    {
        return $this->documentManager->getRepository(Wallet::class)->findBy(
            ['exchange' => $exchangeName],
            ['symbol' => 1]
        );
    }

    public function walletExists(string $exchangeName, string $symbol)
    {
        return $this->documentManager->getRepository(Wallet::class)->findOneBy([
            'symbol' => strtolower($symbol),
            'exchange' => $exchangeName,
        ]);
    }

    public function saveOnMemory(Wallet $wallet)
    {
        $this->documentManager->persist($wallet);
    }

    public function save()
    {
        $this->documentManager->flush();
    }

    public function aggregateWallets(?array $criteria = []): array
    {
        $collection = $this->documentManager->getDocumentCollection(Wallet::class);

        if (!empty($criteria)) {
            $pipeline[] = [
                '$match' => $criteria
            ];
        }

        $pipeline[] = [
            '$match' => [
                'amount' => ['$gt' => 0],
            ],
        ];

        $pipeline[] = [
            '$group' => [
                '_id' => '$symbol',
                'amount' => ['$sum' => '$amount'],
                'exchange' => ['$addToSet' => '$exchange'],
                'totalEUR' => ['$sum' => ['$multiply' => ['$eurPrice', '$amount']]],
                'totalUSD' => ['$sum' => ['$multiply' => ['$usdPrice', '$amount']]],
                'inOrderEUR' => ['$sum' => ['$multiply' => ['$eurPrice', '$inOrder']]],
                'inOrderUSD' => ['$sum' => ['$multiply' => ['$usdPrice', '$inOrder']]],
                'inOrder' => ['$sum' => '$inOrder'],
            ],
        ];

        $pipeline[] = [
            '$sort' => ['_id' => 1],
        ];

        return iterator_to_array($collection->aggregate($pipeline, ['cursor' => []]));
    }

    public function removeWallets(?string $exchangeName = null): void
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Wallet::class)->remove();
        if ($exchangeName) {
            $queryBuilder->field('exchange')->equals($exchangeName);
        }

        $queryBuilder->getQuery()->execute();
    }

    public function getTotalAmount(?string $exchange = null): array
    {
        $collection = $this->documentManager->getDocumentCollection(Wallet::class);

        if ($exchange) {
            $pipeline[] = [
                '$match' => [
                    'exchange' => $exchange,
                ],
            ];
        }

        $pipeline[] = [
            '$match' => [
                'amount' => ['$gt' => 0],
            ],
        ];

        $pipeline[] = [
            '$project' => [
                'total' => ['$sum' => ['$multiply' => ['$usdPrice', '$amount']]],
                'inOrderUSD' => ['$sum' => ['$multiply' => ['$usdPrice', '$inOrder']]],
            ]
        ];

        return iterator_to_array($collection->aggregate($pipeline, ['cursor' => []]));
    }

    public function getNumberOfCrypto(array $criteria): array
    {
        $collection = $this->documentManager->getDocumentCollection(Wallet::class);

        if ($criteria) {
            $pipeline[] = [
                '$match' => $criteria
            ];
        }

        $pipeline[] = [
            '$match' => [
                'amount' => ['$gt' => 0],
            ],
        ];

        $pipeline[] = [
            '$group' => [
                '_id' => '$symbol',
                'total' => ['$sum' => 1],
            ]
        ];

        return iterator_to_array($collection->aggregate($pipeline, ['cursor' => []]));
    }
}
