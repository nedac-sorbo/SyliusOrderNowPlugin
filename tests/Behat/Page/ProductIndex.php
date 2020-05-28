<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

final class ProductIndex extends AbstractOrderNowButtonPage implements ProductIndexInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_index';
    }
}
