<?php

namespace App\Form;

use App\Entity\PricingRule;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PricingRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
            ])
            ->add('name')
            ->add('formula', TextareaType::class, [
                'help' => 'Пример: base_price * width * height + lamination',
            ])
            ->add('attributeConditionsJson', TextareaType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'JSON-условия применения правила',
                'empty_data' => '{}',
            ])
            ->add('priority', IntegerType::class)
            ->add('active', CheckboxType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PricingRule::class,
        ]);
    }
}
