<?php


namespace App\Sensors\Forms;

use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateReadingForm extends AbstractType implements SensorReadingUpdateInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($builder->getData() instanceof Temperature) {
            if ($options['formSensorType'] instanceof Dht) {
                $builder
                    ->add('currentReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
            if ($options['formSensorType'] instanceof Dallas) {
                $builder
                    ->add('currentReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DallasTemperatureConstraint(),
                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
            if ($options['formSensorType'] instanceof Bmp) {
                $builder
                    ->add('currentReading', TextType::class, [
                        'mapped' => false,
                        'required' => true,
                        'constraints' => [
                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }
        if ($builder->getData() instanceof Humidity) {
            $builder
                ->add('currentReading', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new HumidityConstraint(),
                        new NoSpecialCharactersConstraint(),
                        new NotBlank(),
                    ],
                ]);
        }

        if ($builder->getData() instanceof Analog) {
            if ($options['formSensorType'] instanceof Soil) {
                $builder
                    ->add('currentReading', TextType::class, [
                        'mapped' => false,
                        'required' => true,
                        'constraints' => [
                            new SoilConstraint(),
                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'formSensorType' => false,
        ]);
    }
}
