<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;  //ajout du request
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // ajout de l'encoder
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/sign_in", name="sign_in")
     */
    public function sign_in(Request $request, EntityManagerInterface  $entityManager,UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        
        $form->handleRequest($request); //analyse la request
        if($form->isSubmitted() && $form->isValid()) //si le form est envoyé:
        {
            
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
                        
            $em = $entityManager;
            $em->persist($user);
            $em->flush();
            //$manager->persist($user); //persiste l’info dans le temps
            //$manager->flush(); //envoie les info à la BDD
            
            return $this->redirectToRoute('login');
        }
        return $this->render('security/registration.html.twig', [ 'form' => $form->createView() ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }
    
    /**
     * @Route("/logout", name="logout")
     * @IsGranted("ROLE_ADMIN")
     */
    public function logout()
    {
    }
}
