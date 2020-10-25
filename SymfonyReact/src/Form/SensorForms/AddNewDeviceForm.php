<?php


namespace App\Form\SensorForms;


use App\Entity\Core\Devices;
use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Form\CustomFormValidators\NoSpecialCharactersContraint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddNewDeviceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {// @TODO add special character checker
        $builder
            ->add('devicename', TextType::class, [
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
                'class' => Groupname::class,
            ])

            ->add('roomid', EntityType::class, [
                'class' => Room::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Devices::class,
            'csrf_protection' => false,
        ]);
    }
}