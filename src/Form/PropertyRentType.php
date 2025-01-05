<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tenant;
use App\Entity\Property;
use App\Entity\PropertyRent;
use App\Enum\PropertyRentEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PropertyRentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('type', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn(PropertyRentEnum $type) => $type->value, PropertyRentEnum::cases()), // Labels (ex: "Appartement", "Maison")
                    PropertyRentEnum::cases() // Values (ex: "Appartement", "Maison")
                )
            ])
            ->add('monthlyPrice')
            ->add('fromAt', null, [
                'widget' => 'single_text',
            ])
            ->add('endedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'choice_label' => 'name',
            ])
            ->add('tenant', EntityType::class, [
                'class' => Tenant::class,
                'choice_label' => 'fullName',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertyRent::class,
        ]);
    }
}
