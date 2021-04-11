<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InvestmentService;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    private $walletService;
    private $investmentService;

    public function __construct(
        WalletService $walletService,
        InvestmentService $investmentService
    ) {
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
            $globalEUR += $wallets[$key]['totalEUR'] + $wallets[$key]['inOrderEUR'];
            $globalUSD += $wallets[$key]['totalUSD'] + $wallets[$key]['inOrderUSD'];
        }

        $profit = 0;
        if ($investment && 'eur' === $investment->getCurrency()) {
            $profit = ($globalEUR - $investment->getAmount()) / $investment->getAmount() * 100;
        } elseif ($investment && 'usd' === $investment->getCurrency()) {
            $profit = ($globalUSD - $investment->getAmount()) / $investment->getAmount() * 100;
        }

        return $this->render('/stats/global/template.html.twig', [
            'globalEUR' => $globalEUR,
            'globalUSD' => $globalUSD,
            'numberOfCrypto' => count($wallets),
            'profit' => $profit,
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

        return $this->render('/stats/global/_exchange.html.twig', [
            'globalUSD' => $globalUSD,
            'globalEUR' => $globalEUR,
            'exchange' => $exchange,
        ]);
    }
}
