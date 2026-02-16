<?php

namespace App\Controller;

use App\Entity\ProductAttributeValue;
use App\Form\ProductAttributeValueType;
use App\Repository\ProductAttributeValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product-attribute-values', name: 'admin_product_attribute_value_')]
class ProductAttributeValueController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ProductAttributeValueRepository $repository): Response
    {
        return $this->render('product_attribute_value/index.html.twig', [
            'values' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $value = new ProductAttributeValue();
        return $this->saveForm($value, $request, $entityManager, 'Создание значения атрибута');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(ProductAttributeValue $value, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->saveForm($value, $request, $entityManager, 'Редактирование значения атрибута');
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(ProductAttributeValue $value, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$value->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($value);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_product_attribute_value_index');
    }

    private function saveForm(ProductAttributeValue $value, Request $request, EntityManagerInterface $entityManager, string $title): Response
    {
        $form = $this->createForm(ProductAttributeValueType::class, $value);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($value);
            $entityManager->flush();

            return $this->redirectToRoute('admin_product_attribute_value_index');
        }

        return $this->render('product_attribute_value/form.html.twig', [
            'form' => $form,
            'title' => $title,
        ]);
    }
}
