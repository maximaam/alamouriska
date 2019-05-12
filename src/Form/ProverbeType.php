<?php

namespace App\Form;

use App\Entity\Proverbe;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class ProverbeType
 * @package App\Form
 */
class ProverbeType extends SharedType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('proverbe', null, [
                'label' => 'Proverbe',
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
            'data_class' => Proverbe::class,
        ]);
    }
}
