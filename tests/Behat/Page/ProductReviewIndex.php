<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

final class ProductReviewIndex extends AbstractOrderNowButtonPage implements ProductReviewIndexInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_review_index';
    }
}
