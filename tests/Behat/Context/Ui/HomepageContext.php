<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\HomepageInterface;

final class HomepageContext implements Context
{
    private HomepageInterface $homepage;
    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        HomepageInterface $homepage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->homepage = $homepage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I visit the homepage
     */
    public function iVisitTheHomepage(): void
    {
        $this->homepage->open();
    }

    /**
     * @When I click the order now button
     */
    public function iClickTheOrderNowButton(): void
    {
        $this->homepage->clickTheOrderNowButton();
    }

    /**
     * @When I add :quantity units of color :code to the cart with the order now button
     */
    public function iAddUnitsOfColorToTheCartWithTheOrderNowButton(string $quantity, string $code): void
    {
        $this->homepage->addToCartUsingOrderNowButton($quantity, $code);
    }

    /**
     * @Then I should be notified that there is insufficient stock for product :productName
     */
    public function iShouldBeNotifiedThatThereIsInsufficientStockForProduct(string $productName): void
    {
        $this->notificationChecker->checkNotification(
            $productName . ' does not have sufficient stock.',
            NotificationType::failure()
        );
    }
}
