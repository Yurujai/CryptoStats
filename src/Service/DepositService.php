<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Deposit;
use App\Utils\PriceConversionUtils;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class DepositService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function add(float $amount, string $currency, string $exchange, \DateTimeInterface $added): Deposit
    {
        $deposit = new Deposit($amount, $currency, $exchange, $added);
        $this->documentManager->persist($deposit);
        $this->documentManager->flush();

        return $deposit;
    }

    public function remove(string $id): void
    {
        $deposit = $this->documentManager->getRepository(Deposit::class)->findOneBy(['_id' => new ObjectId($id)]);
        if ($deposit) {
            $this->documentManager->remove($deposit);
        }

        $this->documentManager->flush();
    }

    public function getAll()
    {
        return $this->documentManager->getRepository(Deposit::class)->findAll();
    }

    public function getTotalUSDAmount(): float
    {
        $deposits = $this->documentManager->getRepository(Deposit::class)->findAll();

        $total = 0;
        foreach ($deposits as $deposit) {
            if ('eur' === $deposit->getSymbol()) {
                $total += PriceConversionUtils::getUSDFromEUR($deposit->getAmount());
            } else {
                $total += $deposit->getAmount();
            }
        }

        return $total;
    }

    public function getTotal(): float
    {
        return $this->getTotalUSDAmount();
    }
}
