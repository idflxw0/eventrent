<?php

namespace App\Form;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class DateRangeValidator
{
    public static function validate(string $startStr, string $endStr, FormInterface $form): bool
    {
        $start = \DateTimeImmutable::createFromFormat('Y-m-d', $startStr)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $startStr . ' 00:00');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d', $endStr)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $endStr . ' 00:00');

        if (!$start || !$end) {
            return true;
        }

        if ($start >= $end) {
            $form->addError(new FormError('La date de début doit être antérieure à la date de fin.'));
            return false;
        }

        if ($start < new \DateTimeImmutable('today')) {
            $form->addError(new FormError('La date de début ne peut pas être dans le passé.'));
            return false;
        }

        return true;
    }
}
