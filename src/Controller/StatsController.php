<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    private $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * @Route("/stats/global", name="crypto_stats_generic_stats")
     */
    public function statsGlobal(): Response
    {
        return $this->render('/stats/global/template.html.twig');
    }

    /**
     * @Route("/statsbymarket", name="crypto_stats_generic_stats_by_market")
     */
    public function statsByMarket(): Response
    {
        return $this->render('/stats/template.html.twig');
    }

    public function listOfCryptoBlock(): Response
    {
        $wallets = $this->statsService->getListOfCrypto();

        return $this->render('/stats/_list.html.twig', [
            'wallets' => $wallets,
        ]);
    }

    public function donutChart(): Response
    {
        $wallets = $this->statsService->getListOfCrypto();
        $donutGlobal = [];
        $labels = [];
        $values = [];
        foreach ($wallets as $wallet) {
            $donutGlobal[] = [
                'y' => $wallet['percentage'],
                'label' => $wallet['_id'],
            ];
            $labels[] = $wallet['_id'];
            $values[] = $wallet['percentage'];
        }

        return $this->render('/stats/_donut.html.twig', [
            'donutGlobal' => $donutGlobal,
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    /**
     * @Route("/global/{exchange}/", name="crypto_trades_exchanges_global")
     */
    public function statsByExchange(string $exchange): Response
    {
        $totalAmount = $this->statsService->getTotalAmount($exchange);

        return $this->render('/stats/global/_exchange.html.twig', [
            'total' => $totalAmount,
            'exchange' => $exchange,
        ]);
    }

    /**
     * @Route("/profit/percentage", name="crypto_stats_profit_percentage")
     */
    public function profitBlock(): Response
    {
        $profit = $this->statsService->calculateProfit();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Profit',
            'value' => number_format($profit, 2),
            'icon' => 'fas fa-percent',
        ]);
    }

    /**
     * @Route("/total/amount", name="crypto_stats_total_amount")
     */
    public function totalBlock(): Response
    {
        $amount = $this->statsService->getTotalAmount();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Total amount',
            'value' => number_format($amount, 2),
            'icon' => 'fas fa-coins',
        ]);
    }

    /**
     * @Route("/crypto/total", name="crypto_stats_crypto_total")
     */
    public function totalCrypto(): Response
    {
        $numberOfCrypto = $this->statsService->countCrypto();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Total Coins',
            'value' => $numberOfCrypto,
            'icon' => 'fab fa-bitcoin',
        ]);
    }

    /**
     * @Route("/crypto/number", name="crypto_stats_crypto_coin_number")
     */
    public function numberOfCryptoCoinBlock(): Response
    {
        $numberOfCrypto = $this->statsService->getNumberOfCrypto();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Crypto',
            'value' => $numberOfCrypto,
            'icon' => 'fab fa-bitcoin',
        ]);
    }

    /**
     * @Route("/crypto/stable", name="crypto_stats_crypto_stable_number")
     */
    public function numberOfStableCoinBlock(): Response
    {
        $numberOfCrypto = $this->statsService->getNumberOfStable();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Stable coins',
            'value' => $numberOfCrypto,
            'icon' => 'fab fa-bitcoin',
        ]);
    }

    /**
     * @Route("/crypto/fiat", name="crypto_stats_crypto_fiat_number")
     */
    public function numberOfFiatCoinBlock(): Response
    {
        $numberOfCrypto = $this->statsService->getNumberOfFiat();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Fiat',
            'value' => $numberOfCrypto,
            'icon' => 'fab fa-bitcoin',
        ]);
    }
}
