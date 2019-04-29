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
use Vich\UploaderBundle\Form\Type\VichImageType;

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
                'help'  => 'label.optional_wished'
            ])
            ->add('inArabic', null, [
                'label' => 'label.arabic_char',
                'help'  => 'label.optional_wished'
            ])
            ->add('description', null, [
                'help'  => 'label.required',
                'attr'  => [
                    'rows'  => 5
                ]
            ])
            ->add('question', null, [
                'label' => 'Ceci est une question pour tous',
                'help'  => 'Clique si tu veux de l\'aide',
            ])
            ->add('imageFile', VichImageType::class, [
                'label'         => 'Photo (optionnel, jpeg uniquement)',
                'required'      => false,
                'allow_delete'  => true,
                //'help'  => 'label.optional',
                //'download_link' => false,
                //'download_uri'  => '',
                //'download_label' => '...',
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
