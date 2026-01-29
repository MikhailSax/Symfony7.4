<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class)
            ->add('shortDescription')
            ->add('slug')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'placeholder' => 'Выберите категорию',
            ])
            ->add('isActive', RadioType::class)
            ->add('imagePromo', FileType::class, [
                'label' => 'Загрузите изображение',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4084k',
                        'mimeTypes' => ['image/png','image/jpeg','image/svg+xml'],
                        'mimeTypesMessage' => 'Загрузите изображение в формате PNG, JPG или SVG',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'product_item', // Уникальный идентификатор для этой формы
        ]);
    }
}
