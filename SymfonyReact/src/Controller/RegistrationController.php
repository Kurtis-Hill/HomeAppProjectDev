<?php

namespace App\Controller;

use App\Entity\Core\GroupMapping;
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
            $groupName = new Groupname();

            $groupName->setGroupname($form->get('groupNameID')->getData());
            $groupName->setTimez(new \DateTime());

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($groupName);
                $entityManager->flush();

                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $user->setEmail($form->get('email')->getData());
                $user->setFirstname($form->get('firstName')->getData());
                $user->setLastname($form->get('lastName')->getData());
                $user->setRoles($user->getRoles());
                $user->setGroupnameid($groupName);
                $user->setTimez(new \DateTime());

                $groupNameMapping = new GroupMapping();

                $groupNameMapping->setGroupnameid($groupName);
                $groupNameMapping->setUserID($user);

                $entityManager->persist($user);
                $entityManager->persist($groupNameMapping);
                $entityManager->flush();
            } catch (\PDOException $e) {
                error_log($e->getMessage());
            }  catch (ORMException $e) {
                error_log($e->getMessage());
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
