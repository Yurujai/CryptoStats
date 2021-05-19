<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WithdrawService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WithdrawController extends AbstractController
{
    private $withdrawService;

    public function __construct(WithdrawService $withdrawService)
    {
        $this->withdrawService = $withdrawService;
    }

    /**
     * @Route("/withdraw/remove/{withdraw}", name="withdraw_remove_amount")
     */
    public function remove(string $withdraw): RedirectResponse
    {
        $this->withdrawService->remove($withdraw);

        return $this->redirectToRoute('transaction_show');
    }
}
