<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusOrderNowPlugin\Behat\Page\HomepageInterface;

final class HomepageContext implements Context
{
    private HomepageInterface $homepage;

    public function __construct(HomepageInterface $homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * @When I click the order now button
     */
    public function iClickTheOrderNowButton(): void
    {
        $this->homepage->clickTheOrderNowButton();
    }
}
