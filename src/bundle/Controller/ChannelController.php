<?php

namespace Ethinking\PushConnectorBundle\Controller;

use Ethinking\PushConnector\EzPlatform\Repository\Form\Factory\FormFactory;
use Ethinking\PushConnectorBundle\Entity\Channel;
use Ethinking\PushConnectorBundle\Service\PushApiService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;

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
     * @var PushApiService
     */
    private $pushApiService;

    const TRANSLATOR = 'translator';
    const SUCCESS = 'success';
    const FORMS = 'forms';
    const ERROR = 'error';
    const CHANNEL_VIEW = 'ezplatform.push.channel.view';


    /**
     * @param FormFactory $formFactory
     * @param PushApiService $pushApiService
     */
    public function __construct(FormFactory $formFactory, PushApiService $pushApiService)
    {
        $this->formFactory = $formFactory;
        $this->pushApiService = $pushApiService;
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
        $translator = $this->get(ChannelController::TRANSLATOR);
        $form = $this->formFactory->saveChannel(
            new Channel(),
            $this->generateUrl('ezplatform.push.channel.create')
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasAdded = $this->pushApiService->addChannelAndDefaultTag($form->getData());

            if ($hasAdded) {
                $this->addFlash(ChannelController::SUCCESS, $translator->trans('push_delivery_channel_creation_success', [], ChannelController::FORMS));
                return new RedirectResponse($this->generateUrl(ChannelController::CHANNEL_VIEW));
            } else {
                $this->addFlash(ChannelController::ERROR, $translator->trans('push_delivery_creation_failed', [], ChannelController::FORMS));
                return new RedirectResponse($this->generateUrl('ezplatform.push.channel.create'));
            }
        }

        return $this->render('@ezdesign/channel_form.html.twig', [
            'form' => $form->createView(),
            'isNew' => true,
            'title' => $translator->trans('push_delivery_create_channel', [], ChannelController::FORMS)
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateAction(Request $request, $id)
    {
        $translator = $this->get(ChannelController::TRANSLATOR);
        $channel = $this->getChannel($id);
        if (empty($channel)) {
            $this->addFlash(ChannelController::ERROR, $translator->trans('push_delivery_channel_not_found', [], ChannelController::FORMS));
            return new RedirectResponse($this->generateUrl(ChannelController::CHANNEL_VIEW));
        }

        $form = $this->formFactory->saveChannel(
            $channel,
            $this->generateUrl('ezplatform.push.channel.edit', ['id' => $id])
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasAdded = $this->pushApiService->updateChannel($form->getData());

            if ($hasAdded) {
                $this->addFlash(ChannelController::SUCCESS, $translator->trans('push_delivery_channel_save_success', [], ChannelController::FORMS));
                return new RedirectResponse($this->generateUrl(ChannelController::CHANNEL_VIEW));
            } else {
                $this->addFlash(ChannelController::ERROR, $translator->trans('push_delivery_update_failed', [], ChannelController::FORMS));
                return new RedirectResponse($this->generateUrl('ezplatform.push.channel.edit', ['id' => $id]));
            }
        }

        return $this->render('@ezdesign/channel_form.html.twig', [
            'form' => $form->createView(),
            'isNew' => false,
            'title' => $translator->trans('push_delivery_edit_channel', [], ChannelController::FORMS)
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $translator = $this->get(ChannelController::TRANSLATOR);
        $ids = $request->get("id");

        if (empty($ids) || !is_array($ids)) {
            return new RedirectResponse($this->generateUrl(ChannelController::CHANNEL_VIEW));
        }

        foreach ($ids as $id) {
            $this->pushApiService->deleteChannel($id);
        }

        $this->addFlash(ChannelController::SUCCESS, $translator->trans('push_delivery_channel_remove_success', [], ChannelController::FORMS));
        return new RedirectResponse($this->generateUrl(ChannelController::CHANNEL_VIEW));
    }

    /**
     * @param $appId
     * @return mixed
     */
    public function generateEmbedCode($id)
    {
        $channel = $this->getChannel($id);
        return $this->render('@ezdesign/channel_embed_code.html.twig', [
            'channel' => $channel
        ]);
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
}
