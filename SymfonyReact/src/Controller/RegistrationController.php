<?php

namespace App\Controller;


use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/HomeApp/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $groupName = new GroupNames();

            $groupName->setGroupName($form->get('groupNameID')->getData());
            $groupName->setTime(new \DateTime());

            try {
                $entityManager->persist($groupName);
                $entityManager->flush();

                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $user->setEmail($form->get('email')->getData());
                $user->setFirstName($form->get('firstName')->getData());
                $user->setLastName($form->get('lastName')->getData());
                $user->setRoles(['ROLE_USER']);
                $user->setGroupNameID($groupName);
                $user->setTime(new \DateTime());

                $groupNameMapping = new GroupnNameMapping();

                $groupNameMapping->setGroupnameid($groupName);
                $groupNameMapping->setUserID($user);

                $entityManager->persist($user);
                $entityManager->persist($groupNameMapping);
                $entityManager->flush();
            } catch (ORMException | \Exception $e) {
                if (isset($groupName)) {
                    $entityManager->remove($groupName);
                }
                if (isset($user)) {
                    $entityManager->remove($user);
                }
                if (isset($groupNameMapping)) {
                    $entityManager->remove($groupNameMapping);
                }
                error_log($e->getMessage());
            }

            return $this->redirectToRoute('spa-view', ['route' => 'login']);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
