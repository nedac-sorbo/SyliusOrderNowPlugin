<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property MetadataInterface $metadata
 * @property RequestConfigurationFactoryInterface $requestConfigurationFactory
 * @property RepositoryInterface $repository
 * @property ResourcesCollectionProviderInterface $resourcesCollectionProvider
 * @property EventDispatcherInterface $eventDispatcher
 * @method void isGrantedOr403(RequestConfiguration $configuration, string $permission)
 * @method FormInterface createForm(string $type, $data = null, array $options = [])
 * @method ResourceInterface findOr404(RequestConfiguration $configuration)
 */
trait ProductControllerTrait
{
    public function indexAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::INDEX);

        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);
        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);

        if ($configuration->isHtmlRequest()) {
            $forms = [];
            $iterable = $resources;

            if ($resources instanceof ResourceGridView) {
                /** @var Pagerfanta<ProductInterface> $pager */
                $pager = $resources->getData();

                $iterable = $pager->getIterator();
            }

            foreach ($iterable as $product) {
                $forms[] = $this
                    ->createForm(AddToCartType::class, null, ['product' => $product])
                    ->createView()
                ;
            }

            return $this->render($configuration->getTemplate(ResourceActions::INDEX . '.html'), [
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resources' => $resources,
                $this->metadata->getPluralName() => $resources,
                'forms' => $forms
            ]);
        }

        return $this->createRestView($configuration, $resources);
    }

    public function showAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource);

        if ($configuration->isHtmlRequest()) {
            $cardForm = $this
                ->createForm(AddToCartType::class, null, ['product' => $resource])
                ->createView()
            ;

            return $this->render($configuration->getTemplate(ResourceActions::SHOW . '.html'), [
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $resource,
                $this->metadata->getName() => $resource,
                'cardForm' => $cardForm
            ]);
        }

        return $this->createRestView($configuration, $resource);
    }
}
