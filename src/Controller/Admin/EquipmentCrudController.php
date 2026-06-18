<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['name' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Nom'),
            TextField::new('reference', 'Référence'),
            AssociationField::new('category', 'Catégorie'),
            AssociationField::new('supplier', 'Fournisseur'),
            MoneyField::new('dailyPrice', 'Prix / jour')->setCurrency('EUR'),
            ChoiceField::new('availabilityStatus', 'Statut')->setChoices([
                'Disponible'     => Equipment::STATUS_AVAILABLE,
                'Maintenance'    => Equipment::STATUS_MAINTENANCE,
                'Hors service'   => Equipment::STATUS_OUT_OF_SERVICE,
            ]),
            TextareaField::new('description', 'Description')->onlyOnForms()->setRequired(false),
        ];
    }
}
