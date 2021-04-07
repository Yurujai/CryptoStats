<?php

declare(strict_types=1);

namespace App\Service;

interface ExchangeInterface
{
    public function createInstance();

    public function getAPIBalance();

    public function getBalance();

    public function saveBalance();
}
