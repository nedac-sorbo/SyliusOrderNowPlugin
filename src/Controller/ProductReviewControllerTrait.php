<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface;
use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @property MetadataInterface $metadata
 * @property RequestConfigurationFactoryInterface $requestConfigurationFactory
 * @property NewResourceFactoryInterface $newResourceFactory
 * @property FactoryInterface $factory
 * @property ResourceFormFactoryInterface $resourceFormFactory
 * @property EventDispatcherInterface $eventDispatcher
 * @property FlashHelperInterface $flashHelper
 * @property RedirectHandlerInterface $redirectHandler
 * @property StateMachineInterface $stateMachine
 * @property RepositoryInterface $repository
 * @method void isGrantedOr403(RequestConfiguration $configuration, string $permission)
 * @method FormInterface createForm(string $type, $data = null, array $options = [])
 * @method Response render(string $view, array $parameters = [], Response $response = null)
 */
trait ProductReviewControllerTrait
{
    public function createAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);

        /** @var ReviewInterface $newResource */
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->resourceFormFactory->create($configuration, $newResource);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newResource = $form->getData();

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                $eventResponse = $event->getResponse();
                if (null !== $eventResponse) {
                    return $eventResponse;
                }

                return $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }

            $this->repository->add($newResource);

            if ($configuration->isHtmlRequest()) {
                $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(
                ResourceActions::CREATE,
                $configuration,
                $newResource
            );

            if (!$configuration->isHtmlRequest()) {
                return $this->createRestView($configuration, $newResource, Response::HTTP_CREATED);
            }

            $postEventResponse = $postEvent->getResponse();
            if (null !== $postEventResponse) {
                return $postEventResponse;
            }

            return $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->createRestView($configuration, $form, Response::HTTP_BAD_REQUEST);
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(
            ResourceActions::CREATE,
            $configuration,
            $newResource
        );
        $initializeEventResponse = $initializeEvent->getResponse();
        if (null !== $initializeEventResponse) {
            return $initializeEventResponse;
        }

        /** @var ProductInterface $product */
        $product = $newResource->getReviewSubject();
        $cardForm = $this
            ->createForm(AddToCartType::class, null, ['product' => $product])
            ->createView()
        ;

        return $this->render($configuration->getTemplate(ResourceActions::CREATE . '.html'), [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'resource' => $newResource,
            $this->metadata->getName() => $newResource,
            'form' => $form->createView(),
            'cardForm' => $cardForm
        ]);
    }
}
