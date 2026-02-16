<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => static fn(User $user): string => sprintf('%s (%s)', $user->getEmail(), $user->getName() ?? '-'),
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(Order::STATUSES),
            ])
            ->add('totalPrice', MoneyType::class, ['currency' => 'RUB'])
            ->add('materialCost', MoneyType::class, ['currency' => 'RUB'])
            ->add('comment', TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
