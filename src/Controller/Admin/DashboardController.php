<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Equipment;
use App\Entity\Invoice;
use App\Entity\Maintenance;
use App\Entity\Quote;
use App\Entity\Reservation;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\EquipmentRepository;
use App\Repository\QuoteRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EquipmentRepository $equipmentRepo,
        private readonly ReservationRepository $reservationRepo,
        private readonly UserRepository $userRepo,
        private readonly QuoteRepository $quoteRepo,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'equipment'    => $this->equipmentRepo->count([]),
                'reservations' => $this->reservationRepo->count([]),
                'users'        => $this->userRepo->count([]),
                'pendingQuotes' => $this->quoteRepo->count(['status' => 'pending']),
            ],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('EventRent Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Catalogue');
        yield MenuItem::linkToCrud('Équipements', 'fas fa-tools', Equipment::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-tags', Category::class);

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        yield MenuItem::section('Activité');
        yield MenuItem::linkToCrud('Réservations', 'fas fa-calendar-check', Reservation::class);
        yield MenuItem::linkToCrud('Devis', 'fas fa-file-invoice', Quote::class);
        yield MenuItem::linkToCrud('Factures', 'fas fa-receipt', Invoice::class);
        yield MenuItem::linkToCrud('Avis', 'fas fa-star', Review::class);
        yield MenuItem::linkToCrud('Maintenance', 'fas fa-wrench', Maintenance::class);
    }
}
