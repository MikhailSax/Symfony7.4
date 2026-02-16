<?php

namespace App\Controller;

use App\Entity\FileAsset;
use App\Form\FileAssetType;
use App\Repository\FileAssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/file-assets', name: 'admin_file_asset_')]
class FileAssetController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(FileAssetRepository $repository): Response
    {
        return $this->render('file_asset/index.html.twig', [
            'files' => $repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $file = new FileAsset();
        return $this->saveForm($file, $request, $entityManager, 'Добавление файла макета');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(FileAsset $file, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->saveForm($file, $request, $entityManager, 'Редактирование файла макета');
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(FileAsset $file, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_file_asset_index');
    }

    private function saveForm(FileAsset $file, Request $request, EntityManagerInterface $entityManager, string $title): Response
    {
        $form = $this->createForm(FileAssetType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('admin_file_asset_index');
        }

        return $this->render('file_asset/form.html.twig', [
            'form' => $form,
            'title' => $title,
        ]);
    }
}
