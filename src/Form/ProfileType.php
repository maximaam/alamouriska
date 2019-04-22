<?php
/**
 * Created by PhpStorm.
 * User: mimosa
 * Date: 05.05.17
 * Time: 17:02
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as ProfileFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class ProfileType
 * @package App\Form
 */
class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('allowMemberContact', null, [
                'label' => 'Les membres peuvent me contacter directement'
            ])
            ->add('allowPostNotification', null, [
                'label' => 'Envoyez-moi un email à chaque nouvelle publication'
            ])
            ->add('avatarFile', VichImageType::class, [
                'label'         => 'Photo (jpeg uniquement)',
                'required'      => false,
                'allow_delete'  => true,
                'download_link' => false,
                'download_uri'  => '',
                //'download_label' => '...',
                ])

            ->remove('current_password')
        ;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return ProfileFormType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

}