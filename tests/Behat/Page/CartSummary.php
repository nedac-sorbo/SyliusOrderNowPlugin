<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

final class CartSummary extends AbstractOrderNowButtonPage implements CartSummaryInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }
}
