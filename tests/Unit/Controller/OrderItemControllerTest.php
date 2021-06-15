<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Controller\OrderItemController;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface;
use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Controller\ResourceDeleteHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderItemControllerTest extends MockeryTestCase
{
    public function testCanInstantiate(): void
    {
        self::expectNotToPerformAssertions();

        new OrderItemController(
            Mockery::mock(MetadataInterface::class),
            Mockery::mock(RequestConfigurationFactoryInterface::class),
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            Mockery::mock(NewResourceFactoryInterface::class),
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            Mockery::mock(ResourceFormFactoryInterface::class),
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            Mockery::mock(EventDispatcherInterface::class),
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );
    }

    public function testThrowsWhenFormTypeNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('getFormType')
            ->once()
            ->andReturnNull()
        ;

        $requestConfigurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $requestConfigurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $newResource = Mockery::mock(OrderItemInterface::class);

        $newResourceFactory = Mockery::mock(NewResourceFactoryInterface::class);
        $newResourceFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($newResource)
        ;

        $controller = Mockery::mock(OrderItemController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller
            ->shouldReceive('getCurrentCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;
        $controller
            ->shouldReceive('getQuantityModifier->modify')
            ->with($newResource, 1)
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $requestConfigurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $newResourceFactory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            Mockery::mock(ResourceFormFactoryInterface::class),
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            Mockery::mock(EventDispatcherInterface::class),
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $controller->addAction(Mockery::mock(Request::class));
    }

    public function testAddsFormErrorFlasher(): void
    {
        $parameters = Mockery::mock(Parameters::class);
        $parameters
            ->shouldReceive('get')
            ->andReturnNull()
        ;
        $parameters->shouldReceive('set');

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('getFormType')
            ->once()
            ->andReturn('FORM_TYPE')
        ;
        $configuration
            ->shouldReceive('getFormOptions')
            ->once()
            ->andReturn([])
        ;
        $configuration
            ->shouldReceive('getParameters')
            ->andReturn($parameters)
        ;

        $requestConfigurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $requestConfigurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $newResponse = Mockery::mock(Response::class);

        $redirectHandler = Mockery::mock(RedirectHandlerInterface::class);
        $redirectHandler
            ->shouldReceive('redirectToReferer')
            ->with($configuration)
            ->once()
            ->andReturn($newResponse)
        ;

        $newResource = Mockery::mock(OrderItemInterface::class);
        $newResource
            ->shouldReceive('getProduct')
            ->andReturnNull()
        ;

        $newResourceFactory = Mockery::mock(NewResourceFactoryInterface::class);
        $newResourceFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($newResource)
        ;

        $command = Mockery::mock(AddToCartCommandInterface::class);

        $error = Mockery::mock(FormError::class);
        $error
            ->shouldReceive('getMessage')
            ->once()
            ->andReturn('big bad error message')
        ;

        $formErrorIterator = Mockery::mock(FormErrorIterator::class);
        $formErrorIterator->shouldReceive('rewind');
        $formErrorIterator
            ->shouldReceive('valid')
            ->twice()
            ->andReturn(true, false)
        ;
        $formErrorIterator
            ->shouldReceive('current')
            ->once()
            ->andReturn($error)
        ;
        $formErrorIterator->shouldReceive('next');

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('handleRequest->isValid')
            ->once()
            ->andReturn(false)
        ;
        $form
            ->shouldReceive('getErrors')
            ->once()
            ->andReturn($formErrorIterator)
        ;

        $controller = Mockery::mock(OrderItemController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller
            ->shouldReceive('getCurrentCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;
        $controller
            ->shouldReceive('getQuantityModifier->modify')
            ->with($newResource, 1)
            ->once()
        ;
        $controller
            ->shouldReceive('createAddToCartCommand')
            ->once()
            ->andReturn($command)
        ;
        $controller
            ->shouldReceive('getFormFactory->create')
            ->with('FORM_TYPE', $command, [])
            ->once()
            ->andReturn($form)
        ;
        $controller
            ->shouldReceive('addFlash')
            ->once()
            ->with('error', 'big bad error message')
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $requestConfigurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $newResourceFactory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            Mockery::mock(ResourceFormFactoryInterface::class),
            $redirectHandler,
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            Mockery::mock(EventDispatcherInterface::class),
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        self::assertSame($newResponse, $controller->addAction($request));
    }

    public function testRedirectsToRefererWhenEventIsStopped(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('getFormType')
            ->once()
            ->andReturn('FORM_TYPE')
        ;
        $configuration
            ->shouldReceive('getFormOptions')
            ->once()
            ->andReturn([])
        ;

        $requestConfigurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $requestConfigurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $newResponse = Mockery::mock(Response::class);

        $redirectHandler = Mockery::mock(RedirectHandlerInterface::class);
        $redirectHandler
            ->shouldReceive('redirectToReferer')
            ->with($configuration)
            ->once()
            ->andReturn($newResponse)
        ;

        $newResource = Mockery::mock(OrderItemInterface::class);

        $newResourceFactory = Mockery::mock(NewResourceFactoryInterface::class);
        $newResourceFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($newResource)
        ;

        $command = Mockery::mock(AddToCartCommandInterface::class);

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('handleRequest->isValid')
            ->once()
            ->andReturn(true)
        ;
        $form
            ->shouldReceive('getData')
            ->once()
            ->andReturn($command)
        ;

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->once()
            ->andReturn(true)
        ;

        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;

        $flashHelper = Mockery::mock(FlashHelperInterface::class);
        $flashHelper
            ->shouldReceive('addFlashFromEvent')
            ->once()
        ;

        $controller = Mockery::mock(OrderItemController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller
            ->shouldReceive('getCurrentCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;
        $controller
            ->shouldReceive('getQuantityModifier->modify')
            ->with($newResource, 1)
            ->once()
        ;
        $controller
            ->shouldReceive('createAddToCartCommand')
            ->once()
            ->andReturn($command)
        ;
        $controller
            ->shouldReceive('getFormFactory->create')
            ->with('FORM_TYPE', $command, [])
            ->once()
            ->andReturn($form)
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $requestConfigurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $newResourceFactory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            Mockery::mock(ResourceFormFactoryInterface::class),
            $redirectHandler,
            $flashHelper,
            Mockery::mock(AuthorizationCheckerInterface::class),
            $eventDispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        self::assertSame($newResponse, $controller->addAction($request));
    }

    public function testReturnsEventResponse(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('getFormType')
            ->once()
            ->andReturn('FORM_TYPE')
        ;
        $configuration
            ->shouldReceive('getFormOptions')
            ->once()
            ->andReturn([])
        ;

        $requestConfigurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $requestConfigurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $newResponse = Mockery::mock(Response::class);

        $newResource = Mockery::mock(OrderItemInterface::class);

        $newResourceFactory = Mockery::mock(NewResourceFactoryInterface::class);
        $newResourceFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($newResource)
        ;

        $command = Mockery::mock(AddToCartCommandInterface::class);
        $command
            ->shouldReceive('getCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $command
            ->shouldReceive('getCartItem')
            ->once()
            ->andReturn(Mockery::mock(OrderItemInterface::class))
        ;

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('handleRequest->isValid')
            ->once()
            ->andReturn(true)
        ;
        $form
            ->shouldReceive('getData')
            ->once()
            ->andReturn($command)
        ;

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->once()
            ->andReturn(false)
        ;

        $postEvent = Mockery::mock(ResourceControllerEvent::class);
        $postEvent
            ->shouldReceive('hasResponse')
            ->once()
            ->andReturn(true)
        ;
        $postEvent
            ->shouldReceive('getResponse')
            ->once()
            ->andReturn($newResponse)
        ;

        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;
        $eventDispatcher
            ->shouldReceive('dispatchPostEvent')
            ->once()
            ->andReturn($postEvent)
        ;

        $manager = Mockery::mock(EntityManagerInterface::class);
        $manager
            ->shouldReceive('persist')
            ->once()
        ;
        $manager
            ->shouldReceive('flush')
            ->once()
        ;

        $controller = Mockery::mock(OrderItemController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller
            ->shouldReceive('getCurrentCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;
        $controller
            ->shouldReceive('getQuantityModifier->modify')
            ->with($newResource, 1)
            ->once()
        ;
        $controller
            ->shouldReceive('createAddToCartCommand')
            ->once()
            ->andReturn($command)
        ;
        $controller
            ->shouldReceive('getFormFactory->create')
            ->with('FORM_TYPE', $command, [])
            ->once()
            ->andReturn($form)
        ;
        $controller
            ->shouldReceive('getOrderModifier->addToOrder')
            ->once()
        ;
        $controller
            ->shouldReceive('getCartManager')
            ->once()
            ->andReturn($manager)
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $requestConfigurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $newResourceFactory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            Mockery::mock(ResourceFormFactoryInterface::class),
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            $eventDispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        self::assertSame($newResponse, $controller->addAction($request));
    }

    public function testRedirectsToRefererWithSuccessFlash(): void
    {
        $parameters = Mockery::mock(Parameters::class);
        $parameters
            ->shouldReceive('get')
            ->andReturnNull()
        ;
        $parameters->shouldReceive('set');

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('getFormType')
            ->once()
            ->andReturn('FORM_TYPE')
        ;
        $configuration
            ->shouldReceive('getFormOptions')
            ->once()
            ->andReturn([])
        ;
        $configuration
            ->shouldReceive('getParameters')
            ->andReturn($parameters)
        ;

        $requestConfigurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $requestConfigurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $newResponse = Mockery::mock(Response::class);

        $newResource = Mockery::mock(OrderItemInterface::class);
        $newResource
            ->shouldReceive('getProduct')
            ->andReturnNull()
        ;

        $newResourceFactory = Mockery::mock(NewResourceFactoryInterface::class);
        $newResourceFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($newResource)
        ;

        $command = Mockery::mock(AddToCartCommandInterface::class);
        $command
            ->shouldReceive('getCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $command
            ->shouldReceive('getCartItem')
            ->once()
            ->andReturn(Mockery::mock(OrderItemInterface::class))
        ;

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('handleRequest->isValid')
            ->once()
            ->andReturn(true)
        ;
        $form
            ->shouldReceive('getData')
            ->once()
            ->andReturn($command)
        ;

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->once()
            ->andReturn(false)
        ;

        $postEvent = Mockery::mock(ResourceControllerEvent::class);
        $postEvent
            ->shouldReceive('hasResponse')
            ->once()
            ->andReturn(false)
        ;

        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;
        $eventDispatcher
            ->shouldReceive('dispatchPostEvent')
            ->once()
            ->andReturn($postEvent)
        ;

        $manager = Mockery::mock(EntityManagerInterface::class);
        $manager
            ->shouldReceive('persist')
            ->once()
        ;
        $manager
            ->shouldReceive('flush')
            ->once()
        ;

        $flashHelper = Mockery::mock(FlashHelperInterface::class);
        $flashHelper
            ->shouldReceive('addSuccessFlash')
            ->once()
        ;

        $redirectHandler = Mockery::mock(RedirectHandlerInterface::class);
        $redirectHandler
            ->shouldReceive('redirectToReferer')
            ->with($configuration)
            ->once()
            ->andReturn($newResponse)
        ;

        $controller = Mockery::mock(OrderItemController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller
            ->shouldReceive('getCurrentCart')
            ->once()
            ->andReturn(Mockery::mock(OrderInterface::class))
        ;
        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;
        $controller
            ->shouldReceive('getQuantityModifier->modify')
            ->with($newResource, 1)
            ->once()
        ;
        $controller
            ->shouldReceive('createAddToCartCommand')
            ->once()
            ->andReturn($command)
        ;
        $controller
            ->shouldReceive('getFormFactory->create')
            ->with('FORM_TYPE', $command, [])
            ->once()
            ->andReturn($form)
        ;
        $controller
            ->shouldReceive('getOrderModifier->addToOrder')
            ->once()
        ;
        $controller
            ->shouldReceive('getCartManager')
            ->once()
            ->andReturn($manager)
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $requestConfigurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $newResourceFactory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            Mockery::mock(ResourceFormFactoryInterface::class),
            $redirectHandler,
            $flashHelper,
            Mockery::mock(AuthorizationCheckerInterface::class),
            $eventDispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        self::assertSame($newResponse, $controller->addAction($request));
    }
}
