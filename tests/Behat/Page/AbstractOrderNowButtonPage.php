<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

abstract class AbstractOrderNowButtonPage extends SymfonyPage
{
    public function clickTheOrderNowButton(): void
    {
        $this->getDriver()->evaluateScript(<<<JS
document.getElementsByClassName('nedac-order-now-button-container')[0].click();
JS
        );
    }
}
