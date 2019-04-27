<?php

namespace App\Form;

use App\Entity\Locution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class LocutionType
 * @package App\Form
 */
class LocutionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locution', null, [
                'label' => 'label.locution',
                'help' => 'label.required',
            ])
            ->add('description', null, [
                'help' => 'label.required',
                'attr'  => [
                    'rows'  => 5,
                ]
            ])
            ->add('question', null, [
                'label' => 'Ceci est une question pour tous',
                'help'  => 'Clique si tu veux de l\'aide ici',
            ])
            ->add('imageFile', VichImageType::class, [
                'label'         => 'Photo (optionnel, jpeg uniquement)',
                'required'      => false,
                'allow_delete'  => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Locution::class,
        ]);
    }
}
