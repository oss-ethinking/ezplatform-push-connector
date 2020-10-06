<?php

namespace EzPlatform\PushConnectorBundle\Controller;

use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use EzPlatform\PushConnector\EzPlatform\Repository\Form\Factory\FormFactory;
use EzPlatform\PushConnectorBundle\Service\PushService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChannelController
 * @package EzPlatform\PushConnectorBundle\Controller
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
                $this->addFlash('success', $translator->trans('push_delivery_channel_creation_success', [], 'forms'));
                return new RedirectResponse($this->generateUrl('ezplatform.push.channel.view'));
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
            return new RedirectResponse($this->generateUrl('ezplatform.push.channel.view'));
        }

        $form = $this->formFactory->saveChannel(
            $channel,
            $this->generateUrl('ezplatform.push.channel.edit', ['id' => $id])
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasAdded = $this->pushApiService->updateChannel($form->getData());

            if ($hasAdded) {
                $this->addFlash('success', $translator->trans('push_delivery_channel_save_success', [], 'forms'));
                return new RedirectResponse($this->generateUrl('ezplatform.push.channel.view'));
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
            return new RedirectResponse($this->generateUrl('ezplatform.push.channel.view'));
        }

        foreach ($ids as $id) {
            $this->pushApiService->deleteChannel($id);
        }

        $this->addFlash('success', $translator->trans('push_delivery_channel_remove_success', [], 'forms'));
        return new RedirectResponse($this->generateUrl('ezplatform.push.channel.view'));
    }

    /**
     * @param $channelId
     * @return mixed
     */
    public function generateEmbedCode($channelId)
    {
        $channel = $this->getChannel($channelId);
        return $this->render('@ezdesign/channel_embed_code.html.twig', [
            'channel' => $channel
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function downloadJsLibrary()
    {
        $fileUrl = 'https://testcopyfile.s3.eu-central-1.amazonaws.com/test_file.zip';
        return new RedirectResponse($fileUrl);
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