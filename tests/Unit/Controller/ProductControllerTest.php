<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class ProductControllerTest extends MockeryTestCase
{
    public function testCanInstantiate(): void
    {
        $controller = new ProductController(
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

    public function testAddsFormsToIndexTemplate(): void
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

        $view
            ->shouldReceive('setTemplateVar')
            ->once()
            ->with('PLURAL')
            ->andReturnSelf()
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
            ->twice()
            ->andReturn('PLURAL')
        ;

        $view
            ->shouldReceive('setData')
            ->with([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resources' => $resources,
                'PLURAL' => $resources,
                'forms' => [$formView]
            ])
            ->once()
            ->andReturnSelf()
        ;

        $response = Mockery::mock(Response::class);

        $viewHandler = Mockery::mock(ViewHandlerInterface::class);
        $viewHandler
            ->shouldReceive('handle')
            ->once()
            ->andReturn($response)
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
            $viewHandler,
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

        $this->assertSame($response, $controller->indexAction(Mockery::mock(Request::class)));
    }

    public function testAddsFormToShowTemplate(): void
    {
        $this->markTestIncomplete('TODO');
    }
}
