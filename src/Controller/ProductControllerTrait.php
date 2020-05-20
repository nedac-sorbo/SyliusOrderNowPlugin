<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
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
 *
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
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resources' => $resources,
                    $this->metadata->getPluralName() => $resources,
                ])
            ;
        }

        return $this->viewHandler->handle($configuration, $view);
    }
}
