<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class SharedType
 * @package App\Form
 */
class SharedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null, [
                'help'  => 'label.required',
                'attr'  => [
                    'rows'  => 5
                ]
            ])
            ->add('question', null, [
                'label' => 'Je pose une question',
                'help'  => 'Coche la case si tu veux de l\'aide',
            ])
            ->add('imageFile', VichImageType::class, [
                'label'         => 'Image ou Graphic',
                'required'      => false,
                'allow_delete'  => true,
                'help'  => 'Optionnel. JPEG seulement',
                'attr'  => [
                    'accept' => 'image/jpeg'
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'inherit_data' => true,
        ]);
    }
}
