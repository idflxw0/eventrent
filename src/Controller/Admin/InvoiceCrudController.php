<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class InvoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invoice::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['issuedAt' => 'DESC'])
            ->setEntityLabelInPlural('Factures')
            ->setEntityLabelInSingular('Facture');
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
            ->add(ChoiceFilter::new('paymentStatus', 'Statut de paiement')->setChoices([
                'En attente' => 'pending',
                'Payée'      => 'paid',
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Détails de la Facture', 'fas fa-receipt'),
            IdField::new('id')->onlyOnIndex(),
            TextField::new('number', 'Numéro de facture'),
            MoneyField::new('amount', 'Montant')->setCurrency('EUR'),
            ChoiceField::new('paymentStatus', 'Statut de paiement')->setChoices([
                'En attente' => 'pending',
                'Payée'      => 'paid',
            ])->renderAsBadges([
                'pending' => 'warning',
                'paid'    => 'success',
            ]),
            DateTimeField::new('issuedAt', 'Émise le')->hideOnForm(),
            DateTimeField::new('dueDate', 'Date d\'échéance')->onlyOnDetail(),

            FormField::addFieldset('Réservation associée', 'fas fa-calendar-check'),
            AssociationField::new('reservation', 'Réservation'),
        ];
    }
}
