<?php

namespace App\Form;

use App\Entity\Locution;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class LocutionType
 * @package App\Form
 */
class LocutionType extends SharedType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locution', null, [
                'label' => 'Locution, groupe de mots',
                'help' => 'label.required',
            ]);

        parent::buildForm($builder, $options);
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
