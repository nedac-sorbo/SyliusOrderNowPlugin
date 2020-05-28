<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductReviewCreateInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductReviewIndexInterface;

final class ProductReviewContext implements Context
{
    private ProductReviewCreateInterface $createPage;
    private ProductReviewIndexInterface $indexPage;

    public function __construct(
        ProductReviewCreateInterface $createPage,
        ProductReviewIndexInterface $indexPage
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
    }

    /**
     * @When I click the order now button on the review create page
     */
    public function iClickTheOrderNowButtonOnTheReviewCreatePage(): void
    {
        $this->createPage->clickTheOrderNowButton();
    }

    /**
     * @When I click the order now button on the review index page
     */
    public function iClickTheOrderNowButtonOnTheReviewIndexPage(): void
    {
        $this->indexPage->clickTheOrderNowButton();
    }
}
