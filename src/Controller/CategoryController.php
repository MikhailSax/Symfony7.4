<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'title' => $title,
            'categories' => $categories,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $fileSystem = new Filesystem();

//        $publicDir = dirname(__DIR__) . '\public\category';
//
//        $publicDir .= $category->getSlug() . DIRECTORY_SEPARATOR;
//
//        dd($publicDir);

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

    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Редактирование категории ';
        $category = $entityManager->getRepository(Category::class)->findOneBy(['slug' => $slug]);
        $form = $this->createForm(CategoryFormType::class, $category);
        $title .= $category->getTitle();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/edit.html.twig', [
            'form' => $form,
            'title' => $title,
            'category' => $category,
        ]);
    }

    #[Route('/delete/{slug}', name: 'delete', methods: ['POST'])]
    public function delete(string $slug, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Category::class)->findOneBy(['slug' => $slug]);

        if ($category->getChildren()->count() > 0) {
            return $this->json(
                [
                    'success' => false,
                    'errorMessage' => 'У данный категории есть связи или она является родительской категорией'
                ]
            );
        } else {
            $entityManager->remove($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index', [
                'message' => 'Категория'
            ]);
        }

    }

}
