<?php


namespace App\Forms\Sensor;

use App\CustomValidators\NoSpecialCharactersNameConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\DallasTemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\DHTTemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\SoilConstraint;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Soil;
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
                            new NoSpecialCharactersNameConstraint(),
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
                            new NoSpecialCharactersNameConstraint(),
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
                            new NoSpecialCharactersNameConstraint(),
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
                        new NoSpecialCharactersNameConstraint(),
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
                            new NoSpecialCharactersNameConstraint(),
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
