<?php

namespace App\Document;

use App\Utils\CryptoUtils;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\WithdrawRepository")
 */
class Withdraw
{
    protected const WITHDRAW_TYPE_FIAT = 1;
    protected const WITHDRAW_TYPE_STABLE = 2;
    protected const WITHDRAW_TYPE_CRYPTO = 3;

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
    private $movedTo;

    /**
     * @MongoDB\Field(type="int")
     */
    private $type;

    public function __construct(
        float $amount,
        string $symbol,
        string $movedTo,
        \DateTimeInterface $added
    ) {
        $this->amount = $amount;
        $this->symbol = strtolower($symbol);
        $this->movedTo = $movedTo;
        $this->added = $added;
        $this->create = new \DateTime();
        $this->type = $this->setTypeFromSymbol($symbol);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getAdded(): \DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): void
    {
        $this->added = $added;
    }

    public function getCreate(): \DateTime
    {
        return $this->create;
    }

    public function setCreate(\DateTime $create): void
    {
        $this->create = $create;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getMovedTo(): string
    {
        return $this->movedTo;
    }

    public function setMovedTo(string $movedTo): void
    {
        $this->movedTo = $movedTo;
    }

    public function setTypeFromSymbol(string $symbol): int
    {
        if (CryptoUtils::isFiatCoin(strtolower($symbol))) {
            return self::WITHDRAW_TYPE_FIAT;
        }

        if (CryptoUtils::isStableCoin(strtolower($symbol))) {
            return self::WITHDRAW_TYPE_STABLE;
        }

        return self::WITHDRAW_TYPE_CRYPTO;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isFiatCoin(): bool
    {
        return self::WITHDRAW_TYPE_FIAT === $this->type;
    }

    public function isStableCoin(): bool
    {
        return self::WITHDRAW_TYPE_STABLE === $this->type;
    }

    public function isCrypto(): bool
    {
        return self::WITHDRAW_TYPE_CRYPTO === $this->type;
    }
}
