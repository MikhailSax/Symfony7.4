<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductPrice;
use App\Form\ProductPriceType;
use App\Repository\ProductPriceRepository;
use App\Service\BreadCrumbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product/price',name: 'product_price_')]
final class ProductPriceController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(ProductPriceRepository $productPriceRepository): Response
    {
        return $this->render('product_price/index.html.twig', [
            'product_prices' => $productPriceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $params = $request->request->all();
        $productPrice = new ProductPrice();
        $productPrice->setName($params['name']);
        $productPrice->setQuantity((int)$params['quantity']);
        $productPrice->setPrice($params['price']);
        $product = $entityManager->getRepository(Product::class)->find((int)$params['product_id']);
        $productPrice->setProductId($product);
        $productPrice->setCreatedAt(new \DateTimeImmutable());
        $productPrice->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($productPrice);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Цена создана успешно!'
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ProductPrice $productPrice): Response
    {
        return $this->render('product_price/show.html.twig', [
            'product_price' => $productPrice,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductPrice $productPrice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductPriceType::class, $productPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('product_price_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_price/create.html.twig', [
            'product_price' => $productPrice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_price_delete', methods: ['POST'])]
    public function delete(Request $request, ProductPrice $productPrice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productPrice->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($productPrice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_price_index', [], Response::HTTP_SEE_OTHER);
    }
}
