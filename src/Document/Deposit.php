<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use MongoDB\BSON\ObjectId;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\DepositRepository")
 */
class Deposit
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="float")
     */
    private $amount;

    /**
     * @MongoDB\Field(type="date")
     */
    private $create;

    /**
     * @MongoDB\Field(type="date")
     */
    private $added;

    /**
     * @MongoDB\Field(type="string")
     */
    private $symbol;

    /**
     * @MongoDB\Field(type="string")
     */
    private $exchange;

    public function __construct(float $amount, string $symbol, string $exchange, \DateTimeInterface $added)
    {
        $this->id = new ObjectId();
        $this->amount = $amount;
        $this->symbol = strtolower($symbol);
        $this->create = new \DateTime();
        $this->added = $added;
        $this->exchange = $exchange;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function setExchange(string $exchange): void
    {
        $this->exchange = $exchange;
    }

    public function getCreate(): \DateTime
    {
        return $this->create;
    }

    public function setCreate(\DateTime $create): void
    {
        $this->create = $create;
    }

    public function getAdded(): \DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): void
    {
        $this->added = $added;
    }
}
