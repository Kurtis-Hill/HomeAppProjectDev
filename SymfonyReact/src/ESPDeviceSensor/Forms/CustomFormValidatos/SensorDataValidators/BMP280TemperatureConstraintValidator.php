<?php


namespace App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators;


use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BMP280TemperatureConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof BMP280TemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, BMP280TemperatureConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_numeric($value)) {
            $this->context->buildViolation($constraint->intMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value > Bmp::HIGH_TEMPERATURE_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($value < Bmp::LOW_TEMPERATURE_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
