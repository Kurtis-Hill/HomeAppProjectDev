<?php


namespace App\Sensors\Forms\CustomFormValidatos\SensorDataValidators;

use App\Sensors\Entity\SensorTypes\Dallas;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DallasTemperatureConstraintValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DallasTemperatureConstraint) {
            throw new UnexpectedTypeException($constraint, DallasTemperatureConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_numeric($value)) {
            $this->context->buildViolation($constraint->intMessage)
                ->setParameter('{{ string }}', is_array($value) ? 'array' : $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if (is_numeric($value) && $value > Dallas::HIGH_TEMPERATURE_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->maxMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if (is_numeric($value) && $value < Dallas::LOW_TEMPERATURE_READING_BOUNDARY) {
            $this->context->buildViolation($constraint->minMessage)
                ->setParameter('{{ string }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
