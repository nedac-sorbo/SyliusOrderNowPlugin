<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use FOS\RestBundle\View\View;
use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
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
 * @property ViewHandlerInterface $viewHandler
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

        $view = View::create($resources);

        if ($configuration->isHtmlRequest()) {
            $forms = [];
            $iterable = $resources;

            if ($resources instanceof ResourceGridView) {
                /** @var Pagerfanta $pager */
                $pager = $resources->getData();

                $iterable = $pager->getIterator();
            }

            foreach ($iterable as $product) {
                $forms[] = $this
                    ->createForm(AddToCartType::class, null, ['product' => $product])
                    ->createView()
                ;
            }

            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resources' => $resources,
                    $this->metadata->getPluralName() => $resources,
                    'forms' => $forms
                ])
            ;
        }

        return $this->viewHandler->handle($configuration, $view);
    }
}
