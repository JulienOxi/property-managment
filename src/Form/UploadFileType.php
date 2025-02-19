<?php

namespace App\Form;

use App\Entity\UploadFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $data = $options['extra_data'] ?? [];

        $builder
            ->add('description')
            ->add('file', FileType::class, [
                'required' => true,
            ])
            ->add('type', HiddenType::class,[
                'data' => $data['type'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UploadFile::class,
            'extra_data' => null, // Option personnalisée pour les données supplémentaires
        ]);

        $resolver->setAllowedTypes('extra_data', ['array', 'null']);
    }
}
