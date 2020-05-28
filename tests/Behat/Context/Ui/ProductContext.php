<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductIndexInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductShowInterface;

final class ProductContext implements Context
{
    private ProductIndexInterface $indexPage;
    private ProductShowInterface $showPage;

    public function __construct(
        ProductIndexInterface $indexPage,
        ProductShowInterface $showPage
    ) {
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
    }

    /**
     * @When I click the order now button on the product index page
     */
    public function iClickTheOrderNowButtonOnTheProductIndexPage(): void
    {
        $this->indexPage->clickTheOrderNowButton();
    }

    /**
     * @When I click the order now button on the product show page
     */
    public function iClickTheOrderNowButtonOnTheProductShowPage()
    {
        $this->showPage->clickTheOrderNowButton();
    }
}
