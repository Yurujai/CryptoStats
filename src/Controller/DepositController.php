<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\DepositType;
use App\Service\DepositService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DepositController extends AbstractController
{
    private $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    /**
     * @Route("/deposit/remove/{deposit}", name="deposit_remove_amount")
     */
    public function remove(string $deposit): RedirectResponse
    {
        $this->depositService->remove($deposit);

        return $this->redirectToRoute('transaction_show');
    }
}
