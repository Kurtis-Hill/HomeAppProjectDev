<?php


namespace App\Form\SensorForms;


use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CustomFormValidators\NoSpecialCharactersConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\DallasTemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\HumidityConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\DHTTemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\SoilContraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateReadingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                        'mapped' => false,
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
                        'required' => true,
                        'constraints' => [
                            new SoilContraint(),
                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'formSensorType' => false,
        ]);
    }
}
