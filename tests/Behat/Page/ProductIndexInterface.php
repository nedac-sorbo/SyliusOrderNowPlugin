<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ProductIndexInterface extends SymfonyPageInterface
{
    public function clickTheOrderNowButton(): void;
}
