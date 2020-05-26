<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Controller;

use Sylius\Bundle\OrderBundle\Controller\OrderItemController as BaseOrderItemController;

final class OrderItemController extends BaseOrderItemController
{
    use OrderItemControllerTrait;
}
