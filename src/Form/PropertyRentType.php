<?php

namespace App\Form;

use App\Entity\Tenant;
use App\Entity\Property;
use App\Entity\PropertyRent;
use App\Enum\AccessRoleEnum;
use App\Enum\PropertyRentEnum;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PropertyRentType extends AbstractType
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;        
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertyRent::class,
        ]);
    }
}
