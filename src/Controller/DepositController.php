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
     * @Route("/deposit/", name="deposit")
     */
    public function show(Request $request): Response
    {
        $deposits = $this->depositService->getAll();

        $depositForm = $this->createForm(DepositType::class, null, []);
        $depositForm->handleRequest($request);
        if ($depositForm->isSubmitted() && $depositForm->isValid()) {
            $depositData = $depositForm->getData();
            $this->depositService->add((float) $depositData['amount'], $depositData['currency'], $depositData['exchange'], $depositData['added']);
            $deposits = $this->depositService->getAll();
        }

        return $this->render('/deposit/template.html.twig', [
            'deposits' => $deposits,
            'depositForm' => $depositForm->createView(),
        ]);
    }

    /**
     * @Route("/deposit/remove/{deposit}", name="deposit_remove_amount")
     */
    public function remove(string $deposit): RedirectResponse
    {
        $this->depositService->remove($deposit);

        return $this->redirectToRoute('transaction_show');
    }

    /**
     * @Route("/deposit/block", name="deposit_block")
     */
    public function block(): Response
    {
        $deposit = $this->depositService->getTotal();

        return $this->render('/resources/_block.html.twig', [
            'headerText' => 'Deposit',
            'value' => $deposit,
            'icon' => 'fas fa-dollar-sign',
        ]);
    }
}
