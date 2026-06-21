<?php

namespace App\Controller\Admin;

use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\EquipmentCrudController;
use App\Controller\Admin\InvoiceCrudController;
use App\Controller\Admin\MaintenanceCrudController;
use App\Controller\Admin\QuoteCrudController;
use App\Controller\Admin\ReservationCrudController;
use App\Controller\Admin\ReviewCrudController;
use App\Controller\Admin\UserCrudController;
use App\Repository\EquipmentRepository;
use App\Repository\QuoteRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EquipmentRepository $equipmentRepo,
        private readonly ReservationRepository $reservationRepo,
        private readonly UserRepository $userRepo,
        private readonly QuoteRepository $quoteRepo,
        private readonly \App\Repository\ReviewRepository $reviewRepo,
    ) {
    }

    public function index(): Response
    {
        // Get recent reservations
        $recentReservations = $this->reservationRepo->findBy([], ['id' => 'DESC'], 5);

        // Get recent pending quotes
        $recentQuotes = $this->quoteRepo->findBy(['status' => 'pending'], ['id' => 'DESC'], 5);

        // Get average rating
        $reviews = $this->reviewRepo->findAll();
        $avgRating = 0;
        if (count($reviews) > 0) {
            $totalRating = array_reduce($reviews, fn($carry, $item) => $carry + $item->getRating(), 0);
            $avgRating = round($totalRating / count($reviews), 1);
        }

        $availableEquip = $this->equipmentRepo->count(['availabilityStatus' => \App\Entity\Equipment::STATUS_AVAILABLE]);
        $maintenanceEquip = $this->equipmentRepo->count(['availabilityStatus' => \App\Entity\Equipment::STATUS_MAINTENANCE]);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'equipment'        => $this->equipmentRepo->count([]),
                'reservations'     => $this->reservationRepo->count([]),
                'users'            => $this->userRepo->count([]),
                'pendingQuotes'    => $this->quoteRepo->count(['status' => 'pending']),
                'avgRating'        => $avgRating,
                'availableEquip'   => $availableEquip,
                'maintenanceEquip' => $maintenanceEquip,
            ],
            'recentReservations' => $recentReservations,
            'recentQuotes'       => $recentQuotes,
        ]);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EventRent Admin')
            ->renderContentMaximized()
            ->disableDarkMode(false);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Catalogue');
        yield MenuItem::linkTo(EquipmentCrudController::class, 'Équipements', 'fas fa-tools');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Catégories', 'fas fa-tags');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users');

        yield MenuItem::section('Activité');
        yield MenuItem::linkTo(ReservationCrudController::class, 'Réservations', 'fas fa-calendar-check');
        yield MenuItem::linkToRoute('Devis en attente', 'fas fa-hourglass-start', 'admin_quote_pending');
        yield MenuItem::linkTo(QuoteCrudController::class, 'Tous les Devis', 'fas fa-file-invoice');
        yield MenuItem::linkTo(InvoiceCrudController::class, 'Factures', 'fas fa-receipt');
        yield MenuItem::linkTo(ReviewCrudController::class, 'Avis', 'fas fa-star');
        yield MenuItem::linkTo(MaintenanceCrudController::class, 'Maintenance', 'fas fa-wrench');
    }
}
