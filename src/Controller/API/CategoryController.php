<?php

namespace App\Controller\API;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/categories', name: 'api_categories_')]
class CategoryController extends AbstractController
{
    #[Route('/all', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->json($categories, 200, [], ['groups' => 'category:read']);
    }
}
