<?php

namespace EzPlatform\PushConnector\EzPlatform\Repository\Form\Factory;

use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use EzPlatform\PushConnector\EzPlatform\Repository\Form\Type\ChannelFormType;
use EzPlatform\PushConnector\EzPlatform\Repository\Form\Type\MainSettingsFormType;
use EzPlatform\PushConnectorBundle\Entity\MainSettings;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package EzPlatform\PushConnector\EzPlatform\Repository\Form\Factory
 */
class FormFactory
{
    /**
     * @var FormFactoryInterface $formFactory
     */
    protected $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param MainSettings $data
     * @param string $action
     * @return FormInterface
     */
    public function saveSettings(MainSettings $data, string $action): FormInterface
    {
        $name = StringUtil::fqcnToBlockPrefix(MainSettingsFormType::class);
        return $this->formFactory->createNamed($name, MainSettingsFormType::class, $data, [
            'action' => $action,
            'method' => Request::METHOD_POST,
        ]);
    }

    /**
     * @param Channel $data
     * @param string $action
     * @return FormInterface
     */
    public function saveChannel(Channel $data, string $action): FormInterface
    {
        $name = StringUtil::fqcnToBlockPrefix(ChannelFormType::class);
        return $this->formFactory->createNamed($name, ChannelFormType::class, $data, [
            'action' => $action,
            'method' => $data->isNew() ? Request::METHOD_POST : Request::METHOD_PUT,
        ]);
    }
}
