<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//  #[Route('/categories')].  prefix pour le controller entier, on enlèverai donc /categories
final class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    public function index(
        CategoryRepository $repoCategory, 
    )
    {

        $category = new Category();

        return $this->render('category/index.html.twig', [
            'categories' => $repoCategory->findAll(),
            
        ]);
    }

     #[Route('/categories/new', name: 'category_new')]
    public function new(CategoryRepository $repoCategory, EntityManagerInterface $em, Request $request) : Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { //contrainte de validation
            $em->persist($category);
             $em->flush();
            
              return $this->redirectToRoute('category_index');
            
            }

        return $this->render('category/new.html.twig', ['form' => $form]);
    }

    #[Route('/categories/delete', name: 'category_delete')]
    public function delete(EntityManagerInterface $em, Request $request, CategoryRepository $repoCategory): Response{
        
        $id = $request->getPayload()->get('id');  // parce que on récup "name = "id"" du form
        $cat = $repoCategory->find($id); 

        if( $cat == null){
            throw $this->createNotFoundException(); //si pas trouvé de catégorie
        }
        $em->remove($cat);
        $em->flush(); //va persister dans la bdd, donc faire la modif

        return $this->redirectToRoute('category_index');
    
    }

    #[Route('/categories/{id}', name: 'category_show')]
    public function show(CategoryRepository $repoCategory, $id){

    return $this->render('category/show.html.twig', [
            'category' => $repoCategory->find($id),
            // 'category' => $cat;    Category $cat en param de show() = raccourci pour id
        ]);

    }
    
     #[Route('/categories/{id}/edit', name: 'category_edit')]
    public function edit(Category $cat, Request $request, EntityManagerInterface $em): Response{
        $form = $this->createForm(CategoryType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('category_show', ['id'=> $cat->getId()]);
        }

        return $this->render('category/edit.html.twig', ['form' => $form]);
    }

    }
