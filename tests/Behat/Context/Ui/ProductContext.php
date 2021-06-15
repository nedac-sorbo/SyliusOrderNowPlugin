<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductIndexInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\ProductShowInterface;

final class ProductContext implements Context
{
    private ProductIndexInterface $indexPage;
    private ProductShowInterface $showPage;
    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        ProductIndexInterface $indexPage,
        ProductShowInterface $showPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
        $this->notificationChecker = $notificationChecker;
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
    public function iClickTheOrderNowButtonOnTheProductShowPage(): void
    {
        $this->showPage->clickTheOrderNowButton();
    }

    /**
     * @When I add :quantity units of color :code to the cart with the order now button on the product index page
     */
    public function iAddUnitsOfColorToTheCartWithTheOrderNowButtonOnTheProductIndexPage(
        string $quantity,
        string $code
    ): void {
        $this->indexPage->addToCartUsingOrderNowButton($quantity, $code);
    }

    /**
     * @Then I should be notified that there is insufficient stock for product :productName on the product index page
     */
    public function iShouldBeNotifiedThatThereIsInsufficientStockForProductOnTheProductIndexPage(
        string $productName
    ): void {
        $this->notificationChecker->checkNotification(
            $productName . ' does not have sufficient stock.',
            NotificationType::failure()
        );
    }

    /**
     * @When I add :quantity units of color :code to the cart with the order now button on the product show page
     */
    public function iAddUnitsOfColorToTheCartWithTheOrderNowButtonOnTheProductShowPage(
        string $quantity,
        string $code
    ): void {
        $this->showPage->addToCartUsingOrderNowButton($quantity, $code);
    }

    /**
     * @Then I should be notified that there is insufficient stock for product :productName on the product show page
     */
    public function iShouldBeNotifiedThatThereIsInsufficientStockForProductOnTheProductShowPage(
        string $productName
    ): void {
        $this->notificationChecker->checkNotification(
            $productName . ' does not have sufficient stock.',
            NotificationType::failure()
        );
    }
}
