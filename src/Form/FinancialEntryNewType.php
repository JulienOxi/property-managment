<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\User;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
use App\Enum\TransactionEnum;
use App\Entity\FinancialEntry;
use App\Enum\FinancialCategoryEnum;
use Symfony\Component\Form\FormEvent;
use App\Repository\PropertyRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FinancialEntryNewType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => TransactionEnum::cases(), // Liste des enums
                'choice_label' => fn(TransactionEnum $type) => $type->value, // Affichage du label
                'choice_value' => fn(?TransactionEnum $type) => $type?->name, // Utilisation du nom de l'enum pour la valeur
                'placeholder' => 'Type de transaction', // Optionnel
            ])
            ->add('amount')
            ->add('description')
            ->add('isPaid', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
            ])
            ->add('paidAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['value' => Date('Y-m-d')],
            ])
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'query_builder' => function (PropertyRepository $propertyRepository) {
                    return $propertyRepository->findAccessibleProperties($this->security->getUser(), [AccessRoleEnum::MEMBER, AccessRoleEnum::OWNER], false);
                },
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un bien',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                $type = $data->getType() ?? null; // Récupère le type sélectionné
    
                $this->updateCategoryField($form, $type);
            })
        ;
    }

    /**
     * Met à jour le champ "Catégorie" en fonction du type de transaction sélectionné.
     *
     * @param FormInterface $form Le formulaire.
     * @param string|null   $type Le type de transaction.
     */
    private function updateCategoryField(FormInterface $form, String | TransactionEnum | null $type): void
    {
        if (null === $type) {
            $categories = null;
        }elseif ($type instanceof TransactionEnum) {
            $type = $type->name;//on recupere le nom de l'enum
        }elseif (is_string($type)) {
            $type = $type;
        }
            $categories = match ($type) {
                'INCOME' => FinancialCategoryEnum::getByType('INCOME'),
                'EXPENSE' => FinancialCategoryEnum::getByType('EXPENSE'),
                default => null,//FinancialCategoryEnum::cases(),
            };
            if(null === $categories) {
                $form->add('category', ChoiceType::class, [
                    'choices' => null,
                    'placeholder' => 'Sélectionnez un type de dépense pour commencer',
                ]);
            }else{
                $form->add('category', ChoiceType::class, [
                    'choices' => $categories,
                    'choice_label' => fn(FinancialCategoryEnum $type) => $type->value,
                    'choice_value' => fn(?FinancialCategoryEnum $type) => $type?->name,
                    'placeholder' => 'Sélectionnez une catégorie',
                ]);
            }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FinancialEntry::class,
        ]);
    }
}
