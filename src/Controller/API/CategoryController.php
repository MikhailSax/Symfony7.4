<?php

namespace App\Controller\API;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/category')]
class CategoryController extends AbstractController
{
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
    }

}
