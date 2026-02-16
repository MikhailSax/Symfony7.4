<?php

namespace App\Form;

use App\Entity\FileAsset;
use App\Entity\Order;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileAssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order', EntityType::class, [
                'class' => Order::class,
                'choice_label' => fn(Order $order) => sprintf('Заказ #%d', $order->getId()),
                'required' => false,
            ])
            ->add('uploadedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'required' => false,
            ])
            ->add('originalName')
            ->add('path')
            ->add('size')
            ->add('checkStatus', ChoiceType::class, [
                'choices' => [
                    'Ожидает' => FileAsset::CHECK_PENDING,
                    'Успешно' => FileAsset::CHECK_OK,
                    'Ошибка' => FileAsset::CHECK_ERROR,
                ],
            ])
            ->add('checkMessage', null, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FileAsset::class]);
    }
}
