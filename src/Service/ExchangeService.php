<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ODM\MongoDB\DocumentManager;

class ExchangeService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }
}
