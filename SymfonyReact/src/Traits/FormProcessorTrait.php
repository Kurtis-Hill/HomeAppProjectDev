<?php

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

trait FormProcessorTrait
{
    private array $formInputErrors = [];

    /**
     * @param FormInterface|FormFactoryInterface $form
     * @param EntityManagerInterface $em
     * @param array $formData
     */
    public function processForm(FormInterface|FormFactoryInterface $form, EntityManagerInterface $em, array $formData): void
    {
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
//            dd('valid!!');
            $validFormData = $form->getData();

            try {
                $em->persist($validFormData);
            } catch (\Exception $e) {
                error_log($e->getMessage());
                $this->formInputErrors[] = 'Form persistence failed please try again';
            }
        }
        else {
            $this->processFormErrors($form);
        }

    }

    /**
     * @param FormInterface $form
     */
    public function processFormErrors(FormInterface $form): void
    {
        foreach ($form->getErrors(true, true) as $error) {
//            dd($error->getMessage());
            $this->formInputErrors[] = $error->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getAllFormInputErrors(): array
    {
        return $this->formInputErrors;
    }
}
