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

    public function balanceProgressBar(): Response
    {
        $balance = $this->statsService->getTotalAmount(true);
        $deposit = $this->statsService->getDepositAmount();

        return $this->render('/block/balance_progress_bar.html.twig', [
            'title' => 'Portfolio balance',
            'balance' => $balance,
            'deposit' => $deposit,
        ]);
    }

    public function balance(): Response
    {
        $profit = $this->statsService->calculateProfit();
        $balance = $this->statsService->getTotalAmount(true);
        $deposit = $this->statsService->getDepositAmount();
        $withdraw = $this->statsService->getWithdrawAmount();

        return $this->render('/block/balance.html.twig', [
            'title' => 'Portfolio balance',
            'balance' => $balance,
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'profit' => $profit,
        ]);
    }

    public function exchanges(): Response
    {
        $data = $this->statsService->getTotalAmountByExchange();
        $labels = array_keys($data);
        $values = array_values($data);
        $total = array_sum($values);

        return $this->render('/block/exchange.html.twig', [
            'title' => 'Exchange balance',
            'data' => $data,
            'labels' => $labels,
            'values' => $values,
            'total' => $total,
        ]);
    }

    public function wallets(): Response
    {
        $wallets = $this->statsService->getListOfCrypto();

        return $this->render('/block/wallets.html.twig', [
            'wallets' => $wallets,
        ]);
    }

    public function fiatAndStable(): Response
    {
        $wallets = $this->statsService->getListOfFiatAndStable();

        return $this->render('/block/fiat_and_stable.html.twig', [
            'wallets' => $wallets,
        ]);
    }
}
