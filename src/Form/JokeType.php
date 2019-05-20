<?php

namespace App\Form;

use App\Entity\Joke;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class JokeType
 * @package App\Form
 */
class JokeType extends SharedType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('post', null, [
                'label' => 'label.joke',
                'help' => 'label.required',
                'attr'  => [
                    'rows'  => 5
                ]
            ]);

        parent::buildForm($builder, $options);

        $builder
            ->remove('description')
            ->remove('question');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Joke::class,
        ]);
    }
}
