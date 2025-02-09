<?php

namespace App\Form;

use App\Entity\Property;
use App\Enum\TransactionEnum;
use App\Enum\FinancialCategoryEnum;
use Symfony\Component\Form\FormEvent;
use App\Repository\PropertyRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TransactionFilterType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $accessibleProperties = $options['accessible_properties'];

        $builder
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'choices' => $accessibleProperties, // Limite les propriétés visibles
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => TransactionEnum::cases(),
                'choice_label' => fn(TransactionEnum $type) => $type->value,
                'choice_value' => fn(?TransactionEnum $type) => $type?->name,
                'placeholder' => 'Type de transaction',
                'multiple' => false,
                'expanded' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                $type = $data['type'] ?? null; // Récupère le type sélectionné
    
                $this->updateCategoryField($form, $type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                $type = $data['type'] ?? null; // Récupère le type soumis
    
                $this->updateCategoryField($form, $type);
            });
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
        $form->add('category', ChoiceType::class, [
            'choices' => $categories,
            'choice_label' => fn(FinancialCategoryEnum $type) => $type->value,
            'choice_value' => fn(?FinancialCategoryEnum $type) => $type?->name,
            'placeholder' => 'Sélectionnez une catégorie',
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas d'entité liée
            'accessible_properties' => [], // Propriétés accessibles par l'utilisateur
        ]);
    }
}
