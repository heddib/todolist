<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="tasks")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();

        $task = new Task();
        // Pour mettre la date d'aujourd'hui sur le form
        $task->setDeadline(new \DateTime('now'));
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setState(false);
            $pdo->persist($task);
            $pdo->flush();
            $this->addFlash("success", "Tâche ajoutée avec succès !");
        }

        $tasks = $pdo->getRepository(Task::class)->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'form_new_task' => $form->createView()
        ]);
    }

    /**
     * @Route("/task/{id}", name="view_task")
     */
    public function task(Request $request, Task $task = null)
    {
        if ($task != null) {
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($task);
                $pdo->flush();
                $this->addFlash("success", "Tâche modifiée avec succès !");
                return $this->redirectToRoute('tasks');
            }

            return $this->render('task/task.html.twig', [
                'task' => $task,
                'form_edit_task' => $form->createView()
            ]);
        } else {
            $this->addFlash("danger", "Tâche introuvable :(");
            return $this->redirectToRoute('tasks');
        }
    }

    /**
     * @Route("/task/delete/{id}", name="delete_task")
     */
    public function delete_task(Request $request, Task $task = null)
    {
        if ($task != null) {
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($task);
            $pdo->flush();
            $this->addFlash("success", "Tâche supprimée avec succès !");
        } else {
            $this->addFlash("danger", "Tâche introuvable :(");
        }
        return $this->redirectToRoute('tasks');
    }

    /**
     * @Route("/task/validate/{id}", name="validate_task")
     */
    public function validate_task(Request $request, Task $task = null)
    {
        if ($task != null) {
            $task->setState(true);
            $pdo = $this->getDoctrine()->getManager();
            $pdo->persist($task);
            $pdo->flush();
            $this->addFlash("success", "Tâche validée avec succès !");
        } else {
            $this->addFlash("danger", "Tâche introuvable :(");
        }
        return $this->redirectToRoute('tasks');
    }
}
