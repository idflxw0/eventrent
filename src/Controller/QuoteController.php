<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Invoice;
use App\Entity\User;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Entity\Reservation;
use App\Entity\ReservationLine;
use App\Form\QuoteType;
use App\Repository\EquipmentRepository;
use App\Repository\QuoteRepository;
use App\Repository\ReservationRepository;
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
        /** @var User $user */
        $user = $this->getUser();
        $status = $request->query->get('status');
        $quotes = $repo->findByUser($user->getId(), $status);

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
        ReservationRepository $reservationRepo,
        QuoteRepository $quoteRepo,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $quote = new Quote();
        $quote->setUser($user);

        if ($request->isMethod('GET')) {
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

            // Restore fields after conflict redirect
            if ($request->query->get('start')) {
                try { $quote->setRequestedStartDate(new \DateTimeImmutable($request->query->get('start'))); } catch (\Exception) {}
            }
            if ($request->query->get('end')) {
                try { $quote->setRequestedEndDate(new \DateTimeImmutable($request->query->get('end'))); } catch (\Exception) {}
            }
            if ($request->query->get('city')) {
                $quote->setEventCity($request->query->get('city'));
            }
        }

        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipmentIds = array_filter(array_map(
                fn($line) => $line->getEquipment()?->getId(),
                $quote->getLines()->toArray()
            ));

            $conflicts = $reservationRepo->findConflictingEquipment(
                array_values($equipmentIds),
                $quote->getRequestedStartDate(),
                $quote->getRequestedEndDate()
            );

            $redirectParams = static function () use ($quote, $form): array {
                $params = [
                    'start' => $quote->getRequestedStartDate()?->format('Y-m-d'),
                    'end'   => $quote->getRequestedEndDate()?->format('Y-m-d'),
                    'city'  => $quote->getEventCity(),
                ];
                $equip = $quote->getLines()->first()?->getEquipment();
                if ($equip) { $params['equipment'] = $equip->getId(); }
                $cat = $form->get('category')->getData();
                if ($cat) { $params['category'] = $cat->getId(); }
                return array_filter($params);
            };

            if (!empty($conflicts)) {
                $names = implode(', ', array_map(fn($e) => $e->getName(), $conflicts));
                $this->addFlash('danger', "Équipement(s) déjà réservé(s) sur cette période : {$names}.");
                return $this->redirectToRoute('quote_new', $redirectParams());
            }

            $duplicates = $quoteRepo->findConflictingActiveQuotes(
                array_values($equipmentIds),
                $quote->getRequestedStartDate(),
                $quote->getRequestedEndDate(),
                $user->getId()
            );

            if (!empty($duplicates)) {
                $names = implode(', ', array_map(fn($e) => $e->getName(), $duplicates));
                $this->addFlash('danger', "Vous avez déjà un devis en cours pour cet équipement sur cette période : {$names}.");
                return $this->redirectToRoute('quote_new', $redirectParams());
            }

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

    #[Route('/quotes/{id}/confirm', name: 'quote_confirm', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function confirm(int $id, QuoteRepository $repo, EntityManagerInterface $em, Request $request, \App\Repository\ReservationRepository $reservationRepo): Response
    {
        if (!$this->isCsrfTokenValid('confirm_quote_' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quote = $repo->findOneWithRelations($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if ($quote->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($quote->getStatus() !== 'approved') {
            $this->addFlash('danger', 'Ce devis ne peut pas être confirmé.');
            return $this->redirectToRoute('quote_show', ['id' => $id]);
        }

        $equipmentIds = array_map(
            fn($line) => $line->getEquipment()->getId(),
            $quote->getLines()->toArray()
        );
        $conflicts = $reservationRepo->findConflictingEquipment(
            $equipmentIds,
            $quote->getRequestedStartDate(),
            $quote->getRequestedEndDate()
        );

        if (!empty($conflicts)) {
            $names = implode(', ', array_map(fn($e) => $e->getName(), $conflicts));
            $this->addFlash('danger', "Impossible de confirmer : équipement(s) déjà réservé(s) sur cette période : {$names}. Veuillez faire une nouvelle demande.");
            return $this->redirectToRoute('quote_show', ['id' => $id]);
        }

        $days = max(1, (int) $quote->getRequestedStartDate()->diff($quote->getRequestedEndDate())->days);
        $total = 0.0;

        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setStartDate($quote->getRequestedStartDate());
        $reservation->setEndDate($quote->getRequestedEndDate());
        $reservation->setEventCity($quote->getEventCity() ?? '');
        $reservation->setVenueType('indoor');
        $reservation->setStatus('confirmed');

        foreach ($quote->getLines() as $quoteLine) {
            $line = new ReservationLine();
            $line->setEquipment($quoteLine->getEquipment());
            $line->setQuantity($quoteLine->getQuantity());
            $line->setUnitPricePerDay($quoteLine->getUnitPricePerDay());
            $line->setReservation($reservation);
            $reservation->addLine($line);
            $total += (float) $quoteLine->getUnitPricePerDay() * $quoteLine->getQuantity() * $days;
        }

        $reservation->setTotalAmount(number_format($total, 2, '.', ''));

        $invoice = new Invoice();
        $invoice->setReservation($reservation);
        $invoice->setNumber('INV-' . date('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT));
        $invoice->setAmount(number_format($total, 2, '.', ''));
        $invoice->setDueDate((new \DateTimeImmutable())->modify('+30 days'));

        $quote->setStatus('completed');

        $em->persist($reservation);
        $em->persist($invoice);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a été créée avec succès !');

        return $this->redirectToRoute('reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/quotes/{id}/decline', name: 'quote_decline', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function decline(int $id, QuoteRepository $repo, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('decline_quote_' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quote = $repo->findOneWithRelations($id);

        if (!$quote) {
            throw $this->createNotFoundException('Devis introuvable.');
        }

        if ($quote->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($quote->getStatus() !== 'approved') {
            $this->addFlash('danger', 'Ce devis ne peut pas être décliné.');
            return $this->redirectToRoute('quote_show', ['id' => $id]);
        }

        $quote->setStatus('cancelled');
        $em->flush();

        $this->addFlash('success', 'Vous avez refusé ce devis.');

        return $this->redirectToRoute('quote_index');
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
