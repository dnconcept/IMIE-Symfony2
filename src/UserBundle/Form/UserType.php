<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, ['attr' => ['placeholder' => "Nom d'utilisateur"]])
            ->add('password', 'password', ['attr' => ['placeholder' => "Mot de passe"]])
            ->add('salt', null)
            ->add('roles', ChoiceType::class, array(
                'multiple'          => true,
                'choices'           => array(
                    'ROLE_USER'              => 'ROLE_USER',
                    'ROLE_ADMIN'             => 'ROLE_ADMIN',
                    'ROLE_ALLOWED_TO_SWITCH' => 'ROLE_ALLOWED_TO_SWITCH',
                ),
                'choices_as_values' => true,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }
}
