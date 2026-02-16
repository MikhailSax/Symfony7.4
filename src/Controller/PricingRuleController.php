<?php

namespace App\Controller;

use App\Entity\PricingRule;
use App\Form\PricingRuleType;
use App\Repository\PricingRuleRepository;
use App\Service\PriceCalculationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/pricing-rules', name: 'admin_pricing_rule_')]
class PricingRuleController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PricingRuleRepository $pricingRuleRepository): Response
    {
        return $this->render('pricing_rule/index.html.twig', [
            'rules' => $pricingRuleRepository->findBy([], ['priority' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rule = new PricingRule();
        return $this->saveForm($rule, $request, $entityManager, 'Создание правила цены');
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(PricingRule $rule, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->saveForm($rule, $request, $entityManager, 'Редактирование правила цены');
    }

    #[Route('/{id}/test', name: 'test', methods: ['POST'])]
    public function test(PricingRule $rule, PriceCalculationService $priceCalculationService): Response
    {
        $price = $priceCalculationService->calculate($rule->getFormula() ?? '0', [
            'base_price' => 100,
            'width' => 2,
            'height' => 3,
            'lamination' => 150,
        ]);

        $this->addFlash('success', sprintf('Тестовый расчет правила "%s": %s ₽', $rule->getName(), $price));

        return $this->redirectToRoute('admin_pricing_rule_index');
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(PricingRule $rule, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rule->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($rule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_pricing_rule_index');
    }

    private function saveForm(PricingRule $rule, Request $request, EntityManagerInterface $entityManager, string $title): Response
    {
        $form = $this->createForm(PricingRuleType::class, $rule);
        $form->get('attributeConditionsJson')->setData(json_encode($rule->getAttributeConditions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rawConditions = $form->get('attributeConditionsJson')->getData();
            $decoded = json_decode((string) $rawConditions, true);
            $rule->setAttributeConditions(is_array($decoded) ? $decoded : []);

            $entityManager->persist($rule);
            $entityManager->flush();

            return $this->redirectToRoute('admin_pricing_rule_index');
        }

        return $this->render('pricing_rule/form.html.twig', [
            'form' => $form,
            'title' => $title,
        ]);
    }
}
