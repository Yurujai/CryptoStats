<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Withdraw;
use App\Utils\PriceConversionUtils;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class WithdrawService
{
    private $documentManager;
    private $priceService;

    public function __construct(
        DocumentManager $documentManager,
        PriceService $priceService
    ) {
        $this->documentManager = $documentManager;
        $this->priceService = $priceService;
    }

    public function getAll()
    {
        return $this->documentManager->getRepository(Withdraw::class)->findAll();
    }

    public function add(float $amount, string $symbol, string $movedTo, \DateTimeInterface $create): Withdraw
    {
        $withdraw = new Withdraw($amount, $symbol, $movedTo, $create);
        $this->documentManager->persist($withdraw);
        $this->documentManager->flush();

        return $withdraw;
    }

    public function remove(string $id): void
    {
        $withdraw = $this->documentManager->getRepository(Withdraw::class)->findOneBy(['_id' => new ObjectId($id)]);
        if ($withdraw) {
            $this->documentManager->remove($withdraw);
        }

        $this->documentManager->flush();
    }

    public function getTotal(): float
    {
        $total = 0;
        $withdraws = $this->documentManager->getRepository(Withdraw::class)->findAll();

        foreach ($withdraws as $withdraw) {
            if ($withdraw->isFiatCoin()) {
                $price = ('eur' === $withdraw->getSymbol()) ? PriceConversionUtils::getUSDFromEUR($withdraw->getAmount()) : $withdraw->getAmount();
                $total += $price;
            }

            if ($withdraw->isStableCoin()) {
                $total += $withdraw->getAmount();
            }

            if ($withdraw->isCrypto()) {
                $amount = $withdraw->getAmount();
                $total += $amount * $this->priceService->getPriceOfSymbol($withdraw->getSymbol());
            }
        }

        return $total;
    }
}
