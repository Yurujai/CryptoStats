<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use MongoDB\BSON\ObjectId;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\WalletRepository")
 */
class Wallet
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $exchange;

    /**
     * @MongoDB\Field(type="string")
     */
    private $symbol;

    /**
     * @MongoDB\Field(type="float")
     */
    private $amount;

    /**
     * @MongoDB\Field(type="float")
     */
    private $inOrder;

    /**
     * @MongoDB\Field(type="float")
     */
    private $eurPrice;

    /**
     * @MongoDB\Field(type="float")
     */
    private $usdPrice;

    public function __construct(string $exchange, string $symbol, float $amount, float $inOrder, float $eurPrice, float $usdPrice)
    {
        $this->id = new ObjectId();
        $this->exchange = $exchange;
        $this->symbol = strtolower($symbol);
        $this->amount = $amount;
        $this->inOrder = $inOrder;
        $this->eurPrice = $eurPrice;
        $this->usdPrice = $usdPrice;
    }

    public function getId(): ObjectId
    {
        return $this->id;
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function setExchange(string $exchange): void
    {
        $this->exchange = $exchange;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = strtolower($symbol);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getInOrder(): float
    {
        return $this->inOrder;
    }

    public function setInOrder(float $inOrder): void
    {
        $this->inOrder = $inOrder;
    }

    public function getEurPrice(): float
    {
        return $this->eurPrice;
    }

    public function setEurPrice(float $eurPrice): void
    {
        $this->eurPrice = $eurPrice;
    }

    public function getUsdPrice(): float
    {
        return $this->usdPrice;
    }

    public function setUsdPrice(float $usdPrice): void
    {
        $this->usdPrice = $usdPrice;
    }
}
