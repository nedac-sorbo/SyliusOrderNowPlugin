<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface CartSummaryInterface extends SymfonyPageInterface
{
    public function clickTheOrderNowButton(): void;
}
