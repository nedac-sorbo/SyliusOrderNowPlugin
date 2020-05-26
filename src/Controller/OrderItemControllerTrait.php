<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property MetadataInterface $metadata
 * @property RequestConfigurationFactoryInterface $requestConfigurationFactory
 * @property RedirectHandlerInterface $redirectHandler
 * @method Response redirectToReferer(RequestConfiguration $configuration)
 */
trait OrderItemControllerTrait
{
    public function addAction(Request $request): Response
    {
        $response = parent::addAction($request);

        // TODO: Only do this when form is submitted from product card, not from the product show page
        if ($response instanceof RedirectResponse) {
            $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
            $response = $this->redirectHandler->redirectToReferer($configuration);
        }

        return $response;
    }
}
