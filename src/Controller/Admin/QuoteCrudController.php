<?php

namespace App\Controller\Admin;

use App\Entity\Quote;
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

class QuoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quote::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityLabelInPlural('Devis')
            ->setEntityLabelInSingular('Devis');
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
                'En attente' => 'pending',
                'Approuvé'   => 'approved',
                'Refusé'     => 'refused',
                'Expiré'     => 'expired',
            ]))
            ->add(DateTimeFilter::new('requestedStartDate', 'Début souhaité'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Détails du Devis', 'fas fa-file-invoice-dollar'),
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user', 'Client'),
            DateField::new('requestedStartDate', 'Début souhaité'),
            DateField::new('requestedEndDate', 'Fin souhaitée'),
            MoneyField::new('estimatedAmount', 'Montant estimé')->setCurrency('EUR'),
            ChoiceField::new('status', 'Statut')->setChoices([
                'En attente' => 'pending',
                'Approuvé'   => 'approved',
                'Refusé'     => 'refused',
                'Expiré'     => 'expired',
            ])->renderAsBadges([
                'pending'  => 'warning',
                'approved' => 'success',
                'refused'  => 'danger',
                'expired'  => 'secondary',
            ]),
            DateField::new('validUntil', 'Valide jusqu\'au')->hideOnForm(),

            FormField::addFieldset('Détails de l\'événement', 'fas fa-map-marker-alt'),
            TextField::new('eventCity', 'Ville de l\'événement')->setRequired(false),

            FormField::addFieldset('Équipements demandés', 'fas fa-boxes'),
            CollectionField::new('lines', 'Équipements')->onlyOnDetail(),
        ];
    }
}
