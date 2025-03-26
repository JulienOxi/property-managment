<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BankType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('website')
            ->add('bic', TextType::class, [
                'attr' => [
                    'style' => 'text-transform:uppercase'
                ]
            ])
            ->add('iban', TextType::class, [
                'attr' => [
                    'style' => 'text-transform:uppercase'
                ]
            ])
            ->add('clearingNumber')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bank::class,
        ]);
    }
}
