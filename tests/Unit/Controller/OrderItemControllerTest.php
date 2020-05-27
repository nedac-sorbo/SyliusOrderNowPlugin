<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Controller\OrderItemController;
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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderItemControllerTest extends MockeryTestCase
{
    public function testCanInstantiate(): void
    {
        $controller = new OrderItemController(
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

        $this->assertInstanceOf(ResourceController::class, $controller);
    }

    public function  testRedirectsToReferer(): void
    {
        $configuration = Mockery::mock(RequestConfiguration::class);

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

        $controller = Mockery::mock(OrderItemController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('getParentAddActionResponse')
            ->andReturn(Mockery::mock(RedirectResponse::class))
        ;

        $controller->__construct(
            Mockery::mock(MetadataInterface::class),
            $requestConfigurationFactory,
            Mockery::mock(ViewHandlerInterface::class),
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            Mockery::mock(NewResourceFactoryInterface::class),
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

        $this->assertSame($newResponse, $controller->addAction($request));
    }
}
