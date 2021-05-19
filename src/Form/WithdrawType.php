<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WithdrawType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'amount',
                TextType::class,
                [
                    'label' => 'amount',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'amount',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'symbol',
                TextType::class,
                [
                    'label' => 'symbol',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'symbol ( EUR, BTC, USD, BNB, ETH, ... )',
                    ],

                    'required' => true,
                ]
            )
            ->add(
                'moved_to',
                TextType::class,
                [
                    'label' => 'Moved to',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'moved to.... ( hardware wallet, bought something, bank account, ... )',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'date',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'label' => 'date',
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                ]
            )
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'crypto_stats_withdraw_form';
    }
}
