<?php

namespace App\Controller;

use App\Entity\Properties;
use App\Form\PropertiesType;
use ContainerKKnOu8R\DumpDataCollectorProxy24c9b63;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/properties', name: 'admin_properties_')]
final class PropertiesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $properties = $entityManager->getRepository(Properties::class)->findAll();

        return $this->render('properties/index.html.twig', [
            'properties' => $properties,
            'title' => 'Характеристики',
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $property = new Properties();
        $form = $this->createForm(PropertiesType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute('admin_properties_index');
        }

        return $this->render('properties/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $property = $entityManager->getRepository(Properties::class)->find($id);
        $form = $this->createForm(PropertiesType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $property = $form->getData();
            $property->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('properties/create.html.twig', [
            'form' => $form,
            'property' => $property
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $property = $entityManager->getRepository(Properties::class)->findOneBy(['id' => $id]);

        $entityManager->remove($property);
        $entityManager->flush();

        return $this->redirectToRoute('index', [
            'message' => 'Свойство успешно созданно'
        ]);

    }


}
