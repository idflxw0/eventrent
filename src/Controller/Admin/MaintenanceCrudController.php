<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Entity\Maintenance;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class MaintenanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Maintenance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['interventionDate' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('equipment', 'Équipement'),
            AssociationField::new('technician', 'Technicien'),
            ChoiceField::new('interventionType', 'Type')->setChoices([
                'Réparation'  => 'repair',
                'Inspection'  => 'inspection',
                'Panne'       => 'breakdown',
            ]),
            TextareaField::new('description', 'Description'),
            DateTimeField::new('interventionDate', 'Date d\'intervention'),
            ChoiceField::new('statusAfterIntervention', 'Statut après')->setChoices([
                'Disponible'    => Equipment::STATUS_AVAILABLE,
                'Maintenance'   => Equipment::STATUS_MAINTENANCE,
                'Hors service'  => Equipment::STATUS_OUT_OF_SERVICE,
            ]),
        ];
    }
}
