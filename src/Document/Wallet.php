<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\Utils\CryptoUtils;
use MongoDB\BSON\ObjectId;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\WalletRepository")
 */
class Wallet
{
    protected const WALLET_TYPE_FIAT = 1;
    protected const WALLET_TYPE_STABLE = 2;
    protected const WALLET_TYPE_CRYPTO = 3;

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private $type;

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
    private $updated;

    /**
     * @MongoDB\Field(type="string")
     */
    private $symbol;

    /**
     * @MongoDB\Field(type="string")
     */
    private $exchange;

    /**
     * @MongoDB\Field(type="float")
     */
    private $inOrder;

    /**
     * @MongoDB\Field(type="float")
     */
    private $price;

    public function __construct(
        string $exchange,
        string $symbol,
        float $amount,
        float $inOrder,
        \DateTimeInterface $updated,
        float $price
    ) {
        $this->id = new ObjectId();
        $this->exchange = $exchange;
        $this->symbol = strtolower($symbol);
        $this->create = new \DateTime();
        $this->updated = $updated;
        $this->amount = $amount;
        $this->inOrder = $inOrder;
        $this->price = $price;
        $this->type = $this->setTypeFromSymbol($symbol);
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

    public function getCreate(): \DateTime
    {
        return $this->create;
    }

    public function setCreate(\DateTime $create): void
    {
        $this->create = $create;
    }

    public function getUpdated(): \DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): void
    {
        $this->updated = $updated;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setTypeFromSymbol(string $symbol): int
    {
        if (CryptoUtils::isFiatCoin(strtolower($symbol))) {
            return self::WALLET_TYPE_FIAT;
        }

        if (CryptoUtils::isStableCoin(strtolower($symbol))) {
            return self::WALLET_TYPE_STABLE;
        }

        return self::WALLET_TYPE_CRYPTO;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isFiatCoin(): bool
    {
        return $this->type === self::WALLET_TYPE_FIAT;
    }

    public function isStableCoin(): bool
    {
        return $this->type === self::WALLET_TYPE_STABLE;
    }

    public function isCrypto(): bool
    {
        return $this->type === self::WALLET_TYPE_CRYPTO;
    }
}
