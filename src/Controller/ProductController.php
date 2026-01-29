<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Service\Files\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function PHPUnit\Framework\isNull;


#[Route('admin/product', name: 'admin_product_')]
final class ProductController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $title = 'Список продуктов';
        $query = $request->query->all();
        $products = $entityManager->getRepository(Product::class);
        $categories = $entityManager->getRepository(Category::class)->findAll();

        if (isset($query['category'])) {
            $products = $products->findByCategory($query['category']);
        } else {
            $products = $products->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'title' => $title,
            'categories' => $categories,

        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Сохраняем все поля форм в БД
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id,
        UploadFileService $uploadFileService,
    ): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Продукт не найден');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('imagePromo')->getData();

            if ($image) {
                // Сохраняем файл
                $fileName = $uploadFileService->saveFile($image, $product->getId());
                $product->setImagePromo($fileName);
            }

            // Форма уже обновила объект $product автоматически
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Продукт обновлён!');
            return $this->redirectToRoute('admin_product_index');
        }

        $path = null;
        if ($product->getImagePromo()) {
            $path = 'uploads/products/' . $product->getId() . '/' . $product->getImagePromo();
        }

        // Для корректной работы с коллекцией

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'prices' => null,
            'productImagePromo' => $path,
        ]);
    }


    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
