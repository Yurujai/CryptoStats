<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WithdrawService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/withdraw/block", name="withdraw_block")
     */
    public function block(): Response
    {
        $withdraw = $this->withdrawService->getTotal();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Withdraw',
            'value' => $withdraw,
            'icon' => 'fas fa-dollar-sign',
        ]);
    }
}
