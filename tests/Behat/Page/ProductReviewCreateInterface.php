<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ProductReviewCreateInterface extends SymfonyPageInterface
{
    public function clickTheOrderNowButton(): void;
    public function addToCartUsingOrderNowButton(string $quantity, string $code): void;
}
