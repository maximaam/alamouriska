<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Form;

use App\Entity\Mot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MotType
 * @package App\Form
 */
class MotType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inLatin', null, [
                'label' => 'label.latin_char',
                'help' => 'label.required',
            ])
            ->add('inTamazight', null, [
                'label' => 'label.tamazight_char',
                'help'  => 'label.optionnal_wished'
            ])
            ->add('inArabic', null, [
                'label' => 'label.arabic_char',
                'help'  => 'label.optionnal_wished'
            ])
            ->add('description', null, [
                'help'  => 'label.required'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mot::class,
        ]);
    }
}
