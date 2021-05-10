<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\DepositType;
use App\Service\DepositService;
use App\Service\WithdrawService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    private $depositService;
    private $withdrawService;

    public function __construct(
        DepositService $depositService,
        WithdrawService $withdrawService
    )
    {
        $this->depositService = $depositService;
        $this->withdrawService = $withdrawService;
    }

    /**
     * @Route("/transaction/", name="transaction_show")
     */
    public function show(Request $request)
    {
        $deposits = $this->depositService->getAll();
        $withdraws = $this->withdrawService->getAll();

        return $this->render('/transaction/template.html.twig', [
            'deposits' => $deposits,
            'withdraws' => $withdraws
        ]);
    }
}
