<?php

namespace Ethinking\PushConnector\EzPlatform\Repository\Form\Type;

use Ethinking\PushConnectorBundle\Entity\Channel;
use Ethinking\PushConnectorBundle\Service\PushApiService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChannelFormType extends AbstractType
{
    const LABEL = 'label';
    const REQUIRED = 'required';
    const LABEL_FORMAT = 'label_format';
    const LABEL_ATTR = 'label_attr';
    const CLASS_ATTRIBUTE = 'class';


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                HiddenType::class,
                [
                    self::REQUIRED => false
                ]
            )
            ->add(
                'platformId',
                ChoiceType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Platform',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                    'choices' => [
                        '- Select -' => '0',
                        'Web Push' => PushApiService::WEB_PUSH,
                        'Firebase' => PushApiService::FIREBASE_ANDROID
                    ]
                ]
            )
            ->add(
                'appName',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Name',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'pushTemplate',
                TextareaType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Push Template',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                    'attr' => [
                        'rows' => '8',
                    ],
                ]
            )
            ->add(
                'senderId',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Firebase Server Key',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'firebaseMessagingSenderId',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Firebase Messaging Sender ID',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'firebaseProjectId',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Firebase Project ID',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'firebaseApiKey',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Firebase API key',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'firebaseAppId',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Firebase App ID',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'fallbackUrl',
                TextType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL_FORMAT => 'Fallback URL',
                    self::LABEL_ATTR => [
                        self::CLASS_ATTRIBUTE => 'mb-3',
                    ],
                ]
            )
            ->add(
                'apiUrl',
                HiddenType::class,
                [
                    self::REQUIRED => false
                ]
            )
            ->add(
                'serviceWorkerPath',
                HiddenType::class,
                [
                    self::REQUIRED => false
                ]
            )
            ->add(
                'accessToken',
                HiddenType::class,
                [
                    self::REQUIRED => false
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    self::LABEL => 'Save',
                    'attr' => [
                        self::CLASS_ATTRIBUTE => 'save btn-primary',
                    ],
                ]
            )
            ->add(
                'generate',
                ButtonType::class,
                [
                    self::LABEL => 'Generate Code',
                    'attr' => [
                        self::CLASS_ATTRIBUTE => 'btn-secondary',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Channel::class,
        ));
    }
}
