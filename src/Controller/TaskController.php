<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Form\TaskType;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index')]
    public function index(
        Request $request, 
        TaskRepository $repo, 
        EntityManagerInterface $em): Response //em envoie vers bdd
    {


        $task = new Task;
        $form = $this->createForm(TaskType::class, $task);
        $form-> handleRequest($request);

        if($form->isSubmitted()){
            $task->setIsDone(false);
            $task->setCreatedAt(new \DateTimeImmutable());
            $em->persist($task);
            $em->flush();

           return $this->redirectToRoute('task_index'); //quand on insère des données on redirige pour éviter le renvoi de form plusieurs fois
        }
     

        // $tasks = $repo->findAll(); // recup la liste des tâches
        // dd($tasks);
        // d = dump and die : c'est le var dump 
        return $this->render('task/index.html.twig', [

            'tasks' => $repo->findBy([], ['createdAt' => 'DESC']),
            'form' => $form,
            
        
           
        ]);
    }
}
