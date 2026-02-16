<?php

namespace App\Controller;

use App\Entity\ProductAttribute;
use App\Form\ProductAttributeType;
use App\Repository\ProductAttributeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product-attributes', name: 'admin_product_attribute_')]
class ProductAttributeController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ProductAttributeRepository $repository): Response
    {
        return $this->render('product_attribute/index.html.twig', [
            'attributes' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $attribute = new ProductAttribute();
        return $this->saveForm($attribute, $request, $entityManager, 'Создание атрибута продукта');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(ProductAttribute $attribute, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->saveForm($attribute, $request, $entityManager, 'Редактирование атрибута продукта');
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(ProductAttribute $attribute, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$attribute->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($attribute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_product_attribute_index');
    }

    private function saveForm(ProductAttribute $attribute, Request $request, EntityManagerInterface $entityManager, string $title): Response
    {
        $form = $this->createForm(ProductAttributeType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($attribute);
            $entityManager->flush();

            return $this->redirectToRoute('admin_product_attribute_index');
        }

        return $this->render('product_attribute/form.html.twig', [
            'form' => $form,
            'title' => $title,
        ]);
    }
}
