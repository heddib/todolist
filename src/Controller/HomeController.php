<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setCreatedAt(new \DateTime('now'));
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pdo->persist($user);
            $pdo->flush();
        }

        $users = $pdo->getRepository(User::class)->findAll();

        return $this->render('home/index.html.twig', [
            'users' => $users,
            'form_new_user' => $form->createView()
        ]);
    }
}
