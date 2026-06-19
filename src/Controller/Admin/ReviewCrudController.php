<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class ReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityLabelInPlural('Avis')
            ->setEntityLabelInSingular('Avis');
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
            ->add(EntityFilter::new('equipment', 'Équipement'))
            ->add(EntityFilter::new('user', 'Utilisateur'))
            ->add(ChoiceFilter::new('rating', 'Note')->setChoices([
                '1 Étoile'  => 1,
                '2 Étoiles' => 2,
                '3 Étoiles' => 3,
                '4 Étoiles' => 4,
                '5 Étoiles' => 5,
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user', 'Utilisateur'),
            AssociationField::new('equipment', 'Équipement'),
            ChoiceField::new('rating', 'Note')->setChoices([
                '⭐ (1/5)'     => 1,
                '⭐⭐ (2/5)'    => 2,
                '⭐⭐⭐ (3/5)'   => 3,
                '⭐⭐⭐⭐ (4/5)'  => 4,
                '⭐⭐⭐⭐⭐ (5/5)' => 5,
            ])->renderAsBadges([
                1 => 'danger',
                2 => 'warning',
                3 => 'info',
                4 => 'primary',
                5 => 'success',
            ]),
            TextareaField::new('comment', 'Commentaire')->setRequired(false),
            DateTimeField::new('createdAt', 'Date')->hideOnForm(),
        ];
    }
}
