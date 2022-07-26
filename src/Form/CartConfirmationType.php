<?php

namespace App\Form;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr'  => [
                    'placeholder' => 'Michel Legrand'
                ]
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse complete',
                'attr'  => [
                    'placeholder' => '11 rue Louis Vaillant'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => "Code postal",
                'attr'  => ['placeholder' => "75000"]
            ])
            ->add("city", TextType::class, [
                "label" => "ville",
                "attr"  => [
                    'placeholder' => 'Paris'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class
        ]);
    }
}
