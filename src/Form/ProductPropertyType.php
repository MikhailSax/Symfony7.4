<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductProperty;
use App\Entity\Properties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'label' => 'Продукт',
            ])
            ->add('property', EntityType::class, [
                'class' => Properties::class,
                'choice_label' => 'name',
                'label' => 'Характеристика',
            ])
            ->add('value', null, [
                'label' => 'Значение',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductProperty::class,
        ]);
    }
}
