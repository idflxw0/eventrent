<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\ReservationLine;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipment', EntityType::class, [
                'class' => Equipment::class,
                'choice_label' => 'name',
                'label' => 'Équipement',
                'placeholder' => 'Choisir un équipement',
                'attr' => ['class' => 'er-form-control form-select'],
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('e')
                        ->where('e.availabilityStatus = :status')
                        ->setParameter('status', Equipment::STATUS_AVAILABLE)
                        ->orderBy('e.name', 'ASC');
                },
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Qté',
                'attr' => ['min' => 1, 'max' => 99, 'class' => 'er-qty-value'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationLine::class,
        ]);
    }
}
