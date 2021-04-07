<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Investment;
use Doctrine\ODM\MongoDB\DocumentManager;

class InvestmentService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function addInvestment(float $amount, string $currency): Investment
    {
        $investment = new Investment((float) $amount, $currency);
        $this->documentManager->persist($investment);
        $this->documentManager->flush();

        return $investment;
    }

    public function getInvestment()
    {
        return $this->documentManager->getRepository(Investment::class)->findOneBy([]);
    }
}
