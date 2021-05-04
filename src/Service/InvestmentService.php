<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Investment;
use App\Utils\PriceConversionUtils;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class InvestmentService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function add(float $amount, string $currency, string $exchange, \DateTimeInterface $added): Investment
    {
        $investment = new Investment($amount, $currency, $exchange, $added);
        $this->documentManager->persist($investment);
        $this->documentManager->flush();

        return $investment;
    }

    public function remove(string $id): void
    {
        $investment = $this->documentManager->getRepository(Investment::class)->findOneBy(['_id' => new ObjectId($id)]);
        if ($investment) {
            $this->documentManager->remove($investment);
        }

        $this->documentManager->flush();
    }

    public function getAll()
    {
        return $this->documentManager->getRepository(Investment::class)->findAll();
    }

    public function getInvestment()
    {
        return $this->documentManager->getRepository(Investment::class)->findOneBy([]);
    }

    public function getTotalEURAmount(): float
    {
        $investments = $this->documentManager->getRepository(Investment::class)->findAll();

        $total = 0;
        foreach ($investments as $investment) {
            if ('usd' === $investment->getCurrency()) {
                $total += PriceConversionUtils::getEURFromUSD($investment->getAmount());
            } else {
                $total += $investment->getAmount();
            }
        }

        return $total;
    }

    public function getTotalUSDAmount(): float
    {
        $investments = $this->documentManager->getRepository(Investment::class)->findAll();

        $total = 0;
        foreach ($investments as $investment) {
            if ('eur' === $investment->getCurrency()) {
                $total += PriceConversionUtils::getUSDFromEUR($investment->getAmount());
            } else {
                $total += $investment->getAmount();
            }
        }

        return $total;
    }

    public function getTotalInvestment(): float
    {
        return $this->getTotalUSDAmount();
    }
}
