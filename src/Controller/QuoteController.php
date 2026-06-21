<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Form\QuoteType;
use App\Repository\EquipmentRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class QuoteController extends AbstractController
{
    #[Route('/quotes', name: 'quote_index')]
    public function index(Request $request, QuoteRepository $repo): Response
    {
        $status = $request->query->get('status');
        $quotes = $repo->findByUser($this->getUser()->getId(), $status);

        return $this->render('quote/index.html.twig', [
            'quotes' => $quotes,
            'activeStatus' => $status,
        ]);
    }

    #[Route('/quotes/new', name: 'quote_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        EquipmentRepository $equipRepo,
    ): Response {
        $quote = new Quote();
        $quote->setUser($this->getUser());

        $equipId = $request->query->getInt('equipment');
        if ($equipId > 0) {
            $preselected = $equipRepo->find($equipId);
            if ($preselected instanceof Equipment && $preselected->getAvailabilityStatus() === Equipment::STATUS_AVAILABLE) {
                $line = new QuoteLine();
                $line->setEquipment($preselected);
                $line->setQuantity(1);
                $quote->addLine($line);
            }
        }

        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days = max(1, (int) $quote->getRequestedStartDate()->diff($quote->getRequestedEndDate())->days);
            $total = 0.0;

            foreach ($quote->getLines() as $line) {
                $equip = $equipRepo->find($line->getEquipment()->getId());
                if ($equip) {
                    $line->setEquipment($equip);
                    $line->setUnitPricePerDay($equip->getDailyPrice());
                    $line->setQuote($quote);
                    $total += (float) $line->getUnitPricePerDay() * $line->getQuantity() * $days;
                }
            }

            $quote->setEstimatedAmount(number_format($total, 2, '.', ''));
            $em->persist($quote);
            $em->flush();

            $this->addFlash('success', 'Devis demandé avec succès. Un administrateur va l\'examiner.');

            return $this->redirectToRoute('quote_show', ['id' => $quote->getId()]);
        }

        return $this->render('quote/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/quotes/{id}', name: 'quote_show', requirements: ['id' => '\d+'])]
    public function show(int $id, QuoteRepository $repo): Response
    {
        $quote = $repo->findOneWithRelations($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if ($quote->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $days = max(1, (int) $quote->getRequestedStartDate()->diff($quote->getRequestedEndDate())->days);

        return $this->render('quote/show.html.twig', [
            'quote' => $quote,
            'days' => $days,
        ]);
    }
}
