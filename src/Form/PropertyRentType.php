<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tenant;
use App\Entity\Property;
use App\Entity\PropertyRent;
use App\Enum\AccessRoleEnum;
use App\Enum\PropertyRentEnum;
use App\Repository\PropertyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PropertyRentType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
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
            ->add('fromAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['value' => Date('Y-m-d')],
            ])
            ->add('endedAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'query_builder' => function (PropertyRepository $propertyRepository) {
                    return $propertyRepository->findAccessibleProperties($this->security->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER], false);
                },
                'choice_label' => 'name',
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
