<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Market;
use Doctrine\ODM\MongoDB\DocumentManager;

class MarketService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function getInfoOfAsset(string $symbol)
    {
        $market = $this->documentManager->getRepository(Market::class)->findOneBy([
            'symbol' => $symbol,
        ]);

        if($market instanceof Market) {
            return $market;
        }

        return [];
    }
}
