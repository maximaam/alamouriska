<?php

namespace App\Form;

use App\Entity\Citation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class CitationType
 * @package App\Form
 */
class CitationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('citation', null, [
                'label' => 'label.citation',
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
            'data_class' => Citation::class,
        ]);
    }
}
