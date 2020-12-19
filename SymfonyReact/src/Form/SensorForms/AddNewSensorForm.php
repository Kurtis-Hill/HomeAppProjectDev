<?php


namespace App\Form\SensorForms;


use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensors;
use App\Entity\Core\Sensortype;
use App\Form\CustomFormValidators\NoSpecialCharactersContraint;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddNewSensorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sensorname', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NoSpecialCharactersContraint(),
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 20,
                        'minMessage' => 'Device name too short',
                        'maxMessage' => 'Device name too long'])
                ]
            ])

            ->add('groupnameid', EntityType::class, [
                'class' => Groupname::class
            ])

            ->add('roomid', EntityType::class, [
                'class' => Room::class
            ])

            ->add('sensortype', EntityType::class, [
                'class' => Sensortype::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sensors::class,
            'csrf_protection' => false,
        ]);
    }
}
