<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InvoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invoice::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['issuedAt' => 'DESC']);
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
            TextField::new('number', 'Numéro'),
            AssociationField::new('reservation', 'Réservation'),
            MoneyField::new('amount', 'Montant')->setCurrency('EUR'),
            ChoiceField::new('paymentStatus', 'Paiement')->setChoices([
                'En attente' => 'pending',
                'Payée'      => 'paid',
            ])->renderAsBadges(['pending' => 'warning', 'paid' => 'success']),
            DateTimeField::new('issuedAt', 'Émise le')->onlyOnIndex(),
            DateTimeField::new('dueDate', 'Échéance')->onlyOnDetail(),
        ];
    }
}
