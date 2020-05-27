<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use FOS\RestBundle\View\View;
use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property MetadataInterface $metadata
 * @property RequestConfigurationFactoryInterface $requestConfigurationFactory
 * @property ViewHandlerInterface $viewHandler
 * @property EventDispatcherInterface $eventDispatcher
 * @method void isGrantedOr403(RequestConfiguration $configuration, string $permission)
 * @method FormInterface createForm(string $type, $data = null, array $options = [])
 * @method ResourceInterface findOr404(RequestConfiguration $configuration)
 */
trait ProductAssociationControllerTrait
{
    public function showAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);

        /** @var ProductAssociationInterface $resource */
        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource);

        $view = View::create($resource);

        if ($configuration->isHtmlRequest()) {
            $forms = [];
            $products = $resource->getAssociatedProducts();
            foreach ($products as $product) {
                $forms[] = $this
                    ->createForm(AddToCartType::class, null, ['product' => $product])
                    ->createView()
                ;
            }

            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
                ->setTemplateVar($this->metadata->getName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resource' => $resource,
                    $this->metadata->getName() => $resource,
                    'forms' => $forms
                ])
            ;
        }

        return $this->viewHandler->handle($configuration, $view);
    }
}
