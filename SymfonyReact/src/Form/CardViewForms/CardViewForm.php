<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form\CardViewForms;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

//CSRF tokens are off, there is an end point that kind of defeates the prupose of them, implenting better methods later
class CardViewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardcolourid', EntityType::class, [
                'class' => Cardcolour::class,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('cardiconid', EntityType::class, [
                'class' => Icons::class,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('cardstateid', EntityType::class, [
                'class' => Cardstate::class,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cardview::class,
            'csrf_protection' => false,
        ]);
    }
}
