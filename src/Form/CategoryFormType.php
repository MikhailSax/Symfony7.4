<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('description',TextareaType::class)
            ->add('short_desc')
            ->add('parent', EntityType::class, [ // Поле называется 'parent'
                'class' => Category::class,
                'choice_label' => 'title',
                'placeholder' => 'без параметра',
                'required' => false,
            ])
            ->add('is_active',CheckboxType::class,[
                'attr' => [
                    'class' => 'form-check-input',
                    'id' => 'flexSwitchCheckDefault',
                ]
            ])
            ->add('submit', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
