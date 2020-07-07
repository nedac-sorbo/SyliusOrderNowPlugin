<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

abstract class AbstractOrderNowButtonPage extends SymfonyPage
{
    /**
     * @throws DriverException
     * @throws UnsupportedDriverActionException
     */
    public function clickTheOrderNowButton(): void
    {
        $this->getDriver()->evaluateScript(<<<JS
document.querySelector('[data-test-order-now-button-container]').click();
JS
        );
    }

    /**
     * @param string $quantity
     * @param string $code
     * @throws DriverException
     * @throws ElementNotFoundException
     * @throws UnsupportedDriverActionException
     */
    public function addToCartUsingOrderNowButton(string $quantity, string $code): void
    {
        $document = $this->getDocument();

        // Set quantity
        $input = $document->find(
            'xpath',
            'descendant::*[@data-test-order-now-number-input]'
        );

        Assert::notNull($input);

        $input->setValue($quantity);

        // Set option
        $select = $document->find(
            'xpath',
            'descendant::*[@data-test-order-now-dropdown]'
        );

        Assert::notNull($select);

        $select->selectOption($code);

        $this->clickTheOrderNowButton();
    }
}
