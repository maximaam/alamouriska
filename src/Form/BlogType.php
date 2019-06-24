<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('post', null, [
                'label'  => 'label.title',
                'help'  => 'label.required',
            ])
            ->add('description', null, [
                'label'  => 'label.content',
                'help'  => 'label.required',
                'attr'  => [
                    'rows'  => 10
                    ]
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
