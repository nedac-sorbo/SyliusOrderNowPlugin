<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

final class Homepage extends SymfonyPage implements HomepageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_homepage';
    }

    public function clickTheOrderNowButton(): void
    {
        $this->getDriver()->evaluateScript(<<<JS
document.getElementsByClassName('nedac-order-now-button-container')[0].click();
JS
        );
    }
}
