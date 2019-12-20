<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.02.19
 * Time: 16:29
 */

namespace App\Form;

use App\Entity\Word;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MotType
 * @package App\Form
 */
class WordType extends SharedType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('post', null, [
                'label' => 'label.latin_char',
                'help' => 'label.required_no_spaces',
                'attr'  => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('inTamazight', null, [
                'label' => 'label.tamazight_char',
                'help'  => 'label.optional_wished',
                'attr'  => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('inArabic', null, [
                'label' => 'label.arabic_char',
                'help'  => 'label.optional_wished',
                'attr'  => [
                    'autocomplete' => 'off',
                    'dir'   => 'rtl'
                ]
            ]);

        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Word::class,
        ]);
    }
}
