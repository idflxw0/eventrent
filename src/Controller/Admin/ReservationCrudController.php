<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['startDate' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user', 'Utilisateur'),
            DateField::new('startDate', 'Début'),
            DateField::new('endDate', 'Fin'),
            TextField::new('eventCity', 'Ville'),
            ChoiceField::new('venueType', 'Lieu')->setChoices(['Intérieur' => 'indoor', 'Extérieur' => 'outdoor']),
            ChoiceField::new('status', 'Statut')->setChoices([
                'En attente'  => 'pending',
                'Confirmée'   => 'confirmed',
                'En cours'    => 'in_progress',
                'Terminée'    => 'completed',
                'Annulée'     => 'cancelled',
            ])->renderAsBadges([
                'pending'     => 'warning',
                'confirmed'   => 'success',
                'in_progress' => 'info',
                'completed'   => 'success',
                'cancelled'   => 'danger',
            ]),
            MoneyField::new('totalAmount', 'Total')->setCurrency('EUR'),
        ];
    }
}
