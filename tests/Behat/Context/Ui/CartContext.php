<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\CartSummaryInterface;

final class CartContext implements Context
{
    private CartSummaryInterface $page;

    public function __construct(CartSummaryInterface $page)
    {
        $this->page = $page;
    }

    /**
     * @When I click the order now button on the cart summary page
     */
    public function iClickTheOrderNowButtonOnTheCartSummaryPage(): void
    {
        $this->page->clickTheOrderNowButton();
    }
}
