<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['startDate' => 'DESC'])
            ->setEntityLabelInPlural('Réservations')
            ->setEntityLabelInSingular('Réservation');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user', 'Client'))
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices([
                'En attente'  => 'pending',
                'Confirmée'   => 'confirmed',
                'En cours'    => 'in_progress',
                'Terminée'    => 'completed',
                'Annulée'     => 'cancelled',
            ]))
            ->add(DateTimeFilter::new('startDate', 'Date de début'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Détails de la Réservation', 'fas fa-calendar-alt'),
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user', 'Client'),
            DateField::new('startDate', 'Date de début'),
            DateField::new('endDate', 'Date de fin'),
            MoneyField::new('totalAmount', 'Montant total')->setCurrency('EUR'),
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
                'completed'   => 'secondary',
                'cancelled'   => 'danger',
            ]),

            FormField::addFieldset('Lieu de l\'événement', 'fas fa-map-marker-alt'),
            TextField::new('eventCity', 'Ville'),
            ChoiceField::new('venueType', 'Type de lieu')->setChoices(['Intérieur' => 'indoor', 'Extérieur' => 'outdoor']),
            TextField::new('weatherForecast', 'Prévisions météo')->onlyOnDetail(),

            FormField::addFieldset('Équipements Réservés & Facturation', 'fas fa-receipt'),
            CollectionField::new('lines', 'Équipements')->onlyOnDetail(),
            AssociationField::new('invoice', 'Facture associée')->onlyOnDetail(),
        ];
    }
}
