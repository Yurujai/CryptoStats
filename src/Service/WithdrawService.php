<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Withdraw;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class WithdrawService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function add(float $amount, string $symbol, string $movedTo, ?string $comment, \DateTimeInterface $create): Withdraw
    {
        $withdraw = new Withdraw($amount, $symbol, $movedTo, $comment, $create);
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

    public function getAll()
    {
        return $this->documentManager->getRepository(Withdraw::class)->findAll();
    }

    public function getTotalFiatAmount()
    {
        // TODO
    }
}
