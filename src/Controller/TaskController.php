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
        EntityManagerInterface $em): Response //em envoie vers bdd, request car on récupère
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
    #[Route('/tasks/complete', name: 'task_complete')]
    public function complete(Request $request, TaskRepository $repo, EntityManagerInterface $em) : Response{

        $id = $request->getPayload()->get('id');
        $task = $repo->find($id);

        if($task === null){
            throw $this->createNotFoundException();
        }

        $task->setIsDone(!$task->isDone());
        $em->flush();

        return $this->redirectToRoute('task_index');

    }

    #[Route('/tasks/delete', name: 'task_delete')]
    public function delete(EntityManagerInterface $em, Request $request, TaskRepository $repoTask): Response{
        
        $id = $request->getPayload()->get('id');  // parce que on récup "name = "id"" du form
        $task = $repoTask->find($id); 

        if( $task == null){
            throw $this->createNotFoundException(); //si pas trouvé de catégorie
        }
        $em->remove($task);
        $em->flush(); //va persister dans la bdd, donc faire la modif

        return $this->redirectToRoute('task_index');
    
    }
}
