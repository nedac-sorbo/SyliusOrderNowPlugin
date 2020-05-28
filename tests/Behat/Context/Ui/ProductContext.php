<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductIndexInterface;

final class ProductContext implements Context
{
    private ProductIndexInterface $indexPage;

    public function __construct(ProductIndexInterface $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @When I click the order now button on the product index page
     */
    public function iClickTheOrderNowButtonOnTheProductIndexPage(): void
    {
        $this->indexPage->clickTheOrderNowButton();
    }
}
