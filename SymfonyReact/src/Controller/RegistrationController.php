<?php

namespace App\Controller;

use App\Entity\Core\Groupname;
use App\Entity\Core\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/HomeApp/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            dd($form->get('groupNameID')->getData());
            $groupName = new Groupname();

            $groupName->setGroupname($form->get('groupNameID')->getData());
            $groupName->setTimez(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($groupName);
            $entityManager->flush();
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
//
      //      dd($groupName->getGroupnameid());
            $user->setEmail($form->get('email')->getData());
            $user->setFirstname($form->get('firstName')->getData());
            $user->setLastname($form->get('lastName')->getData());
            $user->setRoles($user->getRoles());
            $user->setGroupnameid($groupName);
            $user->setTimez(new \DateTime());


            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('index-view.html.twig');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
