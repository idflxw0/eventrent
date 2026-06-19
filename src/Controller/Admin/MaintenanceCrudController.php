<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Entity\Maintenance;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class MaintenanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Maintenance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['interventionDate' => 'DESC'])
            ->setEntityLabelInPlural('Maintenances')
            ->setEntityLabelInSingular('Maintenance');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('equipment', 'Équipement'))
            ->add(EntityFilter::new('technician', 'Technicien'))
            ->add(ChoiceFilter::new('interventionType', 'Type d\'intervention')->setChoices([
                'Réparation'  => 'repair',
                'Inspection'  => 'inspection',
                'Panne'       => 'breakdown',
            ]))
            ->add(ChoiceFilter::new('statusAfterIntervention', 'Statut final')->setChoices([
                'Disponible'    => Equipment::STATUS_AVAILABLE,
                'Maintenance'   => Equipment::STATUS_MAINTENANCE,
                'Hors service'  => Equipment::STATUS_OUT_OF_SERVICE,
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Détails de l\'Intervention', 'fas fa-tools'),
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('equipment', 'Équipement'),
            AssociationField::new('technician', 'Technicien'),
            ChoiceField::new('interventionType', 'Type d\'intervention')->setChoices([
                'Réparation'  => 'repair',
                'Inspection'  => 'inspection',
                'Panne'       => 'breakdown',
            ])->renderAsBadges([
                'repair'     => 'primary',
                'inspection' => 'info',
                'breakdown'  => 'danger',
            ]),
            DateTimeField::new('interventionDate', 'Date d\'intervention'),

            FormField::addFieldset('Compte Rendu & Statut Final', 'fas fa-check-double'),
            TextareaField::new('description', 'Description / Rapport'),
            ChoiceField::new('statusAfterIntervention', 'Statut de l\'équipement après intervention')->setChoices([
                'Disponible'    => Equipment::STATUS_AVAILABLE,
                'Maintenance'   => Equipment::STATUS_MAINTENANCE,
                'Hors service'  => Equipment::STATUS_OUT_OF_SERVICE,
            ])->renderAsBadges([
                Equipment::STATUS_AVAILABLE => 'success',
                Equipment::STATUS_MAINTENANCE => 'warning',
                Equipment::STATUS_OUT_OF_SERVICE => 'danger',
            ]),
        ];
    }
}
