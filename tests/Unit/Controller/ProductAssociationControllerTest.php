<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Controller\ProductAssociationController;
use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProductAssociationControllerTest extends MockeryTestCase
{
    public function testCanInstantiate(): void
    {
        $controller = new ProductAssociationController(
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

    public function testAddsFormsToView(): void
    {
        $response = Mockery::mock(Response::class);

        $viewHandler = Mockery::mock(ViewHandlerInterface::class);
        $viewHandler
            ->shouldReceive('handle')
            ->once()
            ->andReturn($response)
        ;

        $metadata = Mockery::mock(MetadataInterface::class);
        $metadata
            ->shouldReceive('getName')
            ->twice()
            ->andReturn('NAME')
        ;

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

        $requestConfigurationFactory = Mockery::mock(RequestConfigurationFactoryInterface::class);
        $requestConfigurationFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($configuration)
        ;

        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
        ;

        $associatedProduct = Mockery::mock(ProductInterface::class);

        $iterator = Mockery::mock(\Iterator::class);
        $iterator->shouldReceive('rewind');
        $iterator
            ->shouldReceive('valid')
            ->twice()
            ->andReturn(true, false)
        ;

        $iterator
            ->shouldReceive('current')
            ->once()
            ->andReturn($associatedProduct)
        ;

        $iterator->shouldReceive('next');

        $collection = Mockery::mock(Collection::class);
        $collection
            ->shouldReceive('getIterator')
            ->once()
            ->andReturn($iterator)
        ;

        $resource = Mockery::mock(ProductAssociationInterface::class);
        $resource
            ->shouldReceive('getAssociatedProducts')
            ->once()
            ->andReturn($collection)
        ;

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('createView')
            ->once()
            ->andReturn(Mockery::mock(FormView::class))
        ;

        $controller = Mockery::mock(ProductAssociationController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller
            ->shouldReceive('findOr404')
            ->once()
            ->andReturn($resource)
        ;

        $controller
            ->shouldReceive('createForm')
            ->with(AddToCartType::class, null, ['product' => $associatedProduct])
            ->once()
            ->andReturn($form)
        ;

        $controller->__construct(
            $metadata,
            $requestConfigurationFactory,
            $viewHandler,
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
            $eventDispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $this->assertSame($response, $controller->showAction(Mockery::mock(Request::class)));
    }
}
