<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Webmozart\Assert\Assert;

final class NedacSyliusOrderNowExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration([], $container);

        Assert::isInstanceOf($configuration, ConfigurationInterface::class);

        $this->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
    }
}
