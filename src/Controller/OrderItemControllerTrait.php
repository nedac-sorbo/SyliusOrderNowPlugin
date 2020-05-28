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
    /**
     * @codeCoverageIgnore
     * @param Request $request
     * @return Response
     */
    protected function getParentAddActionResponse(Request $request): Response
    {
        return parent::addAction($request);
    }

    public function addAction(Request $request): Response
    {
        $response = $this->getParentAddActionResponse($request);

        if ($response instanceof RedirectResponse) {
            $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
            $response = $this->redirectHandler->redirectToReferer($configuration);
        }

        return $response;
    }
}
