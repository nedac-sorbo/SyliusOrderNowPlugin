<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Service;

use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;

final class NotificationChecker implements NotificationCheckerInterface
{
    private const TYPE_CLASS_MAP = [
        'failure' => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info'
    ];

    private NotificationAccessorInterface $notificationAccessor;

    public function __construct(NotificationAccessorInterface $notificationAccessor)
    {
        $this->notificationAccessor = $notificationAccessor;
    }

    public function checkNotification(string $message, NotificationType $type): void
    {
        foreach ($this->notificationAccessor->getMessageElements() as $messageElement) {
            if (
                false !== strpos($messageElement->getText(), $message) &&
                $messageElement->hasClass(self::TYPE_CLASS_MAP[(string) $type])
            ) {
                return;
            }
        }

        throw new NotificationExpectationMismatchException($type, $message);
    }
}
