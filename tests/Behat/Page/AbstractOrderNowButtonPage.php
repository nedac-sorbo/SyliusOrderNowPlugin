<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Page;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

abstract class AbstractOrderNowButtonPage extends SymfonyPage
{
    /**
     * @throws DriverException
     * @throws UnsupportedDriverActionException
     */
    public function clickTheOrderNowButton(): void
    {
        $this->getDriver()->evaluateScript(<<<JS
document.getElementsByClassName('nedac-order-now-button-container')[0].click();
JS
        );
    }

    /**
     * @param string $quantity
     * @param string $code
     * @throws DriverException
     * @throws UnsupportedDriverActionException
     * @throws ElementNotFoundException
     */
    public function addToCartUsingOrderNowButton(string $quantity, string $code): void
    {
        $document = $this->getDocument();

        // Set quantity
        $input = $document->find(
            'css',
            '.nedac-sylius-order-now-plugin-number-input'
        );
        $input->setValue($quantity);

        // Set option
        $document->selectFieldOption(
            'nedac_sylius_order_now_plugin_add_to_cart[cartItem][variant][Color]',
            $code
        );

        $this->clickTheOrderNowButton();
    }
}
