<?php

namespace Ethinking\PushConnectorBundle\Controller;

use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\PushConnector\EzPlatform\Repository\Form\Factory\FormFactory;
use Ethinking\PushConnectorBundle\Service\PushService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChannelController
 * @package Ethinking\PushConnectorBundle\Controller
 */
class ChannelController extends Controller
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @param FormFactory $formFactory
     * @param PushService $pushService
     */
    public function __construct(FormFactory $formFactory, PushService $pushService)
    {
        $this->formFactory = $formFactory;
        $this->pushApiService = $pushService->getPushApiService();
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('ezplatform.push.channel.delete'))
            ->setMethod('POST')
            ->getForm();

        $channels = $this->pushApiService->getChannels();

        return $this->render('@ezdesign/channels.html.twig', [
            'channels' => $channels,
            'deleteForm' => $deleteForm->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $translator = $this->get('translator');
        $form = $this->formFactory->saveChannel(
            new Channel(),
            $this->generateUrl('ezplatform.push.channel.create')
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasAdded = $this->pushApiService->addChannelAndDefaultTag($form->getData());

            if ($hasAdded) {
                $this->pushApiService->clearDefaultWebPushChannel();
                $this->addFlash('success', $translator->trans('push_delivery_channel_creation_success', [], 'forms'));
                return new RedirectResponse($this->getViewUrl());
            } else {
                $this->addFlash('error', $translator->trans('push_delivery_creation_failed', [], 'forms'));
                return new RedirectResponse($this->generateUrl('ezplatform.push.channel.create'));
            }
        }

        return $this->render('@ezdesign/channel_form.html.twig', [
            'form' => $form->createView(),
            'isNew' => true,
            'title' => $translator->trans('push_delivery_create_channel', [], 'forms')
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateAction(Request $request, $id)
    {
        $translator = $this->get('translator');
        $channel = $this->getChannel($id);
        if (empty($channel)) {
            $this->addFlash('error', $translator->trans('push_delivery_channel_not_found', [], 'forms'));
            return new RedirectResponse($this->getViewUrl());
        }

        $form = $this->formFactory->saveChannel(
            $channel,
            $this->generateUrl('ezplatform.push.channel.edit', ['id' => $id])
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasUpdated = $this->pushApiService->updateChannel($form->getData());

            if ($hasUpdated) {
                $this->clearDefaultChannelById($id);
                $this->addFlash('success', $translator->trans('push_delivery_channel_save_success', [], 'forms'));
                return new RedirectResponse($this->getViewUrl());
            } else {
                $this->addFlash('error', $translator->trans('push_delivery_update_failed', [], 'forms'));
                return new RedirectResponse($this->generateUrl('ezplatform.push.channel.edit', ['id' => $id]));
            }
        }

        return $this->render('@ezdesign/channel_form.html.twig', [
            'form' => $form->createView(),
            'isNew' => false,
            'title' => $translator->trans('push_delivery_edit_channel', [], 'forms')
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $translator = $this->get('translator');
        $ids = $request->get("id");

        if (empty($ids) || !is_array($ids)) {
            return new RedirectResponse($this->getViewUrl());
        }

        foreach ($ids as $id) {
            $this->pushApiService->deleteChannel($id);
            $this->clearDefaultChannelById($id);
        }

        $this->addFlash('success', $translator->trans('push_delivery_channel_remove_success', [], 'forms'));
        return new RedirectResponse($this->getViewUrl());
    }

    /**
     * Clears default channel cache if passed channel id matches
     * @param string $channelId
     */
    public function clearDefaultChannelById($channelId)
    {
        $defaultChannel = $this->pushApiService->getDefaultWebPushChannel();

        if (!empty($defaultChannel) && $defaultChannel->getId() === intval($channelId)) {
            $this->pushApiService->clearDefaultWebPushChannel();
        }
    }

    /**
     * @param $id
     * @return Channel| null
     */
    private function getChannel($id)
    {
        /** @var Channel $channel */
        $channel = $this->pushApiService->getChannel($id);

        return $channel ?? new Channel();
    }

    /**
     * @return string
     */
    private function getViewUrl()
    {
        return $this->generateUrl('ezplatform.push.channel.view');
    }
}
