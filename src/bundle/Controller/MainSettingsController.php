<?php

namespace Ethinking\PushConnectorBundle\Controller;

use Ethinking\PushConnector\EzPlatform\Repository\Form\Factory\FormFactory;
use Ethinking\PushConnectorBundle\Entity\MainSettings;
use Ethinking\PushConnectorBundle\Repository\MainSettingsRepository;
use Ethinking\PushConnectorBundle\Service\PushApiService;
use Ethinking\PushConnector\EzPlatform\UI\Permission\PermissionChecker;
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

    const PUSH_SETTINGS_SAVED = 'push_delivery_settings_saved';

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var MainSettingsRepository
     */
    private $mainSettingsRepository;

    /**
     * @var PushApiService
     */
    private $pushApiService;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * MainSettingsController constructor.
     * @param FormFactory $formFactory
     * @param MainSettingsRepository $mainSettingsRepository
     * @param PushApiService $pushApiService
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(FormFactory $formFactory, MainSettingsRepository $mainSettingsRepository, PushApiService $pushApiService, PermissionChecker $permissionChecker)
    {
        $this->formFactory = $formFactory;
        $this->mainSettingsRepository = $mainSettingsRepository;
        $this->pushApiService = $pushApiService;
        $this->permissionChecker = $permissionChecker;
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
            $this->generateUrl('ezplatform.push.main_settings.view')
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateSettings($form->getData(), $mainSettings);
            $translator = $this->get('translator');
            $this->addFlash('success', $translator->trans(self::PUSH_SETTINGS_SAVED, [], 'forms'));
            return new RedirectResponse($this->generateUrl('ezplatform.push.main_settings.view'));
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
}