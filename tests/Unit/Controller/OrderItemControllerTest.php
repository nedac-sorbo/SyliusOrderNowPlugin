<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Controller\OrderItemController;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
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

    public function testAddsFormErrorFlasher(): void
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

        $this->assertSame($newResponse, $controller->addAction($request));
    }
}
