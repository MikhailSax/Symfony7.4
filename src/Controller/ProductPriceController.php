<?php

namespace App\Controller;

use App\Entity\ProductPrice;
use App\Form\ProductPriceType;
use App\Repository\ProductPriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product/price')]
final class ProductPriceController extends AbstractController
{
    #[Route(name: 'app_product_price_index', methods: ['GET'])]
    public function index(ProductPriceRepository $productPriceRepository): Response
    {
        return $this->render('product_price/index.html.twig', [
            'product_prices' => $productPriceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_price_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $productPrice = new ProductPrice();
        $form = $this->createForm(ProductPriceType::class, $productPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productPrice);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_price_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_price/new.html.twig', [
            'product_price' => $productPrice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_price_show', methods: ['GET'])]
    public function show(ProductPrice $productPrice): Response
    {
        return $this->render('product_price/show.html.twig', [
            'product_price' => $productPrice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_price_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductPrice $productPrice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductPriceType::class, $productPrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_price_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_price/edit.html.twig', [
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

        return $this->redirectToRoute('app_product_price_index', [], Response::HTTP_SEE_OTHER);
    }
}
