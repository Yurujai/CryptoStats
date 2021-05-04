<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InvestmentType extends AbstractType
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
                        'placeholder' => 'amount'
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'currency',
                ChoiceType::class,
                [
                    'label' => 'currency',
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                    'choices' => [
                        'EUR' => 'eur',
                        'USD' => 'usd',
                    ],
                ]
            )
            ->add(
                'exchange',
                ChoiceType::class, [
                    'label' => 'exchange',
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                    'choices' => [
                        'Binance' => 'binance',
                        'Bitvavo' => 'bitvavo',
                        'Coinbase' => 'coinbase',
                        'GateIO' => 'gateio',
                        'Kukoin' => 'kukoin',
                    ],
                ]
            )
            ->add(
                'added',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'label' => 'added on',
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                ]
            )
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'crypto_stats_investment_form';
    }
}
