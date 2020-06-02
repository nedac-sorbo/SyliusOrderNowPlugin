<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductReviewCreateInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductReviewIndexInterface;

final class ProductReviewContext implements Context
{
    private ProductReviewCreateInterface $createPage;
    private ProductReviewIndexInterface $indexPage;
    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        ProductReviewCreateInterface $createPage,
        ProductReviewIndexInterface $indexPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->notificationChecker = $notificationChecker;
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

    /**
     * @When I add :quantity units of color :code to the cart with the order now button on the review create page
     */
    public function iAddUnitsOfColorToTheCartWithTheOrderNowButtonOnTheReviewCreatePage(
        string $quantity,
        string $code
    ): void {
        $this->createPage->addToCartUsingOrderNowButton($quantity, $code);
    }

    /**
     * @Then I should be notified that there is insufficient stock for product :productName on the review page
     */
    public function iShouldBeNotifiedThatThereIsInsufficientStockForProductOnTheReviewPage(
        string $productName
    ): void {
        $this->notificationChecker->checkNotification(
            $productName . ' does not have sufficient stock.',
            NotificationType::failure()
        );
    }

    /**
     * @When I add :quantity units of color :code to the cart with the order now button on the review index page
     */
    public function iAddUnitsOfColorToTheCartWithTheOrderNowButtonOnTheReviewIndexPage(
        string $quantity,
        string $code
    ): void {
        $this->indexPage->addToCartUsingOrderNowButton($quantity, $code);
    }
}
