<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function __construct(
        private readonly ReservationRepository $reservationRepo,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('eventCity', TextType::class, [
                'label' => 'Ville',
                'attr' => ['placeholder' => 'Ex : Paris, Lyon, Marseille...'],
            ])
            ->add('venueType', ChoiceType::class, [
                'label' => 'Type de lieu',
                'choices' => ['Intérieur' => 'indoor', 'Extérieur' => 'outdoor'],
                'expanded' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une catégorie',
                'mapped' => false,
                'required' => false,
                'label' => 'Catégorie',
            ])
            ->add('lines', CollectionType::class, [
                'entry_type' => ReservationLineType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $reservation = $event->getData();
            $form = $event->getForm();

            if (!$reservation || $reservation->getId() === null) {
                return;
            }

            $form->get('category')->setData($reservation->getLines()->first()
                ? $reservation->getLines()->first()->getEquipment()->getCategory()
                : null);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();

            $categoryId = $data['category'] ?? null;
            $startStr = $data['startDate'] ?? '';
            $endStr   = $data['endDate'] ?? '';

            if (!DateRangeValidator::validate($startStr, $endStr, $form)) {
                return;
            }

            $start = \DateTimeImmutable::createFromFormat('Y-m-d', $startStr);
            $end   = \DateTimeImmutable::createFromFormat('Y-m-d', $endStr);

            $available = $this->reservationRepo->findAvailableEquipment(
                $start, $end, $categoryId ? (int) $categoryId : null
            );

            if (empty($available)) {
                $form->addError(new FormError('Aucun équipement disponible pour cette période et catégorie.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
