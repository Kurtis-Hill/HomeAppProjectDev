<?php

namespace App\Controller\Core;


use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    #[Route('/HomeApp/WebApp/register', name: 'app_register', methods: [Request::METHOD_POST])]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $groupNameObject = new GroupNames();

                $groupName = $form->get('groupNameID')->getData();

                $groupNameCheck = $entityManager->getRepository(GroupNames::class)->findOneBy(['groupName' => $groupName]);

                if ($groupNameCheck instanceof GroupNames) {
                    throw new BadRequestException($groupName.' has already been taken by another user');
                }

                $groupNameObject->setGroupName($groupName);
                $groupNameObject->setTime(new \DateTime());

                $entityManager->persist($groupNameObject);
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
                $user->setGroupNameID($groupNameObject);
                $user->setTime(new \DateTime());

                $groupNameMapping = new GroupnNameMapping();

                $groupNameMapping->setGroupnameid($groupNameObject);
                $groupNameMapping->setUserID($user);

                $entityManager->persist($user);
                $entityManager->persist($groupNameMapping);
                $entityManager->flush();
            } catch (ORMException | \Exception $e) {
                if (isset($groupNameObject)) {
                    $entityManager->remove($groupNameObject);
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
