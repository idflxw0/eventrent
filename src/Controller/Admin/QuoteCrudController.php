<?php

namespace App\Controller\Admin;

use App\Entity\Quote;
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

class QuoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quote::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['createdAt' => 'DESC']);
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
            DateField::new('requestedStartDate', 'Début souhaité'),
            DateField::new('requestedEndDate', 'Fin souhaitée'),
            TextField::new('eventCity', 'Ville')->setRequired(false),
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
            MoneyField::new('estimatedAmount', 'Montant estimé')->setCurrency('EUR'),
            DateField::new('validUntil', 'Valide jusqu\'au')->onlyOnDetail(),
        ];
    }
}
