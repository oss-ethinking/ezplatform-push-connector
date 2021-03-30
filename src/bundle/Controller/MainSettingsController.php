<?php

namespace Ethinking\PushConnectorBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\PushConnector\EzPlatform\Repository\Form\Factory\FormFactory;
use Ethinking\PushConnectorBundle\Entity\MainSettings;
use Ethinking\PushConnector\EzPlatform\UI\Permission\PermissionChecker;
use Ethinking\PushConnectorBundle\Service\PushService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Exception;
use \DateTime;

/**
 * Class MainSettingsController
 * @package Ethinking\PushConnectorBundle\Controller
 */
class MainSettingsController extends Controller
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ObjectRepository
     */
    private $mainSettingsRepository;

    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param FormFactory $formFactory
     * @param EntityManagerInterface $em
     * @param PushService $pushService
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(FormFactory $formFactory, EntityManagerInterface $em,
                                PushService $pushService, PermissionChecker $permissionChecker)
    {
        $this->formFactory = $formFactory;
        $this->mainSettingsRepository = $em->getRepository(MainSettings::class);
        $this->pushApiService = $pushService->getPushApiService();
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response|null
     * @throws Exception
     */
    public function clearCacheAction(Request $request)
    {
        $translator = $this->get('translator');
        $hasCleared = $this->pushApiService->clearDefaultWebPushChannel();
        
        if ($hasCleared) {
            $this->addFlash('success', $translator->trans('push_delivery_cache_cleared', [], 'forms'));
        } else {
            $this->addFlash('error', $translator->trans('push_delivery_cache_clear_failed', [], 'forms'));
        }

        return new RedirectResponse($this->getViewUrl());
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response|null
     * @throws Exception
     */
    public function mainAction(Request $request)
    {
        if (!$this->permissionChecker->checkUserAccess('push', 'settings')) {
            return $this->render(
                '@ezdesign/main_settings.html.twig',
                [
                    'access_denied' => true,
                ]
            );
        }
        $mainSettings = $this->getSettings();

        $form = $this->formFactory->saveSettings(
            $mainSettings,
            $this->getViewUrl()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateSettings($form->getData(), $mainSettings);
            $translator = $this->get('translator');
            $this->addFlash('success', $translator->trans('push_delivery_settings_saved', [], 'forms'));
            return new RedirectResponse($this->getViewUrl());
        }

        return $this->render('@ezdesign/main_settings.html.twig', [
                'form' => $form->createView()
            ]
        );
    }

    private function updateSettings(MainSettings $formData, MainSettings $mainSettings)
    {
        if (empty($mainSettings->getCreatedOn())) {
            $mainSettings->setCreatedOn(new DateTime("now"));
        }

        $mainSettings->setDomain($formData->getDomain());
        $mainSettings->setUsername($formData->getUsername());
        $mainSettings->setPassword($formData->getPassword());
        $mainSettings->setClientId($formData->getClientId());
        $mainSettings->setUpdatedOn(new DateTime("now"));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($mainSettings);
        $entityManager->flush();
    }

    /**
     * @return MainSettings|null
     */
    private function getSettings()
    {
        /** @var MainSettings $settings */
        $settings = $this->mainSettingsRepository->findOneBy([]);

        return $settings ?? new MainSettings();
    }

    /**
     * @return string
     */
    private function getViewUrl() {
        return $this->generateUrl('ezplatform.push.main_settings.view');
    }
}