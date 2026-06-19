<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['registeredAt' => 'DESC'])
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
            ->setSearchFields(['firstName', 'lastName', 'email', 'phone']);
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
            ->add(BooleanFilter::new('active', 'Compte actif'))
            ->add(ChoiceFilter::new('roles', 'Rôles')->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Technicien'     => 'ROLE_TECHNICIEN',
                'Utilisateur'    => 'ROLE_USER',
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Informations Personnelles', 'fas fa-user'),
            IdField::new('id')->onlyOnIndex(),
            TextField::new('firstName', 'Prénom'),
            TextField::new('lastName', 'Nom'),
            EmailField::new('email', 'Email'),
            TextField::new('phone', 'Téléphone')->setRequired(false),

            FormField::addFieldset('Accès & Statut', 'fas fa-user-shield'),
            ChoiceField::new('roles', 'Rôles')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Technicien'     => 'ROLE_TECHNICIEN',
                    'Utilisateur'    => 'ROLE_USER',
                ])
                ->allowMultipleChoices()
                ->renderAsBadges([
                    'ROLE_ADMIN'      => 'danger',
                    'ROLE_TECHNICIEN' => 'warning',
                    'ROLE_USER'       => 'success',
                ]),
            BooleanField::new('active', 'Compte actif'),
            DateTimeField::new('registeredAt', 'Inscrit le')->hideOnForm(),
        ];
    }
}
