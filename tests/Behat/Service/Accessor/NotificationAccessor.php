<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Service\Accessor;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;

final class NotificationAccessor implements NotificationAccessorInterface
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return array<int, NodeElement>
     * @throws ElementNotFoundException
     */
    public function getMessageElements(): array
    {
        $messageElements = $this->session->getPage()->findAll(
            'xpath',
            'descendant::*[@data-test-flash-message]'
        );

        if (empty($messageElements)) {
            throw new ElementNotFoundException(
                $this->session->getDriver(),
                'message element',
                'xpath',
                'descendant::*[@data-test-flash-message]'
            );
        }

        return $messageElements;
    }
}
