<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InvestmentService;
use App\Service\WalletService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    private $documentManager;
    private $walletService;
    private $investmentService;

    public function __construct(
        DocumentManager $documentManager,
        WalletService $walletService,
        InvestmentService $investmentService
    ) {
        $this->documentManager = $documentManager;
        $this->walletService = $walletService;
        $this->investmentService = $investmentService;
    }

    /**
     * @Route("/statsglobal", name="crypto_stats_generic_stats")
     */
    public function statsGlobal(): Response
    {
        $investment = $this->investmentService->getInvestment();
        $wallets = $this->walletService->aggregateWallets();
        $globalEUR = 0;
        $globalUSD = 0;
        foreach ($wallets as $key => $wallet) {
            $globalEUR += $wallets[$key]['totalEUR'];
            $globalUSD += $wallets[$key]['totalUSD'];
        }

        return $this->render('/stats/template_global.html.twig', [
            'globalEUR' => $globalEUR,
            'globalUSD' => $globalUSD,
            'numberOfCrypto' => count($wallets),
            'investment' => $investment,
        ]);
    }

    /**
     * @Route("/statsbymarket", name="crypto_stats_generic_stats_by_market")
     */
    public function statsByMarket(): Response
    {
        $wallets = $this->walletService->aggregateWallets();
        $global = 0;
        foreach ($wallets as $key => $wallet) {
            $global += $wallets[$key]['totalUSD'];
        }

        $donutGlobal = [];
        foreach ($wallets as $key => $wallet) {
            if ($global <= 0) {
                $wallets[$key]['percentage'] = 0;
                continue;
            }
            $wallets[$key]['percentage'] = ($wallets[$key]['totalUSD'] * 100) / $global;
            $donutGlobal[] = [
                'y' => $wallets[$key]['percentage'],
                'label' => $wallets[$key]['_id'],
            ];
        }

        return $this->render('/stats/template.html.twig', [
            'wallets' => $wallets,
            'donutGlobal' => $donutGlobal,
        ]);
    }

    /**
     * @Route("/global/{exchange}/", name="crypto_trades_exchanges_global")
     */
    public function statsByExchange(string $exchange): Response
    {
        $wallets = $this->walletService->aggregateWallets($exchange);
        $globalUSD = 0;
        $globalEUR = 0;
        foreach ($wallets as $wallet) {
            $globalUSD += $wallet['totalUSD'];
            $globalEUR += $wallet['totalEUR'];
        }

        return $this->render('/stats/_block_exchange.html.twig', [
            'globalUSD' => $globalUSD,
            'globalEUR' => $globalEUR,
            'exchange' => $exchange,
        ]);
    }
}
