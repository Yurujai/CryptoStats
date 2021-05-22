<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use MongoDB\BSON\ObjectId;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\ExchangeRepository")
 */
class Exchange
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
    private $image;

    /**
     * @MongoDB\Field(type="string")
     */
    private $url;

    /**
     * @MongoDB\Field(type="bool")
     */
    private $imported;

    public function __construct(string $alternativeId, string $name, string $image, string $url, bool $imported)
    {
        $this->id = new ObjectId();
        $this->alternativeId = $alternativeId;
        $this->name = $name;
        $this->image = $image;
        $this->url = $url;
        $this->imported = $imported;
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

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function isImported(): bool
    {
        return $this->imported;
    }

    public function setImported(bool $imported): void
    {
        $this->imported = $imported;
    }
}
