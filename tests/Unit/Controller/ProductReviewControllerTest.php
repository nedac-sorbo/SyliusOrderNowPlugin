<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Controller\ProductReviewController;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface;
use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactoryInterface;
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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ProductReviewControllerTest extends MockeryTestCase
{
    public function testCanInstantiate(): void
    {
        self::expectNotToPerformAssertions();

        new ProductReviewController(
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

    public function testThrowsWhenTheEventIsStoppedAndTheRequestIsNoHtmlRequest(): void
    {
        $this->expectException(HttpException::class);

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->once()
            ->andReturn(false)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
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
            ->andReturn($review)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->once()
            ->andReturn(true)
        ;
        $event
            ->shouldReceive('getErrorCode')
            ->once()
            ->andReturn(123)
        ;
        $event
            ->shouldReceive('getMessage')
            ->once()
            ->andReturn('ERRRRROOOORORRRR')
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $controller->createAction($request);
    }

    public function testReturnsEventResponse(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->once()
            ->andReturn(true)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
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
            ->andReturn($review)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        $response = Mockery::mock(Response::class);

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->twice()
            ->andReturn(true)
        ;
        $event
            ->shouldReceive('getResponse')
            ->once()
            ->andReturn($response)
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;

        $flashHelper = Mockery::mock(FlashHelperInterface::class);
        $flashHelper
            ->shouldReceive('addFlashFromEvent')
            ->once()
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            $flashHelper,
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    public function testReturnsRedirectToIndexResponse(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->once()
            ->andReturn(true)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
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
            ->andReturn($review)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        $response = Mockery::mock(Response::class);

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->twice()
            ->andReturn(true)
        ;
        $event
            ->shouldReceive('getResponse')
            ->once()
            ->andReturnNull()
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;

        $flashHelper = Mockery::mock(FlashHelperInterface::class);
        $flashHelper
            ->shouldReceive('addFlashFromEvent')
            ->once()
        ;

        $redirectHandler = Mockery::mock(RedirectHandlerInterface::class);
        $redirectHandler
            ->shouldReceive('redirectToIndex')
            ->once()
            ->andReturn($response)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            $redirectHandler,
            $flashHelper,
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    public function testReturnsCreatedResponse(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->twice()
            ->andReturn(false)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
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
            ->andReturn($review)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        $response = Mockery::mock(Response::class);

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->twice()
            ->andReturn(false)
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;
        $dispatcher
            ->shouldReceive('dispatchPostEvent')
            ->once()
            ->andReturn($event)
        ;

        $repository = Mockery::mock(RepositoryInterface::class);
        $repository
            ->shouldReceive('add')
            ->with($review)
            ->once()
        ;

        $viewHandler = Mockery::mock(ViewHandlerInterface::class);
        $viewHandler
            ->shouldReceive('handle')
            ->once()
            ->andReturn($response)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            $viewHandler,
            $repository,
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    public function testReturnsPostEventResponse(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->twice()
            ->andReturn(true)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
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
            ->andReturn($review)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        $response = Mockery::mock(Response::class);

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->twice()
            ->andReturn(false)
        ;

        $postEvent = Mockery::mock(ResourceControllerEvent::class);
        $postEvent
            ->shouldReceive('getResponse')
            ->once()
            ->andReturn($response)
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;
        $dispatcher
            ->shouldReceive('dispatchPostEvent')
            ->once()
            ->andReturn($postEvent)
        ;

        $repository = Mockery::mock(RepositoryInterface::class);
        $repository
            ->shouldReceive('add')
            ->with($review)
            ->once()
        ;

        $flashHelper = Mockery::mock(FlashHelperInterface::class);
        $flashHelper
            ->shouldReceive('addSuccessFlash')
            ->once()
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            $repository,
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            $flashHelper,
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    public function testReturnsRedirectToResourceResponse(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->twice()
            ->andReturn(true)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
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
            ->andReturn($review)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->with('POST')
            ->once()
            ->andReturn(true)
        ;

        $response = Mockery::mock(Response::class);

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('isStopped')
            ->twice()
            ->andReturn(false)
        ;

        $postEvent = Mockery::mock(ResourceControllerEvent::class);
        $postEvent
            ->shouldReceive('getResponse')
            ->once()
            ->andReturnNull()
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchPreEvent')
            ->once()
            ->andReturn($event)
        ;
        $dispatcher
            ->shouldReceive('dispatchPostEvent')
            ->once()
            ->andReturn($postEvent)
        ;

        $repository = Mockery::mock(RepositoryInterface::class);
        $repository
            ->shouldReceive('add')
            ->with($review)
            ->once()
        ;

        $flashHelper = Mockery::mock(FlashHelperInterface::class);
        $flashHelper
            ->shouldReceive('addSuccessFlash')
            ->once()
        ;

        $redirectHandler = Mockery::mock(RedirectHandlerInterface::class);
        $redirectHandler
            ->shouldReceive('redirectToResource')
            ->once()
            ->andReturn($response)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            $repository,
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            $redirectHandler,
            $flashHelper,
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    public function testReturnsBadRequestResponse(): void
    {
        $response = Mockery::mock(Response::class);

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->once()
            ->andReturn(false)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $form = Mockery::mock(FormInterface::class);

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->once()
            ->andReturn(false)
        ;

        $viewHandler = Mockery::mock(ViewHandlerInterface::class);
        $viewHandler
            ->shouldReceive('handle')
            ->once()
            ->andReturn($response)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            $viewHandler,
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            Mockery::mock(EventDispatcherInterface::class),
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    public function testReturnsInitializeEventResponse(): void
    {
        $response = Mockery::mock(Response::class);

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->once()
            ->andReturn(true)
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $form = Mockery::mock(FormInterface::class);

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $review = Mockery::mock(ReviewInterface::class);

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->once()
            ->andReturn(false)
        ;

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('getResponse')
            ->once()
            ->andReturn($response)
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchInitializeEvent')
            ->once()
            ->andReturn($event)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $configurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCardFormIsAddedToCreateTemplate(): void
    {
        $view = Mockery::mock('alias:' . View::class);
        $view
            ->shouldReceive('create')
            ->andReturnSelf()
        ;

        $view
            ->shouldReceive('setTemplate')
            ->with('TEMPLATE')
            ->once()
            ->andReturnSelf()
        ;

        $response = Mockery::mock(Response::class);

        $configuration = Mockery::mock(RequestConfiguration::class);
        $configuration
            ->shouldReceive('isHtmlRequest')
            ->once()
            ->andReturn(true)
        ;
        $configuration
            ->shouldReceive('getTemplate')
            ->once()
            ->andReturn('TEMPLATE')
        ;

        $configurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $configurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $formView = Mockery::mock(FormView::class);

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('createView')
            ->once()
            ->andReturn($formView)
        ;

        $formFactory = Mockery::mock(ResourceFormFactoryInterface::class);
        $formFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($form)
        ;

        $product = Mockery::mock(ProductInterface::class);

        $review = Mockery::mock(ReviewInterface::class);
        $review
            ->shouldReceive('getReviewSubject')
            ->once()
            ->andReturn($product)
        ;

        $factory = Mockery::mock(NewResourceFactoryInterface::class);
        $factory
            ->shouldReceive('create')
            ->once()
            ->andReturn($review)
        ;

        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('isMethod')
            ->once()
            ->andReturn(false)
        ;

        $event = Mockery::mock(ResourceControllerEvent::class);
        $event
            ->shouldReceive('getResponse')
            ->once()
            ->andReturnNull()
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchInitializeEvent')
            ->once()
            ->andReturn($event)
        ;

        $cardFormView = Mockery::mock(FormView::class);

        $cardForm = Mockery::mock(FormInterface::class);
        $cardForm
            ->shouldReceive('createView')
            ->once()
            ->andReturn($cardFormView)
        ;

        $metadata = Mockery::mock(MetadataInterface::class);
        $metadata
            ->shouldReceive('getName')
            ->once()
            ->andReturn('NAME')
        ;

        $view
            ->shouldReceive('setData')
            ->with([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resource' => $review,
                'NAME' => $review,
                'form' => $formView,
                'cardForm' => $cardFormView
            ])
            ->once()
            ->andReturnSelf()
        ;

        $viewHandler = Mockery::mock(ViewHandlerInterface::class);
        $viewHandler
            ->shouldReceive('handle')
            ->once()
            ->andReturn($response)
        ;

        $controller = Mockery::mock(ProductReviewController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;
        $controller
            ->shouldReceive('createForm')
            ->once()
            ->andReturn($cardForm)
        ;

        $controller->__construct(
            $metadata,
            $configurationFactory,
            $viewHandler,
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            $factory,
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            Mockery::mock(ResourcesCollectionProviderInterface::class),
            $formFactory,
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        self::assertSame($response, $controller->createAction($request));
    }
}
