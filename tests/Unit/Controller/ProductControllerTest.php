<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Controller\ProductController;
use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
use Pagerfanta\Pagerfanta;
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
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class ProductControllerTest extends MockeryTestCase
{
    public function testCanInstantiate(): void
    {
        self::expectNotToPerformAssertions();

        new ProductController(
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

    public function testAddsFormsToIndexTemplate(): void
    {
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

        $product = Mockery::mock(ProductInterface::class);

        $iterator = Mockery::mock(\ArrayIterator::class);
        $iterator->shouldReceive('rewind');

        $iterator
            ->shouldReceive('valid')
            ->twice()
            ->andReturn(true, false)
        ;

        $iterator
            ->shouldReceive('current')
            ->once()
            ->andReturn($product)
        ;

        $iterator
            ->shouldReceive('next')
            ->once()
        ;

        $pager = Mockery::mock(Pagerfanta::class);
        $pager
            ->shouldReceive('getIterator')
            ->once()
            ->andReturn($iterator)
        ;

        $resources = Mockery::mock(ResourceGridView::class);
        $resources
            ->shouldReceive('getData')
            ->once()
            ->andReturn($pager)
        ;

        $provider = Mockery::mock(ResourcesCollectionProviderInterface::class);
        $provider
            ->shouldReceive('get')
            ->once()
            ->andReturn($resources)
        ;

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatchMultiple')
            ->once()
        ;

        $formView = Mockery::mock(FormView::class);

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('createView')
            ->once()
            ->andReturn($formView)
        ;

        $metadata = Mockery::mock(MetadataInterface::class);
        $metadata
            ->shouldReceive('getPluralName')
            ->once()
            ->andReturn('PLURAL')
        ;

        $container = Mockery::mock(ContainerInterface::class);
        $container
            ->shouldReceive('has')
            ->with('templating')
            ->once()
            ->andReturnFalse()
        ;

        $container
            ->shouldReceive('has')
            ->with('twig')
            ->once()
            ->andReturn(Mockery::mock(Environment::class))
        ;

        $container
            ->shouldReceive('get->render')
            ->once()
            ->andReturn('')
        ;

        $controller = Mockery::mock(ProductController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller
            ->shouldReceive('createForm')
            ->with(AddToCartType::class, null, ['product' => $product])
            ->once()
            ->andReturn($form)
        ;

        $controller->__construct(
            $metadata,
            $requestConfigurationFactory,
            null,
            Mockery::mock(RepositoryInterface::class),
            Mockery::mock(FactoryInterface::class),
            Mockery::mock(NewResourceFactoryInterface::class),
            Mockery::mock(ObjectManager::class),
            Mockery::mock(SingleResourceProviderInterface::class),
            $provider,
            Mockery::mock(ResourceFormFactoryInterface::class),
            Mockery::mock(RedirectHandlerInterface::class),
            Mockery::mock(FlashHelperInterface::class),
            Mockery::mock(AuthorizationCheckerInterface::class),
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $controller->setContainer($container);

        $controller->indexAction(Mockery::mock(Request::class));
    }

    public function testAddsFormToShowTemplate(): void
    {
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

        $metadata = Mockery::mock(MetadataInterface::class);
        $metadata
            ->shouldReceive('getName')
            ->once()
            ->andReturn('NAME')
        ;

        $product = Mockery::mock(ProductInterface::class);

        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher
            ->shouldReceive('dispatch')
            ->once()
        ;

        $formView = Mockery::mock(FormView::class);

        $form = Mockery::mock(FormInterface::class);
        $form
            ->shouldReceive('createView')
            ->once()
            ->andReturn($formView)
        ;

        $twig = Mockery::mock(Environment::class);
        $twig
            ->shouldReceive('render')
            ->withArgs(function (string $name, array $options): bool {
                return isset($options['cardForm']);
            })
            ->once()
            ->andReturn('')
        ;

        $container = Mockery::mock(ContainerInterface::class);
        $container
            ->shouldReceive('has')
            ->with('templating')
            ->once()
            ->andReturnFalse()
        ;

        $container
            ->shouldReceive('has')
            ->with('twig')
            ->once()
            ->andReturn(Mockery::mock(Environment::class))
        ;

        $container
            ->shouldReceive('get')
            ->with('twig')
            ->once()
            ->andReturn($twig)
        ;

        $controller = Mockery::mock(ProductController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('isGrantedOr403')
            ->once()
        ;

        $controller
            ->shouldReceive('findOr404')
            ->once()
            ->andReturn($product)
        ;

        $controller
            ->shouldReceive('createForm')
            ->with(AddToCartType::class, null, ['product' => $product])
            ->once()
            ->andReturn($form)
        ;

        $controller->__construct(
            $metadata,
            $requestConfigurationFactory,
            null,
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
            $dispatcher,
            Mockery::mock(StateMachineInterface::class),
            Mockery::mock(ResourceUpdateHandlerInterface::class),
            Mockery::mock(ResourceDeleteHandlerInterface::class)
        );

        $controller->setContainer($container);

        $controller->showAction(Mockery::mock(Request::class));
    }
}
