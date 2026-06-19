<?php

namespace App\Controller\Admin;

use App\Entity\AudioEquipment;
use App\Entity\Equipment;
use App\Entity\VideoEquipment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['name' => 'ASC'])
            ->setEntityLabelInPlural('Équipements')
            ->setEntityLabelInSingular('Équipement');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category', 'Catégorie'))
            ->add(EntityFilter::new('supplier', 'Fournisseur'))
            ->add(ChoiceFilter::new('availabilityStatus', 'Statut')->setChoices([
                'Disponible' => Equipment::STATUS_AVAILABLE,
                'En maintenance' => Equipment::STATUS_MAINTENANCE,
                'Hors service' => Equipment::STATUS_OUT_OF_SERVICE,
            ]))
            ->add(NumericFilter::new('dailyPrice', 'Prix journalier'));
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            FormField::addFieldset('Informations Générales', 'fas fa-info-circle'),
            IdField::new('id')->onlyOnIndex(),
            AvatarField::new('photo', 'Photo')->onlyOnIndex(),
            TextField::new('photo', 'URL de la photo (Optionnel)')->onlyOnForms()->setRequired(false),
            TextField::new('name', 'Nom'),
            TextField::new('reference', 'Référence'),
            AssociationField::new('category', 'Catégorie'),
            AssociationField::new('supplier', 'Fournisseur'),
            MoneyField::new('dailyPrice', 'Prix / jour')->setCurrency('EUR'),
            ChoiceField::new('availabilityStatus', 'Statut')->setChoices([
                'Disponible'     => Equipment::STATUS_AVAILABLE,
                'Maintenance'    => Equipment::STATUS_MAINTENANCE,
                'Hors service'   => Equipment::STATUS_OUT_OF_SERVICE,
            ])->renderAsBadges([
                Equipment::STATUS_AVAILABLE => 'success',
                Equipment::STATUS_MAINTENANCE => 'warning',
                Equipment::STATUS_OUT_OF_SERVICE => 'danger',
            ]),
            TextareaField::new('description', 'Description')->hideOnIndex()->setRequired(false),
        ];

        $entity = $this->getContext()?->getEntity()?->getInstance();

        if ($entity === null || $entity instanceof AudioEquipment) {
            $fields[] = FormField::addFieldset('Spécifications Audio', 'fas fa-volume-up');
            $fields[] = IntegerField::new('powerWatts', 'Puissance (Watts)')->hideOnIndex();
            $fields[] = TextField::new('connectorType', 'Type de connecteur')->hideOnIndex();
            $fields[] = IntegerField::new('channelCount', 'Nombre de canaux')->hideOnIndex();
        }

        if ($entity === null || $entity instanceof VideoEquipment) {
            $fields[] = FormField::addFieldset('Spécifications Vidéo', 'fas fa-video');
            $fields[] = TextField::new('resolution', 'Résolution')->hideOnIndex();
            $fields[] = IntegerField::new('brightnessLumens', 'Luminosité (Lumens)')->hideOnIndex();
            $fields[] = TextField::new('projectionType', 'Technologie de projection')->hideOnIndex();
        }

        return $fields;
    }
}
