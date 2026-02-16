<?php

namespace App\Controller\API;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/products', name: 'api_products_')]
class ProductController extends AbstractController
{
    #[Route('/all', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }
}
