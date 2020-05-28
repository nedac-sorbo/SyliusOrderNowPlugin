<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('nedac_sylius_order_now_plugin');
    }
}
