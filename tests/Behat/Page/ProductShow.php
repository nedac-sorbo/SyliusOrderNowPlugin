<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

final class ProductShow extends AbstractOrderNowButtonPage implements ProductShowInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_show';
    }
}
