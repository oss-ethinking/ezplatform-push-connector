<?php

namespace EzPlatform\PushConnector\EzPlatform\Repository\Form\Type;

use EzPlatform\PushConnectorBundle\Entity\MainSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainSettingsFormType extends AbstractType
{
    const REQUIRED = 'required';
    const LABEL_FORMAT = 'label_format';
    const LABEL_ATTR = 'label_attr';
    const CLASS_ATTRIBUTE = 'class';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'domain',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Your domain with Push.Delivery',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                    'help' => 'Eg: https://ez.push.delivery'
                ]
            )
            ->add(
                'clientId',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Client ID',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Username',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'password',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Password',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Save',
                    'attr' => [
                        self::CLASS_ATTRIBUTE => 'save btn-primary',
                    ],
                ]
            )
            ->add(
                'test',
                ButtonType::class,
                [
                    'label' => 'Test Connection',
                    'attr' => [
                        self::CLASS_ATTRIBUTE => 'btn-secondary',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MainSettings::class,
        ));
    }
}
