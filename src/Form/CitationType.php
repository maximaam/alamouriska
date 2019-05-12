<?php

namespace App\Form;

use App\Entity\Citation;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class CitationType
 * @package App\Form
 */
class CitationType extends SharedType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('citation', null, [
                'label' => 'Citation',
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
            'data_class' => Citation::class,
        ]);
    }
}
