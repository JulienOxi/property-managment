<?php

namespace App\Form;

use App\Entity\Lease;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Repository\PropertyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeaseType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromAt', null, [
                'widget' => 'single_text',
            ])
            ->add('toAt', null, [
                'widget' => 'single_text',
            ])
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'query_builder' => function (PropertyRepository $propertyRepository) {
                    return $propertyRepository->findAccessibleProperties($this->security->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER], false);
                },
                'choice_label' => 'name',
            ])
            ->add('tenants', CollectionType::class, [
                'entry_type' => TenantType::class,
                'allow_add' => true,
                'by_reference' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'data-controller' => 'form_collection',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lease::class,
        ]);
    }
}
