<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\CartSummaryInterface;

final class CartContext implements Context
{
    private CartSummaryInterface $page;
    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        CartSummaryInterface $page,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->page = $page;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I click the order now button on the cart summary page
     */
    public function iClickTheOrderNowButtonOnTheCartSummaryPage(): void
    {
        $this->page->clickTheOrderNowButton();
    }

    /**
     * @When I add :quantity units of color :code to the cart with the order now button on the cart summary page
     */
    public function iAddUnitsOfColorToTheCartWithTheOrderNowButtonOnTheCartSummaryPage(
        string $quantity,
        string $code
    ): void {
        $this->page->addToCartUsingOrderNowButton($quantity, $code);
    }

    /**
     * @Then I should be notified that there is insufficient stock for product :productName on the cart summary page
     */
    public function iShouldBeNotifiedThatThereIsInsufficientStockForProductOnTheCartSummaryPage(
        string $productName
    ): void {
        $this->notificationChecker->checkNotification(
            $productName . ' does not have sufficient stock.',
            NotificationType::failure()
        );
    }
}
