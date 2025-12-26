<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_')]
final class CategoryController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
       $title = 'Список Категорий';
       $category = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'title' => $title,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/create.html.twig', [
            'form' => $form,
        ]);
    }

}
