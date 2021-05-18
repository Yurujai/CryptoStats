<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BinanceExchangeService;
use App\Service\BitvavoExchangeService;
use App\Service\GateIOExchangeService;
use App\Service\KukoinExchangeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WalletUpdateCommand extends Command
{
    protected static $defaultName = 'crypto:wallet:update';
    private $binanceExchangeService;
    private $bitvavoExchangeService;
    private $kukoinExchangeService;
    private $gateIOExchangeService;

    public function __construct(
        BinanceExchangeService $binanceExchangeService,
        BitvavoExchangeService $bitvavoExchangeService,
        KukoinExchangeService $kukoinExchangeService,
        GateIOExchangeService $gateIOExchangeService,
        $name = null
    ) {
        parent::__construct($name);
        $this->binanceExchangeService = $binanceExchangeService;
        $this->bitvavoExchangeService = $bitvavoExchangeService;
        $this->kukoinExchangeService = $kukoinExchangeService;
        $this->gateIOExchangeService = $gateIOExchangeService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Automatically update wallets')
            ->setHelp('Automatically update wallets')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->binanceExchangeService->saveBalance();
        $this->bitvavoExchangeService->saveBalance();
        $this->kukoinExchangeService->saveBalance();
        $this->gateIOExchangeService->saveBalance();

        return 0;
    }

}
