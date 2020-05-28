<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Nedac\SyliusOrderNowPlugin\DependencyInjection\NedacSyliusOrderNowExtension;

final class NedacSyliusOrderNowExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [new NedacSyliusOrderNowExtension()];
    }

    public function testControllerIsLoaded(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('Nedac\SyliusOrderNowPlugin\Controller\OrderItemController');
    }

    private const LISTENERS = [
        'nedac.sylius_order_now_plugin.shop.block_event_listener.stylesheets' =>
            'sonata.block.event.sylius.shop.layout.stylesheets',
        'nedac.sylius_order_now_plugin.shop.block_event_listener.javascripts' =>
            'sonata.block.event.sylius.shop.layout.javascripts'
    ];

    public function testEventListenersAreLoaded(): void
    {
        $this->load();

        foreach (self::LISTENERS as $id => $event) {
            $this->assertContainerBuilderHasServiceDefinitionWithTag(
                $id,
                'kernel.event_listener',
                ['event' => $event, 'method' => 'onBlockEvent']
            );
        }
    }

    public function testFormTypeIsloaded(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'nedac.sylius_order_now_plugin.form.type.add_to_cart',
            'form.type'
        );
    }
}
