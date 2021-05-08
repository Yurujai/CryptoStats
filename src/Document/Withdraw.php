<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use MongoDB\BSON\ObjectId;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\WithdrawRepository")
 */
class Withdraw
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
     * @MongoDB\Field(type="string")
     */
    private $symbol;

    /**
     * @MongoDB\Field(type="string")
     */
    private $movedTo;

    /**
     * @MongoDB\Field(type="string")
     */
    private $comment;

    public function __construct(float $amount, string $symbol, string $movedTo, ?string $comment, \DateTimeInterface $create)
    {
        $this->amount = $amount;
        $this->symbol = $symbol;
        $this->movedTo = $movedTo;
        $this->create = $create;
        $this->comment = $comment;
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

    public function getCreate(): \DateTimeInterface
    {
        return $this->create;
    }

    public function setCreate(\DateTimeInterface $create): void
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

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

}
