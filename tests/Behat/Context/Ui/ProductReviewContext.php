<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductReviewCreateInterface;

final class ProductReviewContext implements Context
{
    private ProductReviewCreateInterface $createPage;

    public function __construct(ProductReviewCreateInterface $createPage)
    {
        $this->createPage = $createPage;
    }

    /**
     * @When I click the order now button on the review create page
     */
    public function iClickTheOrderNowButtonOnTheReviewCreatePage(): void
    {
        $this->createPage->clickTheOrderNowButton();
    }
}
