<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use MongoDB\BSON\ObjectId;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\MarketRepository")
 */
class Market
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $alternativeId;

    /**
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @MongoDB\Field(type="string")
     */
    private $symbol;

    /**
     * @MongoDB\Field(type="string")
     */
    private $image;

    public function __construct(string $alternativeId, string $name, string $symbol, string $image)
    {
        $this->id = new ObjectId();
        $this->alternativeId = $alternativeId;
        $this->name = $name;
        $this->symbol = $symbol;
        $this->image = $image;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAlternativeId(): string
    {
        return $this->alternativeId;
    }

    public function setAlternativeId(string $alternativeId): void
    {
        $this->alternativeId = $alternativeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }
}
