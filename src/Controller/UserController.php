<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de la date d'inscription
            $user->setCreatedAt(new \DateTime('now'));
            $pdo->persist($user);
            $pdo->flush();
            $this->addFlash("success", "Utilisateur ajouté avec succès !");
        }

        $users = $pdo->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'form_new_user' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{id}", name="view_user")
     */
    public function user(Request $request, User $user = null)
    {
        if ($user != null) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($user);
                $pdo->flush();
                $this->addFlash("success", "Utilisateur modifié avec succès !");
            }

            return $this->render('user/user.html.twig', [
                'user' => $user,
                'form_edit_user' => $form->createView()
            ]);
        } else {
            $this->addFlash("danger", "Utilisateur introuvable :(");
            return $this->redirectToRoute('home');
        }
    }
}
